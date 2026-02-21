<template>
    <router-view />
</template>

<script setup>
import { onMounted } from 'vue';
import { useThemeStore } from '@/stores/theme';

onMounted(() => {
    // Initialize the theme immediately upon application load,
    // ensuring the dark/light class is applied even when not in AppLayout.
    useThemeStore();
});
</script>
