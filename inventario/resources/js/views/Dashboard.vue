<template>
  <div class="min-h-screen bg-gray-950">
    <!-- Navbar -->
    <Navbar />

    <div class="flex">
      <!-- Sidebar -->
      <Sidebar />

      <!-- Main Content -->
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-100 mb-1">Bienvenido al Dashboard</h1>
          <p class="text-gray-500">Resumen general del inventario</p>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
          <!-- Card: Total Activos -->
          <div class="group relative bg-gray-900 border border-gray-800 rounded-xl hover:border-cyan-500/30 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-cyan-500 to-cyan-700 group-hover:shadow-[0_0_15px_rgba(6,182,212,0.3)] transition-all duration-300"></div>
            <div class="p-5 relative z-10">
              <div class="flex items-center justify-between mb-3">
                <div class="bg-cyan-500/10 border border-cyan-500/20 rounded-lg p-2.5">
                  <span class="text-2xl">📦</span>
                </div>
                <div class="text-right">
                  <p class="text-xs font-medium text-gray-500">Total Activos</p>
                  <p class="text-2xl font-bold text-cyan-400">{{ totalActivos }}</p>
                </div>
              </div>
              <p class="text-[11px] text-gray-600">Activos fijos registrados</p>
            </div>
          </div>

          <!-- Card: Total Consumibles -->
          <div class="group relative bg-gray-900 border border-gray-800 rounded-xl hover:border-emerald-500/30 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-emerald-500 to-emerald-700 group-hover:shadow-[0_0_15px_rgba(16,185,129,0.3)] transition-all duration-300"></div>
            <div class="p-5 relative z-10">
              <div class="flex items-center justify-between mb-3">
                <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-2.5">
                  <span class="text-2xl">🔧</span>
                </div>
                <div class="text-right">
                  <p class="text-xs font-medium text-gray-500">Consumibles</p>
                  <p class="text-2xl font-bold text-emerald-400">{{ totalConsumibles }}</p>
                </div>
              </div>
              <p class="text-[11px] text-gray-600">Lotes de consumibles</p>
            </div>
          </div>

          <!-- Card: Movimientos Hoy -->
          <div class="group relative bg-gray-900 border border-gray-800 rounded-xl hover:border-violet-500/30 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-violet-500 to-violet-700 group-hover:shadow-[0_0_15px_rgba(139,92,246,0.3)] transition-all duration-300"></div>
            <div class="p-5 relative z-10">
              <div class="flex items-center justify-between mb-3">
                <div class="bg-violet-500/10 border border-violet-500/20 rounded-lg p-2.5">
                  <span class="text-2xl">📋</span>
                </div>
                <div class="text-right">
                  <p class="text-xs font-medium text-gray-500">Movimientos Hoy</p>
                  <p class="text-2xl font-bold text-violet-400">{{ movimientosHoy }}</p>
                </div>
              </div>
              <p class="text-[11px] text-gray-600">Registrados en el día</p>
            </div>
          </div>

          <!-- Card: Activos Asignados -->
          <div class="group relative bg-gray-900 border border-gray-800 rounded-xl hover:border-amber-500/30 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-amber-500 to-amber-700 group-hover:shadow-[0_0_15px_rgba(245,158,11,0.3)] transition-all duration-300"></div>
            <div class="p-5 relative z-10">
              <div class="flex items-center justify-between mb-3">
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-2.5">
                  <span class="text-2xl">👤</span>
                </div>
                <div class="text-right">
                  <p class="text-xs font-medium text-gray-500">Asignados</p>
                  <p class="text-2xl font-bold text-amber-400">{{ activosAsignados }}</p>
                </div>
              </div>
              <p class="text-[11px] text-gray-600">Activos en uso</p>
            </div>
          </div>
        </div>

        <!-- Últimos Movimientos -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-800">
            <h2 class="text-lg font-bold text-gray-100 flex items-center gap-2">
              <span>📊</span> Últimos Movimientos
            </h2>
          </div>
          
          <div v-if="ultimosMovimientos.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800/40">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Observaciones</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60">
                <tr v-for="move in ultimosMovimientos" :key="move.id" class="hover:bg-gray-800/30 transition">
                  <td class="px-6 py-4 text-sm text-gray-300 font-medium">{{ formatDate(move.created_at) }}</td>
                  <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold" :class="getTipoClass(move.tipo_movimiento)">
                      {{ move.tipo_movimiento }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-300">{{ move.operador?.nombre_completo || 'N/A' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ move.observaciones || '—' }}</td>
                  <td class="px-6 py-4 text-sm">
                    <button @click="verDetalles(move)" class="text-cyan-400 hover:text-cyan-300 font-medium text-xs px-3 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 transition">Ver detalles</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Modal Detalles Movimiento -->
          <div v-if="mostrarDetalles" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cerrarDetalles"></div>
            <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
              <h2 class="text-lg font-bold text-gray-100 mb-5">Detalles del Movimiento</h2>
              <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Fecha:</span><span class="text-gray-200">{{ formatDate(detalleMovimiento.created_at) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tipo:</span><span class="text-gray-200">{{ detalleMovimiento.tipo_movimiento }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Usuario:</span><span class="text-gray-200">{{ detalleMovimiento.operador?.nombre_completo || 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Observaciones:</span><span class="text-gray-200">{{ detalleMovimiento.observaciones || '—' }}</span></div>
                <div>
                  <span class="text-gray-500">Componente:</span>
                  <span v-if="detalleMovimiento.activo_fijo" class="text-gray-200 ml-2">Activo Fijo: {{ detalleMovimiento.activo_fijo?.numero_serie }} ({{ detalleMovimiento.activo_fijo?.catalogo?.marca }} {{ detalleMovimiento.activo_fijo?.catalogo?.modelo }})</span>
                  <span v-else-if="detalleMovimiento.lote_consumible" class="text-gray-200 ml-2">Consumible: {{ detalleMovimiento.lote_consumible?.catalogo?.modelo }} (Disponible: {{ detalleMovimiento.lote_consumible?.cantidad_disponible }})</span>
                  <span v-else class="text-gray-500 ml-2">—</span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">Cantidad:</span><span class="text-gray-200 font-semibold">{{ detalleMovimiento.cantidad_afectada }}</span></div>
              </div>
              <div class="flex justify-end mt-6">
                <button @click="cerrarDetalles" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-5 rounded-xl border border-gray-700 transition">Cerrar</button>
              </div>
            </div>
          </div>

          <div v-if="ultimosMovimientos.length === 0" class="text-center text-gray-600 py-12">
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
    'Ingreso': 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
    'Check-out': 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30',
    'Check-in': 'bg-violet-500/15 text-violet-400 border border-violet-500/30',
    'Baja': 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
    'Venta': 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
  }
  return classes[tipo] || 'bg-gray-500/15 text-gray-400 border border-gray-500/30'
}
</script>
