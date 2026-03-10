<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="fixed inset-0 flex items-center justify-center z-50">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
          <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-2xl relative z-10">
            <h1 class="text-2xl font-bold text-gray-100 mb-1">Nuevo Activo Fijo</h1>
            <p class="text-gray-500 mb-5 text-sm">Registra un nuevo componente serializado</p>
            <form @submit.prevent="handleSubmit" class="space-y-5">
            <!-- Categoría -->
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Categoría</label>
              <select
                v-model="categoriaSeleccionada"
                @change="form.catalogo_id = ''"
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              >
                <option value="">Todas las categorías</option>
                <option v-for="cat in categoriasDisponibles" :key="cat" :value="cat">
                  {{ cat }}
                </option>
              </select>
            </div>

            <!-- Catálogo -->
            <div>
              <div class="flex justify-between items-end mb-2">
                <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider">Catálogo *</label>
                <button 
                  type="button" 
                  @click.prevent="abrirNuevoCatalogo"
                  class="text-cyan-400 hover:text-cyan-300 text-[11px] font-semibold flex items-center gap-1 bg-cyan-500/10 hover:bg-cyan-500/20 px-2 py-0.5 rounded transition"
                >
                  <span>+</span> Añadir Modelo
                </button>
              </div>
              <select
                v-model="form.catalogo_id"
                required
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              >
                <option value="">Selecciona un componente</option>
                <option v-for="item in catalogosFiltrados" :key="item.id" :value="item.id">
                  {{ item.marca }} {{ item.modelo }} ({{ item.categoria }})
                </option>
              </select>
            </div>

            <!-- Número de Serie -->
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Número de Serie *</label>
              <input
                v-model="form.numero_serie"
                type="text"
                placeholder="Ej: DELL-001-2024"
                required
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              />
            </div>

            <!-- Estado -->
            <div>
              <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Estado *</label>
              <select
                v-model="form.estado"
                required
                class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm"
              >
                <option value="">Selecciona un estado</option>
                <option value="En Almacén">En Almacén</option>
                <option value="Asignado">Asignado</option>
                <option value="Dado de Baja">Dado de Baja</option>
              </select>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-2">
              <button
                type="submit"
                :disabled="loading"
                class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2.5 px-6 rounded-xl disabled:opacity-50 transition text-sm shadow-lg shadow-cyan-500/20"
              >
                {{ loading ? 'Guardando...' : 'Guardar Activo' }}
              </button>
              <router-link to="/activos-fijos" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2.5 px-6 rounded-xl border border-gray-700 transition text-sm">
                Cancelar
              </router-link>
            </div>

            <!-- Error -->
            <div v-if="error" class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl text-sm">
              {{ error }}
            </div>
          </form>
        </div>

        <!-- Modal Nuevo Componente (Catalogo) -->
        <div v-if="mostrarNuevoCatalogo" class="fixed inset-0 flex items-center justify-center z-50">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cerrarNuevoCatalogo"></div>
          <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
            <h2 class="text-lg font-bold text-gray-100 mb-5">Registrar Nuevo Modelo de Componente</h2>
            <form @submit.prevent="guardarNuevoCatalogo" class="space-y-4">
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Categoría *</label>
                <select v-model="nuevoCatalogo.categoria" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                  <option value="">Selecciona una categoría</option>
                  <option value="Computadoras">Computadoras</option>
                  <option value="Servidores">Servidores</option>
                  <option value="Impresoras">Impresoras</option>
                  <option value="Monitores">Monitores</option>
                  <option value="Equipamiento de Red">Equipamiento de Red</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Marca *</label>
                <input v-model="nuevoCatalogo.marca" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" placeholder="Ej: Dell" />
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Modelo *</label>
                <input v-model="nuevoCatalogo.modelo" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" placeholder="Ej: OptiPlex 7090" />
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Precio Estimado (Bs)</label>
                <input v-model.number="nuevoCatalogo.precio" type="number" min="0" class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
              </div>
              <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="cerrarNuevoCatalogo" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-5 rounded-xl border border-gray-700 transition text-sm">Cancelar</button>
                <button type="submit" :disabled="loadingNuevoCatalogo" class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2 px-5 rounded-xl transition text-sm shadow-lg shadow-cyan-500/20 disabled:opacity-50">Guardar Modelo</button>
              </div>
              <div v-if="errorNuevoCatalogo" class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl text-sm mt-3">
                {{ errorNuevoCatalogo }}
              </div>
            </form>
          </div>
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

