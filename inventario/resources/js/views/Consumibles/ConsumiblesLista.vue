<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-100">Consumibles</h1>
            <p class="text-gray-500 mt-1 text-sm">Gestión de insumos y repuestos</p>
          </div>

          <!-- Modal Nuevo Consumible (Catalogo) -->
          <div v-if="mostrarNuevoCatalogo" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cerrarNuevoCatalogo"></div>
            <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
              <h2 class="text-lg font-bold text-gray-100 mb-5">Registrar Nuevo Consumible</h2>
              <form @submit.prevent="guardarNuevoCatalogo" class="space-y-4">
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Marca *</label>
                  <input v-model="nuevoCatalogo.marca" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Modelo *</label>
                  <input v-model="nuevoCatalogo.modelo" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Categoría *</label>
                  <span class="w-full px-4 py-2.5 bg-gray-800/50 border border-gray-700 rounded-xl text-gray-400 block text-sm">Consumibles</span>
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Precio Unitario (Bs) *</label>
                  <input v-model.number="nuevoCatalogo.precio" type="number" min="0" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Tipo *</label>
                  <select v-model="nuevoCatalogo.tipo_registro" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                    <option value="Consumible">Consumible</option>
                  </select>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                  <button type="button" @click="cerrarNuevoCatalogo" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-5 rounded-xl border border-gray-700 transition text-sm">Cancelar</button>
                  <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-2 px-5 rounded-xl transition text-sm shadow-lg shadow-emerald-500/20">Guardar</button>
                </div>
              </form>
            </div>
          </div>
          <div class="flex gap-2">
            <router-link
              to="#"
              class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-2 px-5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/20"
              @click.prevent="abrirNuevoCatalogo"
            >
              + Nuevo Consumible
            </router-link>
            <button
              class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2 px-5 rounded-xl text-sm transition shadow-lg shadow-cyan-500/20"
              @click="abrirAgregarStock"
            >
              + Agregar Stock
            </button>
          </div>

          <!-- Modal Agregar Stock -->
          <div v-if="mostrarAgregarStock" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cerrarAgregarStock"></div>
            <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
              <h2 class="text-lg font-bold text-gray-100 mb-5">Agregar Stock a Consumible</h2>
              <form @submit.prevent="guardarAgregarStock" class="space-y-4">
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Consumible *</label>
                  <select v-model="stockForm.catalogo_id" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                    <option value="" disabled>Seleccione un consumible</option>
                    <option v-for="item in inventario.catalogo.filter(c => c.tipo_registro === 'Consumible')" :value="item.id">
                      {{ item.marca }} {{ item.modelo }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Cantidad a agregar *</label>
                  <input v-model.number="stockForm.cantidad_disponible" type="number" min="1" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
                </div>
                <div class="flex justify-end gap-3 pt-2">
                  <button type="button" @click="cerrarAgregarStock" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-5 rounded-xl border border-gray-700 transition text-sm">Cancelar</button>
                  <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2 px-5 rounded-xl transition text-sm shadow-lg shadow-cyan-500/20">Agregar</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Tabla de Consumibles -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800/40">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Marca</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Modelo</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cantidad</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60">
                <tr v-for="consumible in inventario.consumibles" :key="consumible.id" class="hover:bg-gray-800/30 transition">
                  <td class="px-6 py-4 text-sm text-gray-300">{{ consumible.catalogo?.marca }}</td>
                  <td class="px-6 py-4 text-sm text-gray-200 font-medium">{{ consumible.catalogo?.modelo }}</td>
                  <td class="px-6 py-4 text-sm text-gray-400">{{ consumible.catalogo?.categoria }}</td>
                  <td class="px-6 py-4 text-sm font-semibold">
                    <span :class="getStockClass(consumible.cantidad_disponible)">
                      {{ consumible.cantidad_disponible }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-300">Bs {{ consumible.catalogo?.precio }}</td>
                  <td class="px-6 py-4 text-sm">
                    <button @click="abrirEditar(consumible)" class="text-cyan-400 hover:text-cyan-300 font-medium text-xs px-3 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 transition">Editar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="inventario.consumibles.length === 0" class="text-center py-12 text-gray-600">
            No hay consumibles registrados
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useInventarioStore } from '../../stores/inventario.js'
import Navbar from '../../components/Navbar.vue'
import Sidebar from '../../components/Sidebar.vue'

const inventario = useInventarioStore()
const mostrarModal = ref(false)
const editando = ref(false)
const form = ref({
  catalogo_id: '',
  cantidad_disponible: '',
})
let consumibleEditId = null

const mostrarNuevoCatalogo = ref(false)
const nuevoCatalogo = ref({
  marca: '',
  modelo: '',
  categoria: '',
  precio: '',
  tipo_registro: 'Consumible',
})

// Agregar Stock
const mostrarAgregarStock = ref(false)
const stockForm = ref({
  catalogo_id: '',
  cantidad_disponible: '',
})

const abrirAgregarStock = () => {
  stockForm.value = { catalogo_id: '', cantidad_disponible: '' }
  mostrarAgregarStock.value = true
}
const cerrarAgregarStock = () => {
  mostrarAgregarStock.value = false
}
const guardarAgregarStock = async () => {
  const lote = inventario.consumibles.find(c => c.catalogo_id === stockForm.value.catalogo_id)
  if (lote) {
    const nuevaCantidad = lote.cantidad_disponible + stockForm.value.cantidad_disponible
    await inventario.actualizar_consumible(lote.id, {
      catalogo_id: lote.catalogo_id,
      cantidad_disponible: nuevaCantidad,
    })
  }
  mostrarAgregarStock.value = false
  await inventario.fetchConsumibles()
}

onMounted(async () => {
  await inventario.fetchConsumibles()
  await inventario.fetchCatalogo()
})

const getStockClass = (stock) => {
  if (stock > 20) return 'px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'
  if (stock > 5) return 'px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30'
  return 'px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30'
}

const abrirEditar = (consumible) => {
  editando.value = true
  consumibleEditId = consumible.id
  form.value = {
    catalogo_id: consumible.catalogo_id,
    cantidad_disponible: consumible.cantidad_disponible,
  }
  mostrarModal.value = true
}

const cerrarModal = () => {
  mostrarModal.value = false
}

const guardarConsumible = async () => {
  if (editando.value) {
    await inventario.actualizar_consumible(consumibleEditId, form.value)
  } else {
    await inventario.crear_consumible(form.value)
  }
  mostrarModal.value = false
  await inventario.fetchConsumibles()
}

const abrirNuevoCatalogo = () => {
  nuevoCatalogo.value = { marca: '', modelo: '', categoria: '', precio: '', tipo_registro: 'Consumible' }
  mostrarNuevoCatalogo.value = true
}
const cerrarNuevoCatalogo = () => {
  mostrarNuevoCatalogo.value = false
}
const guardarNuevoCatalogo = async () => {
  const data = {
    marca: nuevoCatalogo.value.marca,
    modelo: nuevoCatalogo.value.modelo,
    categoria: 'Consumibles',
    precio: nuevoCatalogo.value.precio,
    tipo_registro: 'Consumible',
  }
  await inventario.crear_catalogo(data)
  mostrarNuevoCatalogo.value = false
  await inventario.fetchCatalogo()
}
</script>
