<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-100">Catálogo de Inventario</h1>
          <p class="text-gray-500 mt-1 text-sm">Busca y visualiza todos los componentes disponibles</p>
        </div>

        <!-- Búsqueda -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Buscar</label>
              <input
                v-model="filtros.busqueda"
                type="text"
                placeholder="Marca, modelo, categoría..."
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              />
            </div>
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Categoría</label>
              <select
                v-model="filtros.categoria"
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
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
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Tipo</label>
              <select
                v-model="filtros.tipo"
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              >
                <option value="">Todos</option>
                <option value="Serializado">Serializado</option>
                <option value="Consumible">Consumible</option>
              </select>
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

        <!-- Tabla de Catálogo -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800/40">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Marca</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Modelo</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Precio (Bs)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60">
                <tr v-for="item in catalogoFiltrado" :key="item.id" class="hover:bg-gray-800/30 transition">
                  <td class="px-6 py-4 text-sm text-gray-300">{{ item.categoria }}</td>
                  <td class="px-6 py-4 text-sm text-gray-300">{{ item.marca }}</td>
                  <td class="px-6 py-4 text-sm text-gray-200 font-medium">{{ item.modelo }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span
                      class="px-2.5 py-1 rounded-lg text-xs font-semibold"
                      :class="item.tipo_registro === 'Serializado' ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'"
                    >
                      {{ item.tipo_registro }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-200 font-semibold">Bs {{ item.precio }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="catalogoFiltrado.length === 0" class="text-center py-12 text-gray-600">
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
</script>
