<template>
  <div class="min-h-screen bg-gray-950">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <div class="flex-1 pl-64 pt-[73px] p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-100">Usuarios</h1>
            <p class="text-gray-500 mt-1 text-sm">Gestión de cuentas de usuario</p>
          </div>
          <button @click="abrirNuevo" class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2 px-5 rounded-xl text-sm transition shadow-lg shadow-cyan-500/20">
            + Nuevo Usuario
          </button>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800/40">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Turno</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60">
                <tr v-for="usuario in usuarios" :key="usuario.id" class="hover:bg-gray-800/30 transition">
                  <td class="px-6 py-4 text-sm text-gray-200 font-medium">{{ usuario.nombre_completo }}</td>
                  <td class="px-6 py-4 text-sm text-gray-400 font-mono">{{ usuario.username }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span :class="usuario.rol === 'Admin' ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30' : 'bg-amber-500/15 text-amber-400 border border-amber-500/30'" class="px-2.5 py-1 rounded-lg text-xs font-semibold">
                      {{ usuario.rol }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-400">{{ usuario.turno }}</td>
                  <td class="px-6 py-4 text-sm flex items-center gap-2">
                    <span :class="usuario.activo == 1 ? 'text-emerald-400' : 'text-rose-400'" class="font-semibold text-xs">
                      {{ usuario.activo == 1 ? '● Activo' : '● Inactivo' }}
                    </span>
                    <button @click="editarUsuario(usuario)" class="text-cyan-400 hover:text-cyan-300 font-medium text-xs px-2.5 py-1 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 transition">Editar</button>
                    <button v-if="usuario.activo == 1" @click="darDeBajaUsuario(usuario.id)" class="text-rose-400 hover:text-rose-300 font-medium text-xs px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition">Inhabilitar</button>
                    <button v-else @click="activarUsuario(usuario.id)" class="text-emerald-400 hover:text-emerald-300 font-medium text-xs px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition">Activar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="usuarios.length === 0" class="text-center py-12 text-gray-600">
            No hay usuarios registrados
          </div>
        </div>

        <!-- Modal Nuevo/Editar Usuario -->
        <div v-if="mostrarModal" class="fixed inset-0 flex items-center justify-center z-50">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cerrarModal"></div>
          <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-7 w-full max-w-md relative z-10">
            <h2 class="text-lg font-bold text-gray-100 mb-5">{{ editando ? 'Editar Usuario' : 'Nuevo Usuario' }}</h2>
            <form @submit.prevent="guardarUsuario" class="space-y-4">
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Nombre Completo *</label>
                <input v-model="form.nombre_completo" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Usuario *</label>
                <input v-model="form.username" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" />
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Contraseña *</label>
                <input v-model="form.password" type="password" :required="!editando" class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm" :placeholder="editando ? '********' : ''" />
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Rol *</label>
                <select v-model="form.rol" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                  <option value="Admin">Admin</option>
                  <option value="Tecnico">Tecnico</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wider">Turno *</label>
                <select v-model="form.turno" required class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:ring-1 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm">
                  <option value="Matutino">Matutino</option>
                  <option value="Vespertino">Vespertino</option>
                  <option value="Nocturno">Nocturno</option>
                </select>
              </div>
              <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="cerrarModal" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-5 rounded-xl border border-gray-700 transition text-sm">Cancelar</button>
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2 px-5 rounded-xl transition text-sm shadow-lg shadow-cyan-500/20">Guardar</button>
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