const route = useRouter().currentRoute.value

onMounted(async () => {
  if (!inventario.catalogo || inventario.catalogo.length === 0) {
    await inventario.fetchCatalogo()
  }
})

const categoriaSeleccionada = ref('')

const categoriasDisponibles = computed(() => {
  if (!inventario.catalogo) return []
  const cats = inventario.catalogo
    .filter(item => item.tipo_registro === 'Serializado' && item.categoria)
    .map(item => item.categoria)
  return [...new Set(cats)].sort()
})

const catalogosFiltrados = computed(() => {
  if (!inventario.catalogo) return []
  let list = inventario.catalogo.filter(item => item.tipo_registro === 'Serializado')
  if (categoriaSeleccionada.value) {
    list = list.filter(item => item.categoria === categoriaSeleccionada.value)
  }
  return list
})

const form = ref({
  catalogo_id: '',
  numero_serie: '',
  estado: 'En Almacén',
})

const loading = ref(false)
const error = ref('')

const mostrarNuevoCatalogo = ref(false)
const loadingNuevoCatalogo = ref(false)
const errorNuevoCatalogo = ref('')
const nuevoCatalogo = ref({
  categoria: '',
  marca: '',
  modelo: '',
  precio: '',
})

const abrirNuevoCatalogo = () => {
  errorNuevoCatalogo.value = ''
  nuevoCatalogo.value = {
    categoria: categoriaSeleccionada.value || '',
    marca: '',
    modelo: '',
    precio: '',
  }
  mostrarNuevoCatalogo.value = true
}

const cerrarNuevoCatalogo = () => {
  mostrarNuevoCatalogo.value = false
}

const guardarNuevoCatalogo = async () => {
  errorNuevoCatalogo.value = ''

  if (!nuevoCatalogo.value.categoria) {
    errorNuevoCatalogo.value = 'Debes seleccionar una categoría.'
    return
  }
  if (!nuevoCatalogo.value.marca?.trim()) {
    errorNuevoCatalogo.value = 'La marca es obligatoria.'
    return
  }
  if (!nuevoCatalogo.value.modelo?.trim()) {
    errorNuevoCatalogo.value = 'El modelo es obligatorio.'
    return
  }
  if (nuevoCatalogo.value.precio === '' || nuevoCatalogo.value.precio === null || Number(nuevoCatalogo.value.precio) <= 0) {
    errorNuevoCatalogo.value = 'El precio debe ser un número mayor a 0.'
    return
  }

  loadingNuevoCatalogo.value = true

  const data = {
    ...nuevoCatalogo.value,
    precio: Number(nuevoCatalogo.value.precio),
    tipo_registro: 'Serializado',
  }
  const result = await inventario.crear_catalogo(data)
  
  if (result.success) {
    mostrarNuevoCatalogo.value = false
    await inventario.fetchCatalogo()
    
    // Auto-select the newly created catalog item
    const newItem = inventario.catalogo.find(
      c => c.marca === data.marca && c.modelo === data.modelo && c.tipo_registro === 'Serializado'
    )
    if (newItem) {
      categoriaSeleccionada.value = newItem.categoria
      form.value.catalogo_id = newItem.id
    }
  } else {
    errorNuevoCatalogo.value = result.error || 'Error al crear modelo'
  }
  
  loadingNuevoCatalogo.value = false
}

const handleSubmit = async () => {
  loading.value = true
  error.value = ''

  const data = {
    ...form.value,
    creado_por: auth.user?.id,
  }

  const result = await inventario.crear_activoFijo(data)

  if (result.success) {
    router.push('/activos-fijos')
  } else {
    error.value = result.error
  }

  loading.value = false
}

</script>
