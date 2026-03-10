<template>
  <div>
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-72 p-8">
        <div class="fixed inset-0 flex items-center justify-center z-50" style="background: rgba(0,0,0,0.04);">
          <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-2xl relative">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Nuevo Activo Fijo</h1>
            <p class="text-gray-600 mb-4">Registra un nuevo componente serializado</p>
            <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Catálogo -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Catálogo *</label>
              <select
                v-model="form.catalogo_id"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              >
                <option value="">Selecciona un componente</option>
                <option v-for="item in inventario.catalogo" :key="item.id" :value="item.id">
                  {{ item.marca }} {{ item.modelo }} ({{ item.categoria }})
                </option>
              </select>
            </div>

            <!-- Número de Serie -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Número de Serie *</label>
              <input
                v-model="form.numero_serie"
                type="text"
                placeholder="Ej: DELL-001-2024"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              />
            </div>

            <!-- Estado -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Estado *</label>
              <select
                v-model="form.estado"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              >
                <option value="">Selecciona un estado</option>
                <option value="En Almacén">En Almacén</option>
                <option value="Asignado">Asignado</option>
                <option value="Dado de Baja">Dado de Baja</option>
              </select>
            </div>

            <!-- Botones -->
            <div class="flex gap-4">
              <button
                type="submit"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg disabled:opacity-50"
              >
                {{ loading ? 'Guardando...' : 'Guardar Activo' }}
              </button>
              <router-link to="/activos-fijos" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-8 rounded-lg">
                Cancelar
              </router-link>
            </div>

            <!-- Error -->
            <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
              {{ error }}
            </div>
          </form>
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
