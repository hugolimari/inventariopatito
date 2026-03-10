<template>
  <div>
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-72 p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Consumibles</h1>
            <p class="text-gray-600 mt-2">Gestión de insumos y repuestos</p>
          </div>
          <!-- Modal Nuevo Consumible (Catalogo) -->
          <div v-if="mostrarNuevoCatalogo" class="fixed inset-0 flex items-center justify-center z-50" style="z-index:9999">
            <div class="absolute inset-0 pointer-events-none" style="background: rgba(0,0,0,0.08); z-index:9998;"></div>
            <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative" style="z-index:9999;">
              <h2 class="text-xl font-bold mb-4">Registrar Nuevo Consumible</h2>
              <form @submit.prevent="guardarNuevoCatalogo">
                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Marca *</label>
                  <input v-model="nuevoCatalogo.marca" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Modelo *</label>
                  <input v-model="nuevoCatalogo.modelo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Categoría *</label>
                  <span class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 block">Consumibles</span>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Precio Unitario (Bs) *</label>
                  <input v-model.number="nuevoCatalogo.precio" type="number" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Tipo *</label>
                  <select v-model="nuevoCatalogo.tipo_registro" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="Consumible">Consumible</option>
                  </select>
                </div>
                <div class="flex justify-end gap-2">
                  <button type="button" @click="cerrarNuevoCatalogo" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cancelar</button>
                  <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">Guardar</button>
                </div>
              </form>
            </div>
          </div>
          <div class="flex gap-2">
            <router-link
              to="#"
              class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg"
              @click.prevent="abrirNuevoCatalogo"
            >
              + Nuevo Consumible
            </router-link>
            <button
              class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg"
              @click="abrirAgregarStock"
            >
              + Agregar Stock
            </button>
          </div>
                  <!-- Modal Agregar Stock -->
                  <div v-if="mostrarAgregarStock" class="fixed inset-0 flex items-center justify-center z-50" style="z-index:9999">
                    <div class="absolute inset-0 pointer-events-none" style="background: rgba(0,0,0,0.08); z-index:9998;"></div>
                    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative" style="z-index:9999;">
                      <h2 class="text-xl font-bold mb-4">Agregar Stock a Consumible</h2>
                      <form @submit.prevent="guardarAgregarStock">
                        <div class="mb-4">
                          <label class="block text-gray-700 font-semibold mb-2">Consumible *</label>
                          <select v-model="stockForm.catalogo_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="" disabled>Seleccione un consumible</option>
                            <option v-for="item in inventario.catalogo.filter(c => c.tipo_registro === 'Consumible')" :value="item.id">
                              {{ item.marca }} {{ item.modelo }}
                            </option>
                          </select>
                        </div>
                        <div class="mb-4">
                          <label class="block text-gray-700 font-semibold mb-2">Cantidad a agregar *</label>
                          <input v-model.number="stockForm.cantidad_disponible" type="number" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                        </div>
                        <div class="flex justify-end gap-2">
                          <button type="button" @click="cerrarAgregarStock" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cancelar</button>
                          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Agregar</button>
                        </div>
                      </form>
                    </div>
                  </div>
        </div>

        <!-- Tabla de Consumibles -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Marca</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Modelo</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Categoría</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cantidad</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Precio Unit.</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="consumible in inventario.consumibles" :key="consumible.id" class="border-b hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-900">{{ consumible.catalogo?.marca }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ consumible.catalogo?.modelo }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ consumible.catalogo?.categoria }}</td>
                  <td class="px-6 py-4 text-sm font-semibold">
                    <span :class="getStockClass(consumible.cantidad_disponible)">
                      {{ consumible.cantidad_disponible }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900">Bs {{ consumible.catalogo?.precio }}</td>
                  <td class="px-6 py-4 text-sm">
                    <button @click="abrirEditar(consumible)" class="text-blue-600 hover:text-blue-900 font-semibold">Editar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="inventario.consumibles.length === 0" class="text-center py-8 text-gray-500">
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
  // Buscar lote existente
  const lote = inventario.consumibles.find(c => c.catalogo_id === stockForm.value.catalogo_id)
  if (lote) {
    // Sumar cantidad y actualizar
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
  if (stock > 20) return 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800'
  if (stock > 5) return 'px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800'
  return 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800'
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
