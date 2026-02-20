<template>
    <div class="min-h-screen bg-background flex items-center justify-center p-4 relative overflow-hidden transition-all duration-700">
        <!-- ─── Atmospheric Glows ─── -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>

        <div class="w-full max-w-md z-10 space-y-8">
            <!-- Logo Section -->
            <div class="text-center space-y-4">
                <div class="inline-flex w-16 h-16 bg-linear-to-br from-primary to-secondary rounded-lg items-center justify-center shadow-2xl shadow-primary/30 animate-bounce-slow">
                    <span class="text-white font-black text-2xl tracking-tighter">IT</span>
                </div>
                <div class="space-y-1">
                    <h1 class="text-4xl font-black tracking-tight text-text-main">
                        Welcome <span class="text-gradient">Back</span>
                    </h1>
                    <p class="text-text-dim text-sm font-medium tracking-wide">Next-Gen IT Management & Support</p>
                </div>
            </div>

            <!-- Login Card -->
            <div class="glass-card rounded-xl p-8 shadow-2xl relative">
                <form @submit.prevent="handleLogin" class="space-y-6">
                    <div class="space-y-4">
                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Email Address</label>
                            <div class="relative group">
                                <input 
                                    v-model="form.email"
                                    type="email" 
                                    required
                                    placeholder="name@company.com"
                                    class="w-full px-5 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/50 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all duration-300"
                                />
                                <div class="absolute inset-0 rounded-lg bg-primary/5 opacity-0 group-focus-within:opacity-100 pointer-events-none transition-opacity"></div>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center ml-1">
                                <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em]">Password</label>
                                <a href="#" class="text-[10px] font-bold text-primary hover:text-text-main transition-colors uppercase tracking-widest">Forgot?</a>
                            </div>
                            <div class="relative group">
                                <input 
                                    v-model="form.password"
                                    type="password" 
                                    required
                                    placeholder="••••••••"
                                    class="w-full px-5 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/50 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all duration-300"
                                />
                                <div class="absolute inset-0 rounded-lg bg-primary/5 opacity-0 group-focus-within:opacity-100 pointer-events-none transition-opacity"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Error State -->
                    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform -translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100">
                        <p v-if="error" class="text-red-400 text-xs font-bold text-center bg-red-500/10 py-3 rounded-xl border border-red-500/20">
                            {{ error }}
                        </p>
                    </transition>

                    <button 
                        type="submit" 
                        :disabled="authStore.loading"
                        class="group relative w-full py-4 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white font-black rounded-lg shadow-xl shadow-primary/20 transition-all duration-300 hover-lift active:scale-[0.98] overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        <span v-if="authStore.loading" class="flex items-center justify-center gap-2">
                            <span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            Validating...
                        </span>
                        <span v-else class="tracking-widest uppercase text-xs">Authorize & Sign In</span>
                    </button>
                </form>

                <!-- SSO Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-glass-border"></div></div>
                    <div class="relative flex justify-center text-[10px] uppercase font-black tracking-[0.3em] text-text-dim">
                        <span class="bg-surface px-4 py-1 rounded-full border border-glass-border">Identity Providers</span>
                    </div>
                </div>

                <!-- SSO Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <button class="flex items-center justify-center gap-3 py-3.5 px-4 bg-surface-light/50 border border-glass-border rounded-lg hover:bg-surface-light hover-lift active:scale-95 transition-all duration-300">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                        <span class="text-[11px] font-bold text-text-main uppercase tracking-wider">Google</span>
                    </button>
                    <button class="flex items-center justify-center gap-3 py-3.5 px-4 bg-surface-light/50 border border-glass-border rounded-lg hover:bg-surface-light hover-lift active:scale-95 transition-all duration-300">
                        <img src="https://www.svgrepo.com/show/333558/microsoft.svg" class="w-5 h-5" alt="Microsoft">
                        <span class="text-[11px] font-bold text-text-main uppercase tracking-wider">Azure</span>
                    </button>
                </div>
            </div>

            <p class="text-center text-text-dim text-xs font-medium">
                Protected by enterprise-grade <span class="text-primary font-bold">SHA-256</span> encryption
            </p>
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
        const success = await authStore.login(form);
        if (success) {
            let redirect = router.currentRoute.value.query.redirect;
            
            if (!redirect) {
                redirect = ['agent', 'admin'].includes(authStore.user.role) ? '/agent' : '/';
            }
            
            router.push(redirect);
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Invalid credentials. Please try again.';
    }
};
</script>

<style scoped>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
    animation: bounce-slow 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
</style>
