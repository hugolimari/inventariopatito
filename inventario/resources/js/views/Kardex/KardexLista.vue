<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-100">Kardex - Movimientos</h1>
            <p class="text-gray-500 mt-1 text-sm">Historial de todas las transacciones</p>
          </div>
          <div class="flex gap-3">
            <button
              @click="exportarPDF"
              class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-4 rounded-xl text-sm border border-gray-700 transition flex items-center gap-2"
            >
              📄 Exportar PDF
            </button>
            <router-link
              to="/movimientos/crear"
              class="bg-violet-600 hover:bg-violet-500 text-white font-medium py-2 px-5 rounded-xl text-sm transition shadow-lg shadow-violet-500/20"
            >
              + Nuevo Movimiento
            </router-link>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Tipo de Movimiento</label>
              <select
                v-model="filtros.tipo"
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm"
              >
                <option value="">Todos</option>
                <option value="Ingreso">Ingreso</option>
                <option value="Check-out">Check-out</option>
                <option value="Check-in">Check-in</option>
                <option value="Baja">Baja</option>
                <option value="Venta">Venta</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Usuario</label>
              <input
                v-model="filtros.usuario"
                type="text"
                placeholder="Buscar usuario..."
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500 text-sm"
              />
            </div>
            <div class="flex items-end">
              <button
                @click="limpiarFiltros"
                class="w-full bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2.5 px-4 rounded-xl border border-gray-700 transition text-sm"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800/40">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Componente</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cantidad</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Observaciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60">
                <tr v-for="mov in movimientosFiltrados" :key="mov.id" class="hover:bg-gray-800/30 transition">
                  <td class="px-6 py-4 text-sm text-gray-300 font-medium">{{ formatDate(mov.created_at) }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span :class="getTipoClass(mov.tipo_movimiento)" class="px-2.5 py-1 rounded-lg text-xs font-semibold">
                      {{ mov.tipo_movimiento }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-300">{{ mov.operador?.nombre_completo }}</td>
                  <td class="px-6 py-4 text-sm text-gray-400">
                    <span v-if="mov.activo_fijo">{{ mov.activo_fijo?.numero_serie }}</span>
                    <span v-else-if="mov.lote_consumible">{{ mov.lote_consumible?.catalogo?.modelo }}</span>
                  </td>
                  <td class="px-6 py-4 text-sm font-semibold text-gray-200">{{ mov.cantidad_afectada }}</td>
                  <td class="px-6 py-4 text-sm text-gray-500">{{ mov.observaciones }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="movimientosFiltrados.length === 0" class="text-center py-12 text-gray-600">
            No hay movimientos registrados
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { jsPDF } from 'jspdf'
import autoTable from 'jspdf-autotable'
import { useInventarioStore } from '../../stores/inventario.js'
import Navbar from '../../components/Navbar.vue'
import Sidebar from '../../components/Sidebar.vue'

const inventario = useInventarioStore()

const filtros = ref({
  tipo: '',
  usuario: '',
})

onMounted(async () => {
  await inventario.fetchMovimientos()
})

const movimientosFiltrados = computed(() => {
  return inventario.movimientos.filter(mov => {
    const matchTipo = !filtros.value.tipo || mov.tipo_movimiento === filtros.value.tipo
    const matchUsuario =
      !filtros.value.usuario ||
      mov.operador?.nombre_completo?.toLowerCase().includes(filtros.value.usuario.toLowerCase())

    return matchTipo && matchUsuario
  })
})

const limpiarFiltros = () => {
  filtros.value = { tipo: '', usuario: '' }
}

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

const exportarPDF = () => {
  const doc = new jsPDF()
  
  doc.setFontSize(18)
  doc.text('Kardex de Movimientos - Inventario', 14, 22)
  
  doc.setFontSize(11)
  doc.setTextColor(100)
  doc.text(`Fecha de exportación: ${new Date().toLocaleDateString()}`, 14, 30)
  
  const tableData = movimientosFiltrados.value.map(mov => {
    let componente = mov.activo_fijo 
      ? mov.activo_fijo.numero_serie 
      : (mov.lote_consumible ? mov.lote_consumible.catalogo?.modelo : '')
      
    return [
      formatDate(mov.created_at),
      mov.tipo_movimiento,
      mov.operador?.nombre_completo || 'N/A',
      componente,
      mov.cantidad_afectada,
      mov.observaciones || ''
    ]
  })

  autoTable(doc, {
    startY: 36,
    head: [['Fecha', 'Tipo', 'Usuario', 'Componente', 'Cantidad', 'Observaciones']],
    body: tableData,
    theme: 'grid',
    headStyles: { fillColor: [59, 130, 246] }, // blue-500
    styles: { fontSize: 8 },
    alternateRowStyles: { fillColor: [245, 247, 250] } // Light gray contrast
  })

  // Format date and time for Bolivia
  const dateObj = new Date()
  const dateStr = dateObj.toLocaleDateString('es-BO', { 
    year: 'numeric', 
    month: '2-digit', 
    day: '2-digit',
    timeZone: 'America/La_Paz'
  }).replace(/\//g, '-')
  
  const timeStr = dateObj.toLocaleTimeString('es-BO', { 
    hour: '2-digit', 
    minute: '2-digit', 
    second: '2-digit',
    hour12: false,
    timeZone: 'America/La_Paz'
  }).replace(/:/g, '')

  const fileName = `kardex_${dateStr}_${timeStr}.pdf`
  doc.save(fileName)
}
</script>
