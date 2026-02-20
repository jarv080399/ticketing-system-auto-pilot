<template>
    <div class="w-full space-y-12 pb-12">
        <div class="text-center space-y-4">
            <h1 class="text-5xl font-black tracking-tight text-text-main">
                How can we <span class="text-gradient">Help?</span>
            </h1>
            <p class="text-text-dim text-lg font-medium">Search our library of documentation and quick fixes.</p>
        </div>

        <!-- KB Search -->
        <div class="max-w-2xl mx-auto">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <svg class="h-6 w-6 text-text-dim group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="Search articles (e.g. WiFi setup, VPN reset)..."
                    class="w-full bg-surface-light border border-glass-border rounded-xl py-6 pl-16 pr-6 text-text-main placeholder:text-text-dim/50 focus:outline-none focus:ring-4 focus:ring-primary/20 focus:border-primary/50 transition-all shadow-2xl shadow-black/10"
                />
            </div>
        </div>

        <!-- KB Categories -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8">
            <div v-for="cat in kbCategories" :key="cat.name" class="glass-card p-8 rounded-xl hover-lift group">
                <div class="w-16 h-16 rounded-lg bg-surface-light flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                    {{ cat.icon }}
                </div>
                <h3 class="text-xl font-bold text-text-main mb-3">{{ cat.name }}</h3>
                <p class="text-text-dim text-sm leading-relaxed mb-6">{{ cat.desc }}</p>
                <div class="space-y-3">
                    <a v-for="article in cat.articles" :key="article" href="#" class="block text-sm font-medium text-primary hover:underline flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary/40"></span>
                        {{ article }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Help Banner -->
        <div class="glass-card p-12 rounded-xl bg-linear-to-r from-primary/20 to-secondary/20 border-white/10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-2">
                <h2 class="text-3xl font-black text-text-main">Can't find what you need?</h2>
                <p class="text-text-dim">Our agents are available 24/7 to assist you with complex issues.</p>
            </div>
            <router-link to="/tickets/new" class="px-10 py-5 bg-white text-primary font-black rounded-lg shadow-xl shadow-primary/20 hover-lift whitespace-nowrap">
                Contact Human Support
            </router-link>
        </div>
    </div>
</template>

<script setup>
const kbCategories = [
    {
        name: 'Getting Started',
        icon: '🚀',
        desc: 'New employee onboarding, hardware setup, and basic portal usage.',
        articles: ['Connect to Office VPN', 'Software Request Policy', 'Setting up MFA']
    },
    {
        name: 'Account & Access',
        icon: '🔐',
        desc: 'Password resets, permission management, and security protocols.',
        articles: ['Reset AD Password', 'Request Slack Access', 'Single Sign-On Guide']
    },
    {
        name: 'Technical Issues',
        icon: '🛠️',
        desc: 'Common hardware fixes, printer setup, and network troubleshooting.',
        articles: ['WiFi Connection Guide', 'Printer Driver Setup', 'Slow Performance Fixes']
    }
];
</script>
