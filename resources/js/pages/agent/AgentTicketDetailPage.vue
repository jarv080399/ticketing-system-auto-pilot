<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Ticket #{{ id }}</h1>
        <p class="text-gray-400">Agent ticket detail view.</p>
    </div>
</template>
<script setup>
defineProps({ id: String });
</script>
