<template>
    <div class="min-h-screen bg-background flex items-center justify-center p-4 relative overflow-hidden transition-all duration-700">
        <!-- Atmospheric Glows -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/10 rounded-full blur-[120px]"></div>

        <div class="w-full max-w-md z-10 space-y-8">
            <div class="text-center space-y-4">
                <router-link to="/login" class="inline-flex items-center gap-2 text-text-dim hover:text-primary transition-colors text-xs font-bold uppercase tracking-widest mb-4">
                    <span class="text-lg">←</span> Back to Login
                </router-link>
                <div class="space-y-1">
                    <h1 class="text-4xl font-black tracking-tight text-text-main">
                        Reset <span class="text-gradient">Password</span>
                    </h1>
                    <p class="text-text-dim text-sm font-medium tracking-wide">Enter your email to receive a recovery link</p>
                </div>
            </div>

            <div class="glass-card rounded-xl p-8 shadow-2xl relative">
                <form v-if="!submitted" @submit.prevent="handleForgot" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Email Address</label>
                        <div class="relative group">
                            <input 
                                v-model="email"
                                type="email" 
                                required
                                placeholder="name@company.com"
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
                            Sending...
                        </span>
                        <span v-else class="tracking-widest uppercase text-xs">Send Reset Link</span>
                    </button>
                </form>

                <div v-else class="text-center space-y-6 py-4">
                    <div class="w-20 h-20 bg-emerald-500/10 border border-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-emerald-500 text-3xl">✓</span>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold text-text-main">Email Sent!</h3>
                        <p class="text-text-dim text-sm font-medium">We've sent a password reset link to <span class="text-primary font-bold">{{ email }}</span>. Please check your inbox.</p>
                    </div>
                    <button @click="submitted = false" class="text-primary text-[10px] font-black uppercase tracking-widest hover:text-text-main transition-colors">
                        Didn't receive it? Try again
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const email = ref('');
const loading = ref(false);
const submitted = ref(false);
const error = ref('');

const handleForgot = async () => {
    error.value = '';
    loading.value = true;
    try {
        await authStore.forgotPassword(email.value);
        submitted.value = true;
    } catch (err) {
        error.value = err.response?.data?.message || 'Something went wrong. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>
