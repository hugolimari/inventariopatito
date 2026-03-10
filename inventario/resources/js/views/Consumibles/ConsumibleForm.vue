<template>
  <div>
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-72 p-8">
        <div class="flex items-center justify-center min-h-screen">
          <div class="absolute inset-0 pointer-events-none" style="background: rgba(0,0,0,0.08); z-index:9998;"></div>
          <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative" style="z-index:9999;">
            <h2 class="text-xl font-bold mb-4">Nuevo Consumible</h2>
            <form @submit.prevent="handleSubmit">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Consumible *</label>
                <select v-model="form.catalogo_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                  <option value="">Selecciona un consumible</option>
                  <option v-for="item in consumiblesDisponibles" :key="item.id" :value="item.id">
                    {{ item.marca }} {{ item.modelo }}
                  </option>
                </select>
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Cantidad *</label>
                <input v-model.number="form.cantidad_disponible" type="number" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
              </div>
              <div class="flex justify-end gap-2">
                <router-link to="/consumibles" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cancelar</router-link>
                <button type="submit" :disabled="loading" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50">
                  {{ loading ? 'Guardando...' : 'Guardar Consumible' }}
                </button>
              </div>
              <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
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
import { ref, computed, onMounted } from 'vue'
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
  cantidad_disponible: '',
})

const loading = ref(false)
const error = ref('')

onMounted(async () => {
  await inventario.fetchCatalogo()
})

const consumiblesDisponibles = computed(() => {
  return inventario.catalogo.filter(item => item.tipo_registro === 'Consumible')
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''

  // Buscar si ya existe un lote con el mismo catalogo_id
  const existente = inventario.consumibles.find(c => c.catalogo_id === form.value.catalogo_id)
  if (existente) {
    // Sumar la cantidad
    const nuevaCantidad = Number(existente.cantidad_disponible) + Number(form.value.cantidad_disponible)
    const result = await inventario.actualizar_consumible(existente.id, { cantidad_disponible: nuevaCantidad })
    if (result.success) {
      router.push('/consumibles')
    } else {
      error.value = result.error
    }
  } else {
    const data = {
      ...form.value,
      creado_por: auth.user?.id,
    }
    const result = await inventario.crear_consumible(data)
    if (result.success) {
      router.push('/consumibles')
    } else {
      error.value = result.error
    }
  }
  loading.value = false
}
</script>
