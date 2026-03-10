<template>
  <aside class="w-64 bg-gray-950 text-gray-400 min-h-screen fixed left-0 top-0 pt-[73px] border-r border-gray-800">
    <nav class="px-3 py-6 space-y-1">
      <!-- Main Menu -->
      <div class="mb-6">
        <p class="px-4 py-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-2">Principal</p>
        
        <router-link
          to="/"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition duration-200 text-sm font-medium"
          :class="{ 
            'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500 -ml-px': isActive('/'),
            'text-gray-400 hover:bg-gray-800/50 hover:text-gray-200': !isActive('/')
          }"
        >
          <span class="text-base">📊</span>
          <span>Dashboard</span>
        </router-link>

        <router-link
          to="/catalogo"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition duration-200 text-sm font-medium"
          :class="{ 
            'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500 -ml-px': isActive('/catalogo'),
            'text-gray-400 hover:bg-gray-800/50 hover:text-gray-200': !isActive('/catalogo')
          }"
        >
          <span class="text-base">📑</span>
          <span>Catálogo</span>
        </router-link>
      </div>

      <!-- Admin Section -->
      <div v-if="isAdminUser" class="border-t border-gray-800/60 pt-4 mb-6">
        <p class="px-4 py-2 text-[10px] font-bold text-rose-500/70 uppercase tracking-widest mb-2">Administración</p>
        <router-link
          to="/activos-fijos"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition duration-200 text-sm font-medium"
          :class="{ 
            'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500 -ml-px': isActive('/activos-fijos'),
            'text-gray-400 hover:bg-gray-800/50 hover:text-gray-200': !isActive('/activos-fijos')
          }"
        >
          <span class="text-base">🖥️</span>
          <span>Activos Fijos</span>
        </router-link>
        <router-link
          to="/consumibles"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition duration-200 text-sm font-medium"
          :class="{ 
            'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500 -ml-px': isActive('/consumibles'),
            'text-gray-400 hover:bg-gray-800/50 hover:text-gray-200': !isActive('/consumibles')
          }"
        >
          <span class="text-base">🔧</span>
          <span>Consumibles</span>
        </router-link>
        <router-link
          to="/usuarios"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition duration-200 text-sm font-medium"
          :class="{ 
            'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500 -ml-px': isActive('/usuarios'),
            'text-gray-400 hover:bg-gray-800/50 hover:text-gray-200': !isActive('/usuarios')
          }"
        >
          <span class="text-base">👤</span>
          <span>Usuarios</span>
        </router-link>
      </div>

      <!-- Kardex Section -->
      <div class="border-t border-gray-800/60 pt-4">
        <p class="px-4 py-2 text-[10px] font-bold text-emerald-500/70 uppercase tracking-widest mb-2">Historial</p>
        
        <router-link
          to="/movimientos"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition duration-200 text-sm font-medium"
          :class="{ 
            'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500 -ml-px': isActive('/movimientos'),
            'text-gray-400 hover:bg-gray-800/50 hover:text-gray-200': !isActive('/movimientos')
          }"
        >
          <span class="text-base">📋</span>
          <span>Kardex</span>
        </router-link>
      </div>
    </nav>

    <!-- Bottom Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800/60">
      <div class="text-center text-[11px] text-gray-600">
        <p class="font-medium">Sistema de Inventario</p>
        <p>v1.0</p>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import { computed } from 'vue'

const route = useRoute()
const auth = useAuthStore()

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const isAdminUser = computed(() => {
  return auth.user?.rol === 'Admin' || auth.isAdmin
})
</script>
