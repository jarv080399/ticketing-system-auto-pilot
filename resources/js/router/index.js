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
            {
                path: 'kb/:slug',
                name: 'KbArticleViewer',
                component: () => import('@/pages/KbArticleViewerPage.vue'),
            },
            {
                path: 'settings',
                name: 'Settings',
                component: () => import('@/pages/SettingsPage.vue'),
            },
            {
                path: 'settings/notifications',
                name: 'NotificationSettings',
                component: () => import('@/pages/NotificationSettingsPage.vue'),
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
                component: () => import('@/pages/AgentDashboardPage.vue'),
            },
            {
                path: 'queue',
                name: 'AgentQueue',
                component: () => import('@/pages/AgentQueuePage.vue'),
            },
            {
                path: 'tickets/:ticketNumber',
                name: 'AgentTicketDetail',
                component: () => import('@/pages/AgentTicketDetailPage.vue'),
                props: true,
            },
            {
                path: 'kb/new',
                name: 'AgentNewArticle',
                component: () => import('@/pages/AgentArticleEditorPage.vue'),
            },
            {
                path: 'kb/edit/:slug',
                name: 'AgentEditArticle',
                component: () => import('@/pages/AgentArticleEditorPage.vue'),
            },
            {
                path: 'assets',
                name: 'AssetRegistry',
                component: () => import('@/pages/assets/AssetIndexPage.vue'),
            },
            {
                path: 'assets/:id',
                name: 'AssetDetail',
                component: () => import('@/pages/assets/AssetDetailPage.vue'),
            },
            {
                path: 'stats',
                name: 'AgentStats',
                component: () => import('@/pages/admin/ManagerDashboardPage.vue'),
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
            {
                path: 'automation',
                name: 'AdminAutomation',
                component: () => import('@/pages/AdminAutomationPage.vue'),
            },
            {
                path: 'dashboard',
                name: 'ManagerDashboard',
                component: () => import('@/pages/admin/ManagerDashboardPage.vue'),
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
