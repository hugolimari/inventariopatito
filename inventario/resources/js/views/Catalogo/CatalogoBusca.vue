<template>
  <div>
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-72 p-8">
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Catálogo de Inventario</h1>
          <p class="text-gray-600 mt-2">Busca y visualiza todos los componentes disponibles</p>
        </div>

        <!-- Búsqueda -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Buscar</label>
              <input
                v-model="filtros.busqueda"
                type="text"
                placeholder="Marca, modelo, categoría..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              />
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Categoría</label>
              <select
                v-model="filtros.categoria"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              >
                <option value="">Todas</option>
                <option value="Computadoras">Computadoras</option>
                <option value="Servidores">Servidores</option>
                <option value="Impresoras">Impresoras</option>
                <option value="Monitores">Monitores</option>
                <option value="Equipamiento de Red">Equipamiento de Red</option>
                <option value="Consumibles">Consumibles</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Tipo</label>
              <select
                v-model="filtros.tipo"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              >
                <option value="">Todos</option>
                <option value="Serializado">Serializado</option>
                <option value="Consumible">Consumible</option>
              </select>
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

        <!-- Tabla de Catálogo -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Categoría</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Marca</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Modelo</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tipo</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Precio (Bs)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in catalogoFiltrado" :key="item.id" class="border-b hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-900">{{ item.categoria }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ item.marca }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ item.modelo }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span
                      class="px-2 py-1 rounded text-xs font-semibold"
                      :class="item.tipo_registro === 'Serializado' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                    >
                      {{ item.tipo_registro }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 font-semibold">Bs {{ item.precio }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="catalogoFiltrado.length === 0" class="text-center py-8 text-gray-500">
            No hay items que coincidan con los filtros
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
  busqueda: '',
  categoria: '',
  tipo: '',
})

onMounted(async () => {
  await inventario.fetchCatalogo()
})

const catalogoFiltrado = computed(() => {
  return inventario.catalogo.filter(item => {
    const matchBusqueda =
      !filtros.value.busqueda ||
      item.marca.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
      item.modelo.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
      item.categoria.toLowerCase().includes(filtros.value.busqueda.toLowerCase())

    const matchCategoria = !filtros.value.categoria || item.categoria === filtros.value.categoria
    const matchTipo = !filtros.value.tipo || item.tipo_registro === filtros.value.tipo

    return matchBusqueda && matchCategoria && matchTipo
  })
})

const limpiarFiltros = () => {
  filtros.value = { busqueda: '', categoria: '', tipo: '' }
}

const getStockClass = (stock) => {
  if (!stock || stock === 0) return 'bg-red-100 text-red-800'
  if (stock < 5) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}
</script>
