<template>
    <div class="relative space-y-10 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-[2px] before:bg-glass-border">
        <div v-for="item in history" :key="item.id" class="relative pl-12 group">
            <!-- Icon Dot -->
            <div class="absolute left-0 top-1 w-6 h-6 rounded-full border-2 border-surface bg-surface-light flex items-center justify-center transition-all group-hover:scale-110 z-10" :class="getIconBg(item.action)">
                <span class="text-[10px]">{{ getActionIcon(item.action) }}</span>
            </div>
            
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <p class="text-sm font-black text-text-main tracking-tight uppercase">{{ formatAction(item.action) }}</p>
                    <span class="w-1 h-1 rounded-full bg-glass-border"></span>
                    <p class="text-[10px] text-text-dim font-bold">{{ formatDate(item.created_at) }}</p>
                </div>
                
                <p class="text-xs text-text-dim leading-relaxed">
                    Performed by <span class="text-text-main font-bold">{{ item.performer?.name || 'System' }}</span>
                </p>
                
                <div v-if="item.old_value || item.new_value" class="mt-3 p-4 rounded-xl bg-surface-light border border-glass-border flex flex-col gap-2">
                    <div v-if="item.old_value" class="flex items-center gap-3">
                        <span class="text-[10px] text-rose-500 font-black uppercase tracking-widest w-12">From</span>
                        <code class="text-[10px] text-text-dim truncate">{{ item.old_value }}</code>
                    </div>
                    <div v-if="item.new_value" class="flex items-center gap-3">
                        <span class="text-[10px] text-emerald-500 font-black uppercase tracking-widest w-12">To</span>
                        <code class="text-[10px] text-text-main truncate">{{ item.new_value }}</code>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="!history || history.length === 0" class="pl-12 py-4">
            <p class="text-sm text-text-dim italic">No recorded history for this asset yet.</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    history: {
        type: Array,
        required: true
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};

const formatAction = (action) => {
    return action.replace('_', ' ');
};

const getActionIcon = (action) => {
    const icons = {
        assigned: '👤',
        unassigned: '🔓',
        status_changed: '🔄',
        edited: '✏️',
        created: '🆕'
    };
    return icons[action] || '📄';
};

const getIconBg = (action) => {
    const classes = {
        assigned: 'border-primary/50 text-primary',
        unassigned: 'border-rose-500/50 text-rose-500',
        status_changed: 'border-amber-500/50 text-amber-500',
        edited: 'border-blue-500/50 text-blue-500',
        created: 'border-emerald-500/50 text-emerald-500'
    };
    return classes[action] || 'border-glass-border';
};
</script>
