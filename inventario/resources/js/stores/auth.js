import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import client from '../api/client.js'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(localStorage.getItem('auth_token') || null)
    const isAuthenticated = computed(() => !!token.value)

    const login = async (username, password) => {
        try {
            const response = await client.post('/login', { username, password })
            token.value = response.data.token
            user.value = response.data.user
            localStorage.setItem('auth_token', token.value)
            return { success: true, user: response.data.user }
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Error al iniciar sesión' }
        }
    }

    const logout = () => {
        token.value = null
        user.value = null
        localStorage.removeItem('auth_token')
    }

    const isAdmin = computed(() => user.value?.rol === 'Admin')
    const isTecnico = computed(() => user.value?.rol === 'Tecnico')

    return {
        user,
        token,
        isAuthenticated,
        isAdmin,
        isTecnico,
        login,
        logout,
    }
})
