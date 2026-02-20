<template>
    <div class="w-full py-8">
        <div class="relative flex justify-between">
            <!-- Background Line -->
            <div class="absolute top-1/2 left-0 w-full h-1 -translate-y-1/2 bg-surface-light rounded-full z-0"></div>
            
            <!-- Progress Line -->
            <div 
                class="absolute top-1/2 left-0 h-1 -translate-y-1/2 bg-primary rounded-full z-0 transition-all duration-1000"
                :style="{ width: progressWidth + '%' }"
            ></div>

            <!-- Steps -->
            <div 
                v-for="(step, index) in steps" :key="step.status"
                class="relative z-10 flex flex-col items-center gap-4 group"
            >
                <div 
                    class="w-10 h-10 rounded-full flex items-center justify-center border-4 transition-all duration-500"
                    :class="[
                        index <= currentStepIndex 
                            ? 'bg-primary border-primary shadow-lg shadow-primary/30 text-white' 
                            : 'bg-surface border-surface-light text-text-dim'
                    ]"
                >
                    <svg v-if="index < currentStepIndex" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span v-else class="text-xs font-bold">{{ index + 1 }}</span>
                </div>
                <div class="text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest" :class="index <= currentStepIndex ? 'text-text-main' : 'text-text-dim'">
                        {{ step.label }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    }
});

const steps = [
    { label: 'Submitted', status: 'new' },
    { label: 'Assigned', status: 'in_progress' },
    { label: 'Reviewing', status: 'waiting_on_customer' },
    { label: 'Resolved', status: 'resolved' },
    { label: 'Closed', status: 'closed' }
];

const currentStepIndex = computed(() => {
    const idx = steps.findIndex(s => s.status === props.status);
    return idx === -1 ? 0 : idx;
});

const progressWidth = computed(() => {
    return (currentStepIndex.value / (steps.length - 1)) * 100;
});
</script>
