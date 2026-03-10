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

        <!-- Weather + User info + logout -->
        <div class="flex items-center gap-5">
          <!-- Weather Widget -->
          <div v-if="weather" class="flex items-center gap-2.5 bg-gray-800/60 border border-gray-700/50 rounded-xl px-3.5 py-2 mr-1">
            <img :src="`https://openweathermap.org/img/wn/${weather.icon}@2x.png`" :alt="weather.desc" class="w-9 h-9 -my-1" />
            <div class="leading-tight">
              <p class="text-sm font-bold text-gray-100">{{ weather.temp }}°C</p>
              <p class="text-[10px] text-gray-500 capitalize">{{ weather.desc }}</p>
            </div>
            <div class="border-l border-gray-700/50 pl-2.5 ml-0.5 leading-tight">
              <p class="text-[10px] text-gray-500">La Paz</p>
              <p class="text-[10px] text-gray-600">💧{{ weather.humidity }}%</p>
            </div>
          </div>

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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const router = useRouter()
const auth = useAuthStore()

const weather = ref(null)

const fetchWeather = async () => {
  try {
    const API_KEY = '5f6a448206c824b01a95263e2cfd8788'
    const res = await fetch(
      `https://api.openweathermap.org/data/2.5/weather?q=La Paz,BO&units=metric&lang=es&appid=${API_KEY}`
    )
    const data = await res.json()
    if (data.cod === 200) {
      weather.value = {
        temp: Math.round(data.main.temp),
        desc: data.weather[0].description,
        icon: data.weather[0].icon,
        humidity: data.main.humidity,
      }
    }
  } catch (e) {
    // silently fail
  }
}

onMounted(() => {
  fetchWeather()
  // Refresh every 10 minutes
  setInterval(fetchWeather, 600000)
})

const handleLogout = () => {
  auth.logout()
  router.push('/login')
}
</script>
