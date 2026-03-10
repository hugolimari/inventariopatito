import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '../api/client.js'

export const useInventarioStore = defineStore('inventario', () => {
        // Catalogo
        const crear_catalogo = async (data) => {
            try {
                const response = await client.post('/catalogos', data)
                catalogo.value.push(response.data.data || response.data)
                return { success: true, data: response.data }
            } catch (err) {
                error.value = err.response?.data?.message || err.message
                return { success: false, error: error.value }
            }
        }
    // Estado
    const catalogo = ref([])
    const activosFijos = ref([])
    const consumibles = ref([])
    const movimientos = ref([])
    const loading = ref(false)
    const error = ref(null)

    // Catálogo
    const fetchCatalogo = async () => {
        loading.value = true
        try {
            const response = await client.get('/catalogos')
            catalogo.value = response.data.data || response.data
            error.value = null
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    // Activos Fijos
    const fetchActivosFijos = async () => {
        loading.value = true
        try {
            const response = await client.get('/activos-fijos')
            activosFijos.value = response.data.data || response.data
            error.value = null
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const crear_activoFijo = async (data) => {
        try {
            const response = await client.post('/activos-fijos', data)
            activosFijos.value.push(response.data.data || response.data)
            return { success: true, data: response.data }
        } catch (err) {
            error.value = err.response?.data?.message || err.message
            return { success: false, error: error.value }
        }
    }

    const actualizar_activoFijo = async (id, data) => {
        try {
            const response = await client.put(`/activos-fijos/${id}`, data)
            const index = activosFijos.value.findIndex(a => a.id === id)
            if (index !== -1) {
                activosFijos.value[index] = response.data.data || response.data
            }
            return { success: true, data: response.data }
        } catch (err) {
            error.value = err.response?.data?.message || err.message
            return { success: false, error: error.value }
        }
    }

    // Consumibles
    const fetchConsumibles = async () => {
        loading.value = true
        try {
            const response = await client.get('/lotes-consumibles')
            consumibles.value = response.data.data || response.data
            error.value = null
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const crear_consumible = async (data) => {
        try {
            const response = await client.post('/lotes-consumibles', data)
            consumibles.value.push(response.data.data || response.data)
            return { success: true, data: response.data }
        } catch (err) {
            error.value = err.response?.data?.message || err.message
            return { success: false, error: error.value }
        }
    }

    const actualizar_consumible = async (id, data) => {
        try {
            const response = await client.put(`/lotes-consumibles/${id}`, data)
            const index = consumibles.value.findIndex(c => c.id === id)
            if (index !== -1) {
                consumibles.value[index] = response.data.data || response.data
            }
            return { success: true, data: response.data }
        } catch (err) {
            error.value = err.response?.data?.message || err.message
            return { success: false, error: error.value }
        }
    }

    // Movimientos Kardex
    const fetchMovimientos = async () => {
        loading.value = true
        try {
            const response = await client.get('/kardex-movimientos')
            movimientos.value = response.data.data || response.data
            error.value = null
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const registrar_movimiento = async (data) => {
        try {
            const response = await client.post('/kardex-movimientos', data)
            movimientos.value.unshift(response.data.data || response.data)
            return { success: true, data: response.data }
        } catch (err) {
            error.value = err.response?.data?.message || err.message
            return { success: false, error: error.value }
        }
    }

    return {
        // Estado
        catalogo,
        activosFijos,
        consumibles,
        movimientos,
        loading,
        error,
        // Catálogo
        fetchCatalogo,
        crear_catalogo,
        // Activos Fijos
        fetchActivosFijos,
        crear_activoFijo,
        actualizar_activoFijo,
        // Consumibles
        fetchConsumibles,
        crear_consumible,
        actualizar_consumible,
        // Movimientos
        fetchMovimientos,
        registrar_movimiento,
    }
});
