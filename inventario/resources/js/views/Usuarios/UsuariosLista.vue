<template>
  <div>
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-72 p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Usuarios</h1>
            <p class="text-gray-600 mt-2">Gestión de cuentas de usuario</p>
          </div>
          <button @click="abrirNuevo" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
            + Nuevo Usuario
          </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Usuario</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rol</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Turno</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="usuario in usuarios" :key="usuario.id" class="border-b hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-900">{{ usuario.nombre_completo }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ usuario.username }}</td>
                  <td class="px-6 py-4 text-sm">{{ usuario.rol }}</td>
                  <td class="px-6 py-4 text-sm">{{ usuario.turno }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span :class="usuario.activo == 1 ? 'text-green-600 font-bold' : 'text-red-600 font-bold'">
                      {{ usuario.activo == 1 ? 'Activo' : 'Inactivo' }}
                    </span>
                    <button @click="editarUsuario(usuario)" class="text-blue-600 hover:underline mx-2">Editar</button>
                    <button v-if="usuario.activo == 1" @click="darDeBajaUsuario(usuario.id)" class="text-red-600 hover:underline">Inhabilitar</button>
                    <button v-else @click="activarUsuario(usuario.id)" class="text-green-600 hover:underline">Activar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="usuarios.length === 0" class="text-center py-8 text-gray-500">
            No hay usuarios registrados
          </div>
        </div>

        <!-- Modal Nuevo/Editar Usuario -->
        <div v-if="mostrarModal" class="fixed inset-0 flex items-center justify-center z-50" style="z-index:9999">
          <div class="absolute inset-0 pointer-events-none" style="background: rgba(0,0,0,0.08); z-index:9998;"></div>
          <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative" style="z-index:9999;">
            <h2 class="text-xl font-bold mb-4">{{ editando ? 'Editar Usuario' : 'Nuevo Usuario' }}</h2>
            <form @submit.prevent="guardarUsuario">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Nombre Completo *</label>
                <input v-model="form.nombre_completo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Usuario *</label>
                <input v-model="form.username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Contraseña *</label>
                <input v-model="form.password" type="password" :required="!editando" class="w-full px-4 py-2 border border-gray-300 rounded-lg" :placeholder="editando ? '********' : ''" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Rol *</label>
                <select v-model="form.rol" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                  <option value="Admin">Admin</option>
                  <option value="Tecnico">Tecnico</option>
                </select>
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Turno *</label>
                <select v-model="form.turno" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                  <option value="Matutino">Matutino</option>
                  <option value="Vespertino">Vespertino</option>
                  <option value="Nocturno">Nocturno</option>
                </select>
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" @click="cerrarModal" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '../../components/Navbar.vue'
import Sidebar from '../../components/Sidebar.vue'
import client from '../../api/client.js'

const usuarios = ref([])
const mostrarModal = ref(false)
const editando = ref(false)
const form = ref({
  nombre_completo: '',
  username: '',
  password: '',
  rol: 'Tecnico',
  turno: ''
})
let usuarioEditId = null

const cargarUsuarios = async () => {
  const res = await client.get('/usuarios')
  usuarios.value = res.data.data || []
}

onMounted(cargarUsuarios)

const abrirNuevo = () => {
  editando.value = false
  usuarioEditId = null
  form.value = { nombre_completo: '', username: '', password: '', rol: 'Tecnico', turno: '' }
  mostrarModal.value = true
}

const editarUsuario = (usuario) => {
  editando.value = true
  usuarioEditId = usuario.id
  form.value = { ...usuario, password: '********' }
  mostrarModal.value = true
}

const cerrarModal = () => {
  mostrarModal.value = false
}

const guardarUsuario = async () => {
  if (editando.value) {
    await client.put(`/usuarios/${usuarioEditId}`, form.value)
  } else {
    await client.post('/usuarios', form.value)
  }
  mostrarModal.value = false
  cargarUsuarios()
}

const darDeBajaUsuario = async (id) => {
  if (confirm('¿Seguro que deseas inhabilitar este usuario?')) {
    await client.delete(`/usuarios/${id}`)
    cargarUsuarios()
  }
}

const activarUsuario = async (id) => {
  if (confirm('¿Seguro que deseas activar este usuario?')) {
    await client.put(`/usuarios/${id}`, { activo: true })
    cargarUsuarios()
  }
}
</script>
