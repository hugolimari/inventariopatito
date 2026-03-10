<template>
  <nav class="bg-gray-900/80 backdrop-blur-xl text-gray-100 border-b border-gray-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4">
      <div class="flex justify-between items-center">
        <!-- Logo and title -->
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-xl flex items-center justify-center text-xl shadow-lg shadow-cyan-500/20">📦</div>
          <div>
            <h1 class="text-lg font-bold text-gray-100">Inventario</h1>
            <p class="text-xs text-gray-500">Gestión de Hardware</p>
          </div>
        </div>

        <!-- User info and logout -->
        <div class="flex items-center gap-5">
          <div class="flex items-center gap-3">
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-200">{{ auth.user?.nombre_completo || 'Usuario' }}</p>
              <p class="text-xs text-gray-500">{{ auth.user?.rol }}</p>
            </div>
            <div>
              <span 
                v-if="auth.user?.rol === 'Admin'" 
                class="bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5"
              >
                👑 Admin
              </span>
              <span 
                v-else 
                class="bg-amber-500/15 text-amber-400 border border-amber-500/30 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5"
              >
                🔧 Técnico
              </span>
            </div>
          </div>
          
          <!-- Logout button -->
          <button
            @click="handleLogout"
            class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 hover:border-rose-500/40 font-medium px-4 py-2 rounded-lg transition duration-200 flex items-center gap-2 text-sm"
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
