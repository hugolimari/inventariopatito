<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <!-- Modal Nuevo Movimiento -->
        <div class="fixed inset-0 flex items-center justify-center z-50">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
          <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
            <h1 class="text-2xl font-bold text-gray-100 mb-1">Nuevo Movimiento</h1>
            <p class="text-gray-500 mb-5 text-sm">Registra un movimiento en el Kardex</p>
            <form @submit.prevent="handleSubmit" class="space-y-4">
              <!-- Tipo de Movimiento -->
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Tipo de Movimiento *</label>
                <select
                  v-model="form.tipo_movimiento"
                  @change="limpiarComponents"
                  required
                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm"
                >
                  <option value="">Selecciona un tipo</option>
                  <option value="Ingreso">Ingreso</option>
                  <option value="Check-out">Check-out</option>
                  <option value="Check-in">Check-in</option>
                  <option value="Baja">Baja</option>
                  <option value="Venta">Venta</option>
                </select>
              </div>

              <!-- Activo Fijo o Consumible -->
              <div v-if="mostrarActivos || form.tipo_movimiento === 'Venta'">
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Activo Fijo *</label>
                <select
                  v-model="form.activo_fijo_id"
                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm"
                >
                  <option value="">Selecciona un activo</option>
                  <option v-for="activo in inventario.activosFijos" :key="activo.id" :value="activo.id">
                    {{ activo.numero_serie }} - {{ activo.catalogo?.marca }} {{ activo.catalogo?.modelo }}
                  </option>
                </select>
              </div>

              <div v-if="mostrarConsumibles || form.tipo_movimiento === 'Venta'">
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Consumible *</label>
                <select
                  v-model="form.lote_consumible_id"
                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm"
                >
                  <option value="">Selecciona un consumible</option>
                  <option v-for="cons in inventario.consumibles" :key="cons.id" :value="cons.id">
                    {{ cons.catalogo?.modelo }} (Disponible: {{ cons.cantidad_disponible }})
                  </option>
                </select>
              </div>

              <!-- Receptor -->
              <div v-if="form.tipo_movimiento === 'Check-out'">
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Entregar a (Usuario) *</label>
                <select
                  v-model="form.receptor_id"
                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm"
                >
                  <option value="">Selecciona un usuario</option>
                  <option value="1">Carlos Almacenero</option>
                  <option value="2">Patricia Almacenera</option>
                  <option value="4">Roberto Técnico</option>
                  <option value="5">Alejandra Técnica</option>
                  <option value="6">Luis Técnico</option>
                </select>
              </div>

              <!-- Cantidad -->
              <div v-if="mostrarConsumibles || form.tipo_movimiento === 'Check-in' || form.tipo_movimiento === 'Venta'">
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Cantidad *</label>
                <input
                  v-model.number="form.cantidad_afectada"
                  type="number"
                  placeholder="Ej: 5"
                  required
                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm"
                />
              </div>

              <!-- Observaciones -->
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Observaciones</label>
                <textarea
                  v-model="form.observaciones"
                  placeholder="Añade observaciones si es necesario..."
                  rows="3"
                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm resize-none"
                ></textarea>
              </div>

              <!-- Botones -->
              <div class="flex gap-3 pt-2">
                <button
                  type="submit"
                  :disabled="loading"
                  class="bg-violet-600 hover:bg-violet-500 text-white font-medium py-2.5 px-6 rounded-xl disabled:opacity-50 transition text-sm shadow-lg shadow-violet-500/20"
                >
                  {{ loading ? 'Registrando...' : 'Registrar Movimiento' }}
                </button>
                <button type="button" @click="cerrarModal" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2.5 px-6 rounded-xl border border-gray-700 transition text-sm">
                  Cancelar
                </button>
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
const cerrarModal = () => {
  router.push('/movimientos')
}
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
  tipo_movimiento: '',
  activo_fijo_id: null,
  lote_consumible_id: null,
  receptor_id: null,
  cantidad_afectada: 1,
  observaciones: '',
})

const loading = ref(false)
const error = ref('')

onMounted(async () => {
  await inventario.fetchActivosFijos()
  await inventario.fetchConsumibles()
})

const mostrarActivos = computed(() => {
  return ['Ingreso', 'Check-out', 'Check-in', 'Baja'].includes(form.value.tipo_movimiento)
})

const mostrarConsumibles = computed(() => {
  return ['Ingreso', 'Check-out'].includes(form.value.tipo_movimiento)
})

const limpiarComponents = () => {
  form.value.activo_fijo_id = null
  form.value.lote_consumible_id = null
  form.value.receptor_id = null
  form.value.cantidad_afectada = 1
}

const handleSubmit = async () => {
  loading.value = true
  error.value = ''

  if (!form.value.tipo_movimiento) {
    error.value = 'Selecciona un tipo de movimiento'
    loading.value = false
    return
  }

  const data = {
    ...form.value,
    operador_id: auth.user?.id,
  }

  const result = await inventario.registrar_movimiento(data)

  if (result.success) {
    router.push('/movimientos')
  } else {
    error.value = result.error
  }

  loading.value = false
}
</script>
