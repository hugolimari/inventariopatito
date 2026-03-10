<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-blue-50">
    <!-- Navbar -->
    <Navbar />

    <div class="flex">
      <!-- Sidebar -->
      <Sidebar />

      <!-- Main Content -->
      <div class="flex-1 pl-72 pt-24 p-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-800 mb-2">👋 Bienvenido al Dashboard</h1>
          <p class="text-gray-600">Resumen general del inventario</p>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Card: Total Activos -->
          <div class="group relative bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-600 to-blue-800 group-hover:w-full transition-all duration-300 opacity-20"></div>
            <div class="p-6 relative z-10">
              <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 rounded-lg p-3">
                  <span class="text-3xl">📦</span>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-gray-600">Total Activos</p>
                  <p class="text-3xl font-bold text-blue-600">{{ totalActivos }}</p>
                </div>
              </div>
              <p class="text-xs text-gray-500">Activos fijos registrados</p>
            </div>
          </div>

          <!-- Card: Total Consumibles -->
          <div class="group relative bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-green-600 to-green-800 group-hover:w-full transition-all duration-300 opacity-20"></div>
            <div class="p-6 relative z-10">
              <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 rounded-lg p-3">
                  <span class="text-3xl">🔧</span>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-gray-600">Consumibles</p>
                  <p class="text-3xl font-bold text-green-600">{{ totalConsumibles }}</p>
                </div>
              </div>
              <p class="text-xs text-gray-500">Lotes de consumibles</p>
            </div>
          </div>

          <!-- Card: Movimientos Hoy -->
          <div class="group relative bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-purple-600 to-purple-800 group-hover:w-full transition-all duration-300 opacity-20"></div>
            <div class="p-6 relative z-10">
              <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-100 rounded-lg p-3">
                  <span class="text-3xl">📋</span>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-gray-600">Movimientos Hoy</p>
                  <p class="text-3xl font-bold text-purple-600">{{ movimientosHoy }}</p>
                </div>
              </div>
              <p class="text-xs text-gray-500">Registrados en el día</p>
            </div>
          </div>

          <!-- Card: Activos Asignados -->
          <div class="group relative bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-orange-600 to-orange-800 group-hover:w-full transition-all duration-300 opacity-20"></div>
            <div class="p-6 relative z-10">
              <div class="flex items-center justify-between mb-4">
                <div class="bg-orange-100 rounded-lg p-3">
                  <span class="text-3xl">👤</span>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-gray-600">Asignados</p>
                  <p class="text-3xl font-bold text-orange-600">{{ activosAsignados }}</p>
                </div>
              </div>
              <p class="text-xs text-gray-500">Activos en uso</p>
            </div>
          </div>
        </div>

        <!-- Últimos Movimientos -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-6">
            <h2 class="text-2xl font-bold flex items-center gap-2">
              <span>📊</span> Últimos Movimientos
            </h2>
          </div>
          
          <div v-if="ultimosMovimientos.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">📅 Fecha</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">🏷️ Tipo</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">👤 Usuario</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">📝 Observaciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="move in ultimosMovimientos" :key="move.id" class="hover:bg-gray-50 transition">
                  <td class="px-6 py-4 text-sm text-gray-800 font-semibold">{{ formatDate(move.created_at) }}</td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold inline-block" :class="getTipoClass(move.tipo_movimiento)">
                      {{ move.tipo_movimiento }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ move.operador?.nombre_completo || 'N/A' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ move.observaciones || '—' }}</td>
                  <td class="px-6 py-4 text-sm">
                    <button @click="verDetalles(move)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded-lg">Ver detalles</button>
                  </td>
                </tr>
                      <!-- Modal Detalles Movimiento -->
                      <div v-if="mostrarDetalles" class="fixed inset-0 flex items-center justify-center z-50" style="z-index:9999">
                        <div class="absolute inset-0 pointer-events-none" style="background: rgba(0,0,0,0.08); z-index:9998;"></div>
                        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative" style="z-index:9999;">
                          <h2 class="text-xl font-bold mb-4">Detalles del Movimiento</h2>
                          <div class="mb-2"><strong>Fecha:</strong> {{ formatDate(detalleMovimiento.created_at) }}</div>
                          <div class="mb-2"><strong>Tipo:</strong> {{ detalleMovimiento.tipo_movimiento }}</div>
                          <div class="mb-2"><strong>Usuario:</strong> {{ detalleMovimiento.operador?.nombre_completo || 'N/A' }}</div>
                          <div class="mb-2"><strong>Observaciones:</strong> {{ detalleMovimiento.observaciones || '—' }}</div>
                          <div class="mb-2"><strong>Componente:</strong>
                            <span v-if="detalleMovimiento.activo_fijo">Activo Fijo: {{ detalleMovimiento.activo_fijo?.numero_serie }} ({{ detalleMovimiento.activo_fijo?.catalogo?.marca }} {{ detalleMovimiento.activo_fijo?.catalogo?.modelo }})</span>
                            <span v-else-if="detalleMovimiento.lote_consumible">Consumible: {{ detalleMovimiento.lote_consumible?.catalogo?.modelo }} (Disponible: {{ detalleMovimiento.lote_consumible?.cantidad_disponible }})</span>
                            <span v-else>—</span>
                          </div>
                          <div class="mb-2"><strong>Cantidad afectada:</strong> {{ detalleMovimiento.cantidad_afectada }}</div>
                          <div class="flex justify-end mt-4">
                            <button @click="cerrarDetalles" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cerrar</button>
                          </div>
                        </div>
                      </div>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center text-gray-500 py-12">
            <p class="text-lg">📭 No hay movimientos registrados</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const mostrarDetalles = ref(false)
const detalleMovimiento = ref({})

const verDetalles = (mov) => {
  detalleMovimiento.value = mov
  mostrarDetalles.value = true
}
const cerrarDetalles = () => {
  mostrarDetalles.value = false
}
import { ref, computed, onMounted } from 'vue'
import { useInventarioStore } from '../stores/inventario.js'
import Navbar from '../components/Navbar.vue'
import Sidebar from '../components/Sidebar.vue'

const inventario = useInventarioStore()

onMounted(async () => {
  await inventario.fetchActivosFijos()
  await inventario.fetchConsumibles()
  await inventario.fetchMovimientos()
})

const totalActivos = computed(() => inventario.activosFijos.length)
const totalConsumibles = computed(() => inventario.consumibles.length)
const activosAsignados = computed(() => 
  inventario.activosFijos.filter(a => a.estado === 'Asignado').length
)
const movimientosHoy = computed(() => {
  const hoy = new Date().toDateString()
  return inventario.movimientos.filter(m => new Date(m.created_at).toDateString() === hoy).length
})
const ultimosMovimientos = computed(() => inventario.movimientos.slice(0, 5))

const formatDate = (date) => {
  return new Date(date).toLocaleDateString() + ' ' + new Date(date).toLocaleTimeString()
}

const getTipoClass = (tipo) => {
  const classes = {
    'Ingreso': 'bg-green-100 text-green-800',
    'Check-out': 'bg-blue-100 text-blue-800',
    'Check-in': 'bg-purple-100 text-purple-800',
    'Baja': 'bg-red-100 text-red-800',
    'Venta': 'bg-yellow-100 text-yellow-800',
  }
  return classes[tipo] || 'bg-gray-100 text-gray-800'
}
</script>
