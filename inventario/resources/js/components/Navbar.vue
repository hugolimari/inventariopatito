<template>
  <nav class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white shadow-xl sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4">
      <div class="flex justify-between items-center">
        <!-- Logo and title -->
        <div class="flex items-center gap-3">
          <div class="text-3xl">📦</div>
          <div>
            <h1 class="text-xl font-bold">Inventario</h1>
            <p class="text-xs text-blue-100">Gestión de Hardware</p>
          </div>
        </div>

        <!-- User info and logout -->
        <div class="flex items-center gap-6">
          <div class="flex items-center gap-3">
            <div class="text-right">
              <p class="text-sm font-semibold">{{ auth.user?.nombre_completo || 'Usuario' }}</p>
              <p class="text-xs text-blue-100">{{ auth.user?.rol }}</p>
            </div>
            <div>
              <span 
                v-if="auth.user?.rol === 'Admin'" 
                class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-full text-xs font-bold transition flex items-center gap-1"
              >
                👑 Admin
              </span>
              <span 
                v-else 
                class="bg-amber-500 hover:bg-amber-600 px-3 py-1 rounded-full text-xs font-bold transition flex items-center gap-1"
              >
                🔧 Técnico
              </span>
            </div>
          </div>
          
          <!-- Logout button -->
          <button
            @click="handleLogout"
            class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 flex items-center gap-2 shadow-md hover:shadow-lg"
          >
            🚪 Salir
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const router = useRouter()
const auth = useAuthStore()

const handleLogout = () => {
  auth.logout()
  router.push('/login')
}
</script>
