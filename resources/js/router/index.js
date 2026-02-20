import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    // ─── Public Routes ───
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/pages/LoginPage.vue'),
        meta: { guest: true },
    },

    // ─── Authenticated Routes ───
    {
        path: '/',
        component: () => import('@/layouts/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('@/pages/DashboardPage.vue'),
            },
            {
                path: 'tickets',
                name: 'MyTickets',
                component: () => import('@/pages/MyTicketsPage.vue'),
            },
            {
                path: 'tickets/new',
                name: 'NewTicket',
                component: () => import('@/pages/NewTicketPage.vue'),
            },
            {
                path: 'tickets/:ticketNumber',
                name: 'TicketDetail',
                component: () => import('@/pages/TicketDetailPage.vue'),
                props: true,
            },
            {
                path: 'kb',
                name: 'KnowledgeBase',
                component: () => import('@/pages/KnowledgeBasePage.vue'),
            },
        ],
    },

    // ─── Agent Routes ───
    {
        path: '/agent',
        component: () => import('@/layouts/AgentLayout.vue'),
        meta: { requiresAuth: true, role: 'agent' },
        children: [
            {
                path: '',
                name: 'AgentDashboard',
                component: () => import('@/pages/agent/AgentDashboardPage.vue'),
            },
            {
                path: 'tickets/:id',
                name: 'AgentTicketDetail',
                component: () => import('@/pages/agent/AgentTicketDetailPage.vue'),
                props: true,
            },
        ],
    },

    // ─── Admin Routes ───
    {
        path: '/admin',
        component: () => import('@/layouts/AdminLayout.vue'),
        meta: { requiresAuth: true, role: 'admin' },
        children: [
            {
                path: '',
                name: 'AdminDashboard',
                component: () => import('@/pages/admin/AdminDashboardPage.vue'),
            },
            {
                path: 'settings',
                name: 'AdminSettings',
                component: () => import('@/pages/admin/SettingsPage.vue'),
            },
        ],
    },

    // ─── 404 ───
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/pages/NotFoundPage.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// ─── Navigation Guards ───
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next({ name: 'Login', query: { redirect: to.fullPath } });
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        return next({ name: 'Dashboard' });
    }

    if (to.meta.role && authStore.user?.role !== to.meta.role && authStore.user?.role !== 'admin') {
        return next({ name: 'Dashboard' });
    }

    next();
});

export default router;
