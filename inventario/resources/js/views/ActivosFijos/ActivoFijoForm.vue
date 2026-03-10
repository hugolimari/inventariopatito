<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="fixed inset-0 flex items-center justify-center z-50">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
          <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-2xl relative z-10">
            <h1 class="text-2xl font-bold text-gray-100 mb-1">Nuevo Activo Fijo</h1>
            <p class="text-gray-500 mb-5 text-sm">Registra un nuevo componente serializado</p>
            <form @submit.prevent="handleSubmit" class="space-y-5">
            <!-- Catálogo -->
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Catálogo *</label>
              <select
                v-model="form.catalogo_id"
                required
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              >
                <option value="">Selecciona un componente</option>
                <option v-for="item in inventario.catalogo" :key="item.id" :value="item.id">
                  {{ item.marca }} {{ item.modelo }} ({{ item.categoria }})
                </option>
              </select>
            </div>

            <!-- Número de Serie -->
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Número de Serie *</label>
              <input
                v-model="form.numero_serie"
                type="text"
                placeholder="Ej: DELL-001-2024"
                required
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              />
            </div>

            <!-- Estado -->
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Estado *</label>
              <select
                v-model="form.estado"
                required
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              >
                <option value="">Selecciona un estado</option>
                <option value="En Almacén">En Almacén</option>
                <option value="Asignado">Asignado</option>
                <option value="Dado de Baja">Dado de Baja</option>
              </select>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-2">
              <button
                type="submit"
                :disabled="loading"
                class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2.5 px-6 rounded-xl disabled:opacity-50 transition text-sm shadow-lg shadow-cyan-500/20"
              >
                {{ loading ? 'Guardando...' : 'Guardar Activo' }}
              </button>
              <router-link to="/activos-fijos" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2.5 px-6 rounded-xl border border-gray-700 transition text-sm">
                Cancelar
              </router-link>
            </div>

            <!-- Error -->
            <div v-if="error" class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl text-sm">
              {{ error }}
            </div>
          </form>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useInventarioStore } from '../../stores/inventario.js'
import { useAuthStore } from '../../stores/auth.js'
import Navbar from '../../components/Navbar.vue'
import Sidebar from '../../components/Sidebar.vue'

const router = useRouter()
const inventario = useInventarioStore()
const auth = useAuthStore()

const form = ref({
  catalogo_id: '',
  numero_serie: '',
  estado: 'En Almacén',
})

const loading = ref(false)
const error = ref('')

const handleSubmit = async () => {
  loading.value = true
  error.value = ''

  const data = {
    ...form.value,
    creado_por: auth.user?.id,
  }

  const result = await inventario.crear_activoFijo(data)

  if (result.success) {
    router.push('/activos-fijos')
  } else {
    error.value = result.error
  }

  loading.value = false
}
</script>
