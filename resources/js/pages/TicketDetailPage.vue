<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Ticket {{ ticketNumber }}</h1>
        <p class="text-gray-400">Ticket details will appear here.</p>
    </div>
</template>

<script setup>
defineProps({ ticketNumber: String });
// Stub — will be implemented in Module 2/3
</script>
