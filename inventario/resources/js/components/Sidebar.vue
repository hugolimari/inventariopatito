<template>
  <aside class="w-72 bg-gradient-to-b from-slate-800 via-slate-900 to-black text-white min-h-screen shadow-2xl fixed left-0 top-0 pt-24 border-r border-slate-700">
    <nav class="px-4 py-6 space-y-2">
      <!-- Main Menu -->
      <div class="mb-6">
        <p class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">📍 Principal</p>
        
        <router-link
          to="/"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 font-medium"
          :class="{ 
            'bg-blue-600 text-white shadow-lg': isActive('/'),
            'text-slate-300 hover:bg-slate-700': !isActive('/')
          }"
        >
          <span class="text-lg">📊</span>
          <span>Dashboard</span>
        </router-link>

        <router-link
          to="/catalogo"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 font-medium"
          :class="{ 
            'bg-blue-600 text-white shadow-lg': isActive('/catalogo'),
            'text-slate-300 hover:bg-slate-700': !isActive('/catalogo')
          }"
        >
          <span class="text-lg">📑</span>
          <span>Catálogo</span>
        </router-link>
      </div>

      <!-- Admin Section -->
      <div v-if="isAdminUser" class="border-t border-slate-700 pt-4">
        <p class="px-4 py-2 text-xs font-bold text-red-400 uppercase tracking-wider mb-3">👑 Administración</p>
        <router-link
          to="/activos-fijos"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 font-medium"
          :class="{ 
            'bg-blue-600 text-white shadow-lg': isActive('/activos-fijos'),
            'text-slate-300 hover:bg-slate-700': !isActive('/activos-fijos')
          }"
        >
          <span class="text-lg">🖥️</span>
          <span>Activos Fijos</span>
        </router-link>
        <router-link
          to="/consumibles"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 font-medium"
          :class="{ 
            'bg-blue-600 text-white shadow-lg': isActive('/consumibles'),
            'text-slate-300 hover:bg-slate-700': !isActive('/consumibles')
          }"
        >
          <span class="text-lg">🔧</span>
          <span>Consumibles</span>
        </router-link>
        <router-link
          to="/usuarios"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 font-medium"
          :class="{ 
            'bg-blue-600 text-white shadow-lg': isActive('/usuarios'),
            'text-slate-300 hover:bg-slate-700': !isActive('/usuarios')
          }"
        >
          <span class="text-lg">👤</span>
          <span>Usuarios</span>
        </router-link>
      </div>

      <!-- Kardex Section -->
      <div class="border-t border-slate-700 pt-4">
        <p class="px-4 py-2 text-xs font-bold text-green-400 uppercase tracking-wider mb-3">📋 Historial</p>
        
        <router-link
          to="/movimientos"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 font-medium"
          :class="{ 
            'bg-blue-600 text-white shadow-lg': isActive('/movimientos'),
            'text-slate-300 hover:bg-slate-700': !isActive('/movimientos')
          }"
        >
          <span class="text-lg">📋</span>
          <span>Kardex</span>
        </router-link>
      </div>
    </nav>

    <!-- Bottom Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700 bg-gradient-to-t from-slate-900 to-transparent">
      <div class="text-center text-xs text-slate-500">
        <p class="font-semibold mb-1">Sistema de Inventario</p>
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
