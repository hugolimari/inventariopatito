<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="fixed inset-0 flex items-center justify-center z-50">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
          <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
            <h2 class="text-lg font-bold text-gray-100 mb-1">Nuevo Consumible</h2>
            <p class="text-gray-500 mb-5 text-sm">Agregar stock a un consumible existente</p>
            <form @submit.prevent="handleSubmit" class="space-y-4">
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Consumible *</label>
                <select v-model="form.catalogo_id" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                  <option value="">Selecciona un consumible</option>
                  <option v-for="item in consumiblesDisponibles" :key="item.id" :value="item.id">
                    {{ item.marca }} {{ item.modelo }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Cantidad *</label>
                <input v-model.number="form.cantidad_disponible" type="number" min="1" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
              </div>
              <div class="flex justify-end gap-3 pt-2">
                <router-link to="/consumibles" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-5 rounded-xl border border-gray-700 transition text-sm">Cancelar</router-link>
                <button type="submit" :disabled="loading" class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-2 px-5 rounded-xl disabled:opacity-50 transition text-sm shadow-lg shadow-emerald-500/20">
                  {{ loading ? 'Guardando...' : 'Guardar Consumible' }}
                </button>
              </div>
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

  const existente = inventario.consumibles.find(c => c.catalogo_id === form.value.catalogo_id)
  if (existente) {
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
