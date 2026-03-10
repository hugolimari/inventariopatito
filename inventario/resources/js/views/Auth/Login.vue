<template>
  <div class="min-h-screen bg-gray-950 flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute top-[-10%] left-[-5%] w-[500px] h-[500px] bg-cyan-500/10 rounded-full filter blur-[120px] animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[500px] h-[500px] bg-teal-500/10 rounded-full filter blur-[120px] animate-pulse animation-delay-2000"></div>
    <div class="absolute top-[40%] left-[50%] w-[300px] h-[300px] bg-violet-500/5 rounded-full filter blur-[100px]"></div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md">
      <div class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl shadow-black/40 overflow-hidden backdrop-blur-sm">
        <!-- Header with gradient -->
        <div class="bg-gradient-to-r from-cyan-600 to-teal-600 px-8 py-8 text-white text-center relative">
          <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIi8+PC9zdmc+')] opacity-50"></div>
          <div class="relative">
            <div class="w-16 h-16 bg-white/15 rounded-2xl mx-auto mb-4 flex items-center justify-center text-4xl backdrop-blur-sm">📦</div>
            <h1 class="text-2xl font-bold">Inventario</h1>
            <p class="text-cyan-100/80 text-sm mt-1">Sistema de Gestión de Hardware</p>
          </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="p-8 space-y-5">
          <!-- Username Input -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-400">
              <span class="inline-flex items-center gap-2">
                👤 Usuario
              </span>
            </label>
            <input
              v-model="form.username"
              type="text"
              placeholder="admin"
              class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 font-medium transition"
              required
            />
            <p v-if="errors.username" class="text-rose-400 text-sm font-medium">{{ errors.username }}</p>
          </div>

          <!-- Password Input -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-400">
              <span class="inline-flex items-center gap-2">
                🔒 Contraseña
              </span>
            </label>
            <input
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 font-medium transition"
              required
            />
            <p v-if="errors.password" class="text-rose-400 text-sm font-medium">{{ errors.password }}</p>
          </div>

          <!-- Error Alert -->
          <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
          >
            <div v-if="error" class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-5 py-4 rounded-xl">
              <p class="flex items-center gap-2 text-sm font-medium">
                <span>⚠️</span>
                {{ error }}
              </p>
            </div>
          </transition>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-500 hover:to-teal-500 text-white font-bold py-3 px-4 rounded-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30 flex items-center justify-center gap-2"
          >
            <span v-if="!loading">Iniciar Sesión</span>
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
        <div class="px-8">
          <div class="border-t border-gray-800"></div>
        </div>

        <!-- Demo Credentials -->
        <div class="px-8 py-6">
          <p class="text-center text-xs font-medium text-gray-500 mb-4">Usuarios de Prueba</p>
          
          <div class="grid grid-cols-2 gap-3">
            <!-- Admin Card -->
            <div class="bg-cyan-500/5 border border-cyan-500/15 rounded-xl p-3.5 hover:border-cyan-500/30 transition">
              <p class="font-semibold text-cyan-400 text-xs mb-2">👨‍💼 Admin</p>
              <div class="space-y-1 text-[11px] text-gray-400">
                <p><code class="bg-gray-800 px-1.5 py-0.5 rounded text-cyan-300 font-mono text-[10px]">admin</code></p>
                <p><code class="bg-gray-800 px-1.5 py-0.5 rounded text-cyan-300 font-mono text-[10px]">password123</code></p>
              </div>
            </div>

            <!-- Tecnico Card -->
            <div class="bg-emerald-500/5 border border-emerald-500/15 rounded-xl p-3.5 hover:border-emerald-500/30 transition">
              <p class="font-semibold text-emerald-400 text-xs mb-2">🔧 Técnico</p>
              <div class="space-y-1 text-[11px] text-gray-400">
                <p><code class="bg-gray-800 px-1.5 py-0.5 rounded text-emerald-300 font-mono text-[10px]">tecnico1</code></p>
                <p><code class="bg-gray-800 px-1.5 py-0.5 rounded text-emerald-300 font-mono text-[10px]">password123</code></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Info -->
      <div class="text-center mt-6 text-gray-600 text-xs">
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
    opacity: 0.08;
  }
  50% {
    opacity: 0.15;
  }
}

.animate-pulse {
  animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}
</style>
