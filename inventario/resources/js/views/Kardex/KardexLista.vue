<template>
  <div>
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-72 p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Kardex - Movimientos</h1>
            <p class="text-gray-600 mt-2">Historial de todas las transacciones</p>
          </div>
          <router-link
            to="/movimientos/crear"
            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg"
          >
            + Nuevo Movimiento
          </router-link>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Tipo de Movimiento</label>
              <select
                v-model="filtros.tipo"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
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
              <label class="block text-gray-700 font-semibold mb-2">Usuario</label>
              <input
                v-model="filtros.usuario"
                type="text"
                placeholder="Buscar usuario..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
              />
            </div>
            <div class="flex items-end">
              <button
                @click="limpiarFiltros"
                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tipo</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Usuario</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Componente</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cantidad</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Observaciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="mov in movimientosFiltrados" :key="mov.id" class="border-b hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-900">{{ formatDate(mov.created_at) }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span :class="getTipoClass(mov.tipo_movimiento)" class="px-2 py-1 rounded text-xs font-semibold">
                      {{ mov.tipo_movimiento }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ mov.operador?.nombre_completo }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">
                    <span v-if="mov.activo_fijo">{{ mov.activo_fijo?.numero_serie }}</span>
                    <span v-else-if="mov.lote_consumible">{{ mov.lote_consumible?.catalogo?.modelo }}</span>
                  </td>
                  <td class="px-6 py-4 text-sm font-semibold">{{ mov.cantidad_afectada }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ mov.observaciones }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="movimientosFiltrados.length === 0" class="text-center py-8 text-gray-500">
            No hay movimientos registrados
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
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
    'Ingreso': 'bg-green-100 text-green-800',
    'Check-out': 'bg-blue-100 text-blue-800',
    'Check-in': 'bg-purple-100 text-purple-800',
    'Baja': 'bg-red-100 text-red-800',
    'Venta': 'bg-yellow-100 text-yellow-800',
  }
  return classes[tipo] || 'bg-gray-100 text-gray-800'
}
</script>
