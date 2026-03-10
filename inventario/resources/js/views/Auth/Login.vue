<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 flex items-center justify-center px-4 py-12">
    <!-- Background decoration -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse animation-delay-2000"></div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header with gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-8 py-8 text-white text-center">
          <div class="text-5xl mb-4">📦</div>
          <h1 class="text-3xl font-bold">Inventario</h1>
          <p class="text-blue-100 text-sm mt-2">Sistema de Gestión de Hardware</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="p-8 space-y-6">
          <!-- Username Input -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
              <span class="inline-flex items-center gap-2">
                👤 Usuario
              </span>
            </label>
            <input
              v-model="form.username"
              type="text"
              placeholder="admin"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-200 font-medium transition"
              required
            />
            <p v-if="errors.username" class="text-red-600 text-sm font-medium">❌ {{ errors.username }}</p>
          </div>

          <!-- Password Input -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
              <span class="inline-flex items-center gap-2">
                🔒 Contraseña
              </span>
            </label>
            <input
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-200 font-medium transition"
              required
            />
            <p v-if="errors.password" class="text-red-600 text-sm font-medium">❌ {{ errors.password }}</p>
          </div>

          <!-- Error Alert -->
          <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
          >
            <div v-if="error" class="bg-red-50 border-2 border-red-300 text-red-800 px-5 py-4 rounded-lg">
              <p class="flex items-center gap-2 text-sm font-semibold">
                <span>⚠️</span>
                {{ error }}
              </p>
            </div>
          </transition>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
          >
            <span v-if="!loading">🚀 Iniciar Sesión</span>
            <span v-else class="flex items-center gap-2">
              <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Iniciando sesión...
            </span>
          </button>
        </form>

        <!-- Divider -->
        <div class="px-8 py-4 border-t border-gray-200"></div>

        <!-- Demo Credentials -->
        <div class="px-8 pb-8">
          <p class="text-center text-sm font-semibold text-gray-700 mb-4">📋 Usuarios de Prueba</p>
          
          <div class="space-y-3">
            <!-- Admin Card -->
            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 hover:border-blue-400 transition">
              <p class="font-bold text-gray-800 text-sm mb-2">👨‍💼 Admin</p>
              <div class="space-y-1 text-xs text-gray-700">
                <p class="flex items-center gap-2">
                  <span class="font-semibold">Usuario:</span>
                  <code class="bg-white px-2 py-1 rounded border border-blue-200 font-mono">admin</code>
                </p>
                <p class="flex items-center gap-2">
                  <span class="font-semibold">Contraseña:</span>
                  <code class="bg-white px-2 py-1 rounded border border-blue-200 font-mono">password123</code>
                </p>
              </div>
            </div>

            <!-- Tecnico Card -->
            <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 hover:border-green-400 transition">
              <p class="font-bold text-gray-800 text-sm mb-2">🔧 Técnico</p>
              <div class="space-y-1 text-xs text-gray-700">
                <p class="flex items-center gap-2">
                  <span class="font-semibold">Usuario:</span>
                  <code class="bg-white px-2 py-1 rounded border border-green-200 font-mono">tecnico1</code>
                </p>
                <p class="flex items-center gap-2">
                  <span class="font-semibold">Contraseña:</span>
                  <code class="bg-white px-2 py-1 rounded border border-green-200 font-mono">password123</code>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Info -->
      <div class="text-center mt-6 text-white text-sm">
        <p>Sistema de Gestión de Inventario v1.0</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth.js'

const router = useRouter()
const auth = useAuthStore()

const form = ref({
  username: '',
  password: '',
})

const loading = ref(false)
const error = ref('')
const errors = ref({})

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  errors.value = {}

  // Validación local
  if (!form.value.username?.trim()) {
    errors.value.username = 'El usuario es obligatorio'
    loading.value = false
    return
  }
  if (!form.value.password) {
    errors.value.password = 'La contraseña es obligatoria'
    loading.value = false
    return
  }

  const result = await auth.login(form.value.username.trim(), form.value.password)
  
  if (result.success) {
    router.push('/')
  } else {
    error.value = result.error || 'Error al iniciar sesión'
  }
  
  loading.value = false
}
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 0.2;
  }
  50% {
    opacity: 0.3;
  }
}

.animate-pulse {
  animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}
</style>

