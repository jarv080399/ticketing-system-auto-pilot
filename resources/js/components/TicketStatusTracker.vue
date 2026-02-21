<template>
    <div class="w-full py-12 px-4">
        <div class="relative flex justify-between items-center max-w-5xl mx-auto">
            <!-- ── Background Track ── -->
            <div class="absolute top-6 left-0 w-full h-[2px] bg-glass-border/40 rounded-full z-0"></div>
            
            <!-- ── Active Progress Line (Gradient + Glow) ── -->
            <div 
                class="absolute top-6 left-0 h-[2.5px] bg-linear-to-r from-primary via-primary-light to-secondary-light rounded-full z-0 transition-all duration-1000 ease-out"
                :style="{ width: progressWidth + '%' }"
            >
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-primary rounded-full blur-md opacity-50"></div>
            </div>

            <!-- ── Steps ── -->
            <div 
                v-for="(step, index) in steps" :key="step.status"
                class="relative z-10 flex flex-col items-center gap-5 group"
            >
                <!-- Step Circle -->
                <div 
                    class="w-12 h-12 rounded-2xl flex items-center justify-center border-2 transition-all duration-500 relative cursor-default"
                    :class="[
                        index < currentStepIndex 
                            ? 'bg-primary/10 border-primary/40 text-primary' 
                            : index === currentStepIndex
                                ? 'bg-primary border-primary shadow-[0_0_20px_rgba(var(--color-primary-rgb),0.4)] text-white scale-110'
                                : 'bg-surface-light/40 border-glass-border text-text-dim'
                    ]"
                >
                    <!-- Pulse for active step -->
                    <div v-if="index === currentStepIndex" class="absolute inset-0 rounded-2xl bg-primary animate-ping opacity-20"></div>

                    <svg v-if="index < currentStepIndex" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span v-else class="text-sm font-black tracking-tighter">{{ index + 1 }}</span>
                </div>

                <!-- Label -->
                <div class="space-y-1 text-center">
                    <p 
                        class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors duration-300" 
                        :class="index <= currentStepIndex ? 'text-text-main' : 'text-text-dim/40'"
                    >
                        {{ step.label }}
                    </p>
                    <div 
                        v-if="index === currentStepIndex" 
                        class="h-1 w-4 bg-primary mx-auto rounded-full animate-pulse"
                    ></div>
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
    // We want the line to go from center of step 1 to center of current step
    return (currentStepIndex.value / (steps.length - 1)) * 100;
});
</script>

<style scoped>
@keyframes ping {
    75%, 100% {
        transform: scale(1.6);
        opacity: 0;
    }
}
</style>
