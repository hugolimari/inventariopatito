<template>
  <div>
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-72 p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Activos Fijos</h1>
            <p class="text-gray-600 mt-2">Gestión de componentes serializados</p>
          </div>
          <router-link
            to="/activos-fijos/nuevo"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg"
          >
            + Nuevo Activo
          </router-link>
        </div>

        <!-- Búsqueda y Filtros -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Buscar</label>
              <input
                v-model="filtros.busqueda"
                type="text"
                placeholder="Número de serie, marca..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              />
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Estado</label>
              <select
                v-model="filtros.estado"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              >
                <option value="">Todos</option>
                <option value="En Almacén">En Almacén</option>
                <option value="Asignado">Asignado</option>
                <option value="Dado de Baja">Dado de Baja</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Tabla de Activos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Número de Serie</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Marca/Modelo</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Asignado a</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="activo in activosFiltrados" :key="activo.id" class="border-b hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ activo.numero_serie }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ activo.catalogo?.marca }} {{ activo.catalogo?.modelo }}
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <span :class="getEstadoClass(activo.estado)" class="px-2 py-1 rounded text-xs font-semibold">
                      {{ activo.estado }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ activo.usuario_asignado?.nombre_completo || '-' }}
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <button
                      @click="abrirEditar(activo)"
                      class="text-blue-600 hover:text-blue-900 font-semibold"
                    >
                      Editar
                    </button>
                  </td>
                        <!-- Modal Editar Activo Fijo -->
                        <div v-if="mostrarEditar" class="fixed inset-0 flex items-center justify-center z-50" style="z-index:9999">
                          <div class="absolute inset-0 pointer-events-none" style="background: rgba(0,0,0,0.08); z-index:9998;"></div>
                          <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative" style="z-index:9999;">
                            <h2 class="text-xl font-bold mb-4">Editar Activo Fijo</h2>
                            <form @submit.prevent="guardarEditar">
                              <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Catálogo *</label>
                                <select v-model="formEditar.catalogo_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                  <option value="">Selecciona un componente</option>
                                  <option v-for="item in inventario.catalogo" :key="item.id" :value="item.id">
                                    {{ item.marca }} {{ item.modelo }} ({{ item.categoria }})
                                  </option>
                                </select>
                              </div>
                              <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Número de Serie *</label>
                                <input v-model="formEditar.numero_serie" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                              </div>
                              <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Estado *</label>
                                <select v-model="formEditar.estado" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                  <option value="En Almacén">En Almacén</option>
                                  <option value="Asignado">Asignado</option>
                                  <option value="Dado de Baja">Dado de Baja</option>
                                </select>
                              </div>
                              <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Asignado a</label>
                                <select v-model="formEditar.asignado_a" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                  <option value="">Sin asignar</option>
                                  <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                    {{ user.nombre_completo }}
                                  </option>
                                </select>
                              </div>
                              <div class="flex justify-end gap-2">
                                <button type="button" @click="cerrarEditar" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cancelar</button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Guardar</button>
                              </div>
                            </form>
                          </div>
                        </div>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="activosFiltrados.length === 0" class="text-center py-8 text-gray-500">
            No hay activos fijos registrados
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const mostrarEditar = ref(false)
const formEditar = ref({
  catalogo_id: '',
  numero_serie: '',
  estado: 'En Almacén',
  asignado_a: '',
})
let activoEditId = null

const usuarios = ref([])

const abrirEditar = (activo) => {
  activoEditId = activo.id
  formEditar.value = {
    catalogo_id: activo.catalogo_id,
    numero_serie: activo.numero_serie,
    estado: activo.estado,
    asignado_a: activo.asignado_a || '',
  }
  mostrarEditar.value = true
  fetchUsuarios()
}
const cerrarEditar = () => {
  mostrarEditar.value = false
}
const guardarEditar = async () => {
  await inventario.actualizar_activoFijo(activoEditId, formEditar.value)
  mostrarEditar.value = false
  await inventario.fetchActivosFijos()
}

const fetchUsuarios = async () => {
  // Obtener usuarios desde el backend
  try {
    const res = await fetch('/api/usuarios', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    const data = await res.json()
    usuarios.value = data.data || []
  } catch (e) {
    usuarios.value = []
  }
}
import { ref, computed, onMounted } from 'vue'
import { useInventarioStore } from '../../stores/inventario.js'
import Navbar from '../../components/Navbar.vue'
import Sidebar from '../../components/Sidebar.vue'

const inventario = useInventarioStore()

const filtros = ref({
  busqueda: '',
  estado: '',
})

onMounted(async () => {
  await inventario.fetchActivosFijos()
})

const activosFiltrados = computed(() => {
  return inventario.activosFijos.filter(activo => {
    const matchBusqueda =
      !filtros.value.busqueda ||
      activo.numero_serie.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
      activo.catalogo?.marca.toLowerCase().includes(filtros.value.busqueda.toLowerCase())

    const matchEstado = !filtros.value.estado || activo.estado === filtros.value.estado

    return matchBusqueda && matchEstado
  })
})

const getEstadoClass = (estado) => {
  const classes = {
    'En Almacén': 'bg-green-100 text-green-800',
    'Asignado': 'bg-blue-100 text-blue-800',
    'Dado de Baja': 'bg-red-100 text-red-800',
    'Vendido': 'bg-gray-100 text-gray-800',
  }
  return classes[estado] || 'bg-gray-100 text-gray-800'
}
</script>
