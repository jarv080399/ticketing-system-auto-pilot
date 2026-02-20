import { defineStore } from 'pinia';
import api from '@/plugins/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('auth_user') || 'null'),
        token: localStorage.getItem('auth_token') || null,
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAgent: (state) => ['agent', 'admin'].includes(state.user?.role),
        isAdmin: (state) => state.user?.role === 'admin',
        fullName: (state) => state.user?.name || '',
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            try {
                const { data } = await api.post('/auth/login', credentials);
                this.setAuth(data.data.user, data.data.token);
                return data;
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            try {
                const { data } = await api.get('/auth/me');
                this.user = data.data;
                localStorage.setItem('auth_user', JSON.stringify(data.data));
            } catch {
                this.logout();
            }
        },

        async logout() {
            try {
                await api.post('/auth/logout');
            } catch {
                // Ignore errors on logout
            } finally {
                this.clearAuth();
            }
        },

        setAuth(user, token) {
            this.user = user;
            this.token = token;
            localStorage.setItem('auth_token', token);
            localStorage.setItem('auth_user', JSON.stringify(user));
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
        },
    },
});
