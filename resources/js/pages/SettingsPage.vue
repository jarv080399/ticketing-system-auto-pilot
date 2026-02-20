<template>
    <div class="max-w-4xl mx-auto space-y-12">
        <h1 class="text-4xl font-black text-text-main">Account <span class="text-gradient">Settings</span></h1>
        
        <div class="glass-card p-12 rounded-xl text-center space-y-6">
            <div class="w-24 h-24 bg-surface-light rounded-full flex items-center justify-center mx-auto text-5xl">⚙️</div>
            <div class="space-y-2">
                <h3 class="text-2xl font-black text-text-main">Under Maintenance</h3>
                <p class="text-text-dim">Settings and profile management are currently being upgraded for better security.</p>
            </div>
            <router-link to="/" class="inline-block px-10 py-4 bg-primary text-white font-black rounded-lg shadow-xl shadow-primary/20 hover-lift">Return Home</router-link>
        </div>
    </div>
</template>
