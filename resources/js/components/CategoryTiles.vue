<template>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <button 
            v-for="category in categories" 
            :key="category.id"
            @click="$emit('select', category)"
            class="glass-card p-6 rounded-xl hover-lift group text-left border-transparent hover:border-primary/50 transition-all duration-300"
            :class="{ 'ring-2 ring-primary bg-primary/5': selectedId === category.id }"
        >
            <div class="w-14 h-14 rounded-lg bg-surface-light flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">
                {{ category.icon || '❓' }}
            </div>
            <h3 class="font-bold text-text-main text-lg mb-1">{{ category.name }}</h3>
            <p class="text-sm text-text-dim line-clamp-2">{{ category.description }}</p>
        </button>
    </div>
</template>

<script setup>
defineProps({
    categories: {
        type: Array,
        required: true
    },
    selectedId: {
        type: Number,
        default: null
    }
});

defineEmits(['select']);
</script>
