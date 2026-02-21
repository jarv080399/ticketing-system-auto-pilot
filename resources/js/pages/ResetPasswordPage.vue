<template>
    <div class="min-h-screen bg-background flex items-center justify-center p-4 relative overflow-hidden transition-all duration-700">
        <!-- Atmospheric Glows -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/10 rounded-full blur-[120px]"></div>

        <div class="w-full max-w-md z-10 space-y-8">
            <div class="text-center space-y-1">
                <h1 class="text-4xl font-black tracking-tight text-text-main">
                    New <span class="text-gradient">Password</span>
                </h1>
                <p class="text-text-dim text-sm font-medium tracking-wide">Enter your new secure password</p>
            </div>

            <div class="glass-card rounded-xl p-8 shadow-2xl relative">
                <form @submit.prevent="handleReset" class="space-y-6">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">New Password</label>
                            <input 
                                v-model="form.password"
                                type="password" 
                                required
                                placeholder="••••••••"
                                class="w-full px-5 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/50 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all duration-300"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Confirm Password</label>
                            <input 
                                v-model="form.password_confirmation"
                                type="password" 
                                required
                                placeholder="••••••••"
                                class="w-full px-5 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/50 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all duration-300"
                            />
                        </div>
                    </div>

                    <p v-if="error" class="text-red-400 text-xs font-bold text-center bg-red-500/10 py-3 rounded-xl border border-red-500/20">
                        {{ error }}
                    </p>

                    <button 
                        type="submit" 
                        :disabled="loading"
                        class="group relative w-full py-4 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white font-black rounded-lg shadow-xl shadow-primary/20 transition-all duration-300 active:scale-[0.98] overflow-hidden"
                    >
                        <span v-if="loading" class="flex items-center justify-center gap-2">
                            <span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            Updating...
                        </span>
                        <span v-else class="tracking-widest uppercase text-xs">Reset Password</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'vue-toastification';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const loading = ref(false);
const error = ref('');

const form = reactive({
    token: route.query.token || '',
    email: route.query.email || '',
    password: '',
    password_confirmation: '',
});

const handleReset = async () => {
    if (!form.token || !form.email) {
        error.value = 'Invalid reset attempt. Please use the link sent to your email.';
        return;
    }

    loading.value = true;
    error.value = '';
    try {
        await authStore.resetPassword(form);
        toast.success('Password updated! You can now log in.');
        router.push('/login');
    } catch (err) {
        error.value = err.response?.data?.message || 'Verification failed. Link may be expired.';
    } finally {
        loading.value = false;
    }
};
</script>
