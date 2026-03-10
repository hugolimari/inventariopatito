<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-100">Activos Fijos</h1>
            <p class="text-gray-500 mt-1 text-sm">Gestión de componentes serializados</p>
          </div>
          <router-link
            to="/activos-fijos/nuevo"
            class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2 px-5 rounded-xl text-sm transition shadow-lg shadow-cyan-500/20"
          >
            + Nuevo Activo
          </router-link>
        </div>

        <!-- Búsqueda y Filtros -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Buscar</label>
              <input
                v-model="filtros.busqueda"
                type="text"
                placeholder="Número de serie, marca..."
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              />
            </div>
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Estado</label>
              <select
                v-model="filtros.estado"
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
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
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800/40">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Número de Serie</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Marca/Modelo</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Asignado a</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60">
                <tr v-for="activo in activosFiltrados" :key="activo.id" class="hover:bg-gray-800/30 transition">
                  <td class="px-6 py-4 text-sm font-mono text-cyan-400">{{ activo.numero_serie }}</td>
                  <td class="px-6 py-4 text-sm text-gray-300">
                    {{ activo.catalogo?.marca }} {{ activo.catalogo?.modelo }}
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <span :class="getEstadoClass(activo.estado)" class="px-2.5 py-1 rounded-lg text-xs font-semibold">
                      {{ activo.estado }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-400">
                    {{ activo.usuario_asignado?.nombre_completo || '-' }}
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <button
                      @click="abrirEditar(activo)"
                      class="text-cyan-400 hover:text-cyan-300 font-medium text-xs px-3 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 transition"
                    >
                      Editar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Modal Editar Activo Fijo -->
          <div v-if="mostrarEditar" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cerrarEditar"></div>
            <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
              <h2 class="text-lg font-bold text-gray-100 mb-5">Editar Activo Fijo</h2>
              <form @submit.prevent="guardarEditar" class="space-y-4">
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Catálogo *</label>
                  <select v-model="formEditar.catalogo_id" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                    <option value="">Selecciona un componente</option>
                    <option v-for="item in inventario.catalogo" :key="item.id" :value="item.id">
                      {{ item.marca }} {{ item.modelo }} ({{ item.categoria }})
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Número de Serie *</label>
                  <input v-model="formEditar.numero_serie" type="text" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Estado *</label>
                  <select v-model="formEditar.estado" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                    <option value="En Almacén">En Almacén</option>
                    <option value="Asignado">Asignado</option>
                    <option value="Dado de Baja">Dado de Baja</option>
                  </select>
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Asignado a</label>
                  <select v-model="formEditar.asignado_a" class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                    <option value="">Sin asignar</option>
                    <option v-for="user in usuarios" :key="user.id" :value="user.id">
                      {{ user.nombre_completo }}
                    </option>
                  </select>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                  <button type="button" @click="cerrarEditar" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-5 rounded-xl border border-gray-700 transition text-sm">Cancelar</button>
                  <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2 px-5 rounded-xl transition text-sm shadow-lg shadow-cyan-500/20">Guardar</button>
                </div>
              </form>
            </div>
          </div>

          <div v-if="activosFiltrados.length === 0" class="text-center py-12 text-gray-600">
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
  if (!inventario.catalogo || inventario.catalogo.length === 0) {
    await inventario.fetchCatalogo()
  }
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
    'En Almacén': 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
    'Asignado': 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30',
    'Dado de Baja': 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
    'Vendido': 'bg-gray-500/15 text-gray-400 border border-gray-500/30',
  }
  return classes[estado] || 'bg-gray-500/15 text-gray-400 border border-gray-500/30'
}
</script>
