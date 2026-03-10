import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const routes = [
        {
            path: '/usuarios',
            name: 'Usuarios',
            component: () => import('../views/Usuarios/UsuariosLista.vue'),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Auth/Login.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '/',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/catalogo',
        name: 'Catalogo',
        component: () => import('../views/Catalogo/CatalogoBusca.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/activos-fijos',
        name: 'ActivosFijos',
        component: () => import('../views/ActivosFijos/ActivosFijosLista.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/activos-fijos/nuevo',
        name: 'ActivoFijoNuevo',
        component: () => import('../views/ActivosFijos/ActivoFijoForm.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/consumibles',
        name: 'Consumibles',
        component: () => import('../views/Consumibles/ConsumiblesLista.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/consumibles/nuevo',
        name: 'ConsumibleNuevo',
        component: () => import('../views/Consumibles/ConsumibleForm.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/movimientos',
        name: 'Movimientos',
        component: () => import('../views/Kardex/KardexLista.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/movimientos/crear',
        name: 'MovimientoCrear',
        component: () => import('../views/Kardex/MovimientoForm.vue'),
        meta: { requiresAuth: true },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return '/login'
    } else if (to.meta.requiresAdmin && !auth.isAdmin) {
        return '/'
    } else if (to.path === '/login' && auth.isAuthenticated) {
        return '/'
    }
})

export default router
