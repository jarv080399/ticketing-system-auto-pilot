<template>
    <div class="min-h-screen bg-background flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">IT Helpdesk</h1>
                <p class="text-gray-400 mt-1">Sign in to your account</p>
            </div>

            <!-- Login Form -->
            <div class="bg-surface rounded-2xl p-8 border border-surface-light shadow-xl">
                <form @submit.prevent="handleLogin" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                        <input v-model="form.email" type="email" required autofocus
                            class="w-full px-4 py-2.5 bg-background border border-surface-light rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            placeholder="you@company.com" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                        <input v-model="form.password" type="password" required
                            class="w-full px-4 py-2.5 bg-background border border-surface-light rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            placeholder="••••••••" />
                    </div>

                    <div v-if="error" class="bg-danger/10 border border-danger/30 text-danger text-sm rounded-lg p-3">
                        {{ error }}
                    </div>

                    <button type="submit" :disabled="authStore.loading"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2.5 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ authStore.loading ? 'Signing in...' : 'Sign in' }}
                    </button>
                </form>

                <!-- Divider -->
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 border-t border-surface-light"></div>
                    <span class="text-xs text-gray-500 uppercase">or continue with</span>
                    <div class="flex-1 border-t border-surface-light"></div>
                </div>

                <!-- SSO Buttons -->
                <div class="space-y-3">
                    <a href="/api/v1/auth/sso/google/redirect"
                        class="flex items-center justify-center gap-3 w-full px-4 py-2.5 bg-background border border-surface-light rounded-lg text-gray-300 hover:text-white hover:border-gray-500 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/></svg>
                        Sign in with Google
                    </a>
                    <a href="/api/v1/auth/sso/microsoft/redirect"
                        class="flex items-center justify-center gap-3 w-full px-4 py-2.5 bg-background border border-surface-light rounded-lg text-gray-300 hover:text-white hover:border-gray-500 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M0 0h11.377v11.372H0zm12.623 0H24v11.372H12.623zM0 12.623h11.377V24H0zm12.623 0H24V24H12.623z"/></svg>
                        Sign in with Microsoft
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const router = useRouter();
const error = ref('');

const form = reactive({
    email: '',
    password: '',
});

const handleLogin = async () => {
    error.value = '';
    try {
        await authStore.login(form);
        const redirect = router.currentRoute.value.query.redirect || '/';
        router.push(redirect);
    } catch (err) {
        error.value = err.response?.data?.message || 'Invalid credentials. Please try again.';
    }
};
</script>
