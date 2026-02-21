<template>
    <div class="relative">
        <!-- Bell Icon -->
        <button 
            @click="isOpen = !isOpen"
            class="relative p-2.5 rounded-xl bg-surface-light border border-glass-border text-text-dim hover:text-primary hover:border-primary/50 transition-all duration-300 active:scale-95"
            :class="{ 'text-primary border-primary/50': isOpen }"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v1m6 0H9" />
            </svg>
            
            <!-- Badge -->
            <span v-if="store.unreadCount > 0" class="absolute top-1 right-1 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-primary text-[10px] items-center justify-center text-white font-black">
                    {{ store.unreadCount > 9 ? '9+' : store.unreadCount }}
                </span>
            </span>
        </button>

        <!-- Dropdown Panel -->
        <transition 
            enter-active-class="transition duration-100 ease-out" 
            enter-from-class="transform scale-95 opacity-0" 
            enter-to-class="transform scale-100 opacity-100" 
            leave-active-class="transition duration-75 ease-in" 
            leave-from-class="transform scale-100 opacity-100" 
            leave-to-class="transform scale-95 opacity-0"
        >
            <div v-if="isOpen" class="absolute right-0 mt-4 w-96 glass-card rounded-2xl shadow-2xl overflow-hidden ring-1 ring-white/10 z-[100]">
                <!-- Header -->
                <div class="px-6 py-4 bg-surface-light/50 border-b border-white/5 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-text-main uppercase tracking-widest">Intelligence Feed</h3>
                        <p class="text-[10px] font-bold text-text-dim uppercase tracking-[0.2em] mt-0.5">{{ store.unreadCount }} Unread Alerts</p>
                    </div>
                    <button 
                        @click="store.markAllAsRead"
                        class="text-[10px] font-black uppercase tracking-widest text-primary hover:text-primary-dark transition-colors"
                    >
                        Mark All Read
                    </button>
                </div>

                <!-- List -->
                <div class="max-h-[450px] overflow-y-auto custom-scrollbar">
                    <div v-if="store.notifications.length === 0" class="py-12 flex flex-col items-center justify-center opacity-50">
                        <span class="text-4xl mb-4">📭</span>
                        <p class="text-xs font-bold uppercase tracking-widest">All systems clear</p>
                    </div>
                    
                    <div 
                        v-for="notification in store.notifications" 
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        class="p-6 border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer group"
                        :class="{ 'bg-primary/5': !notification.read_at }"
                    >
                        <div class="flex gap-4">
                            <!-- Icon/Avatar based on type -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-surface-light flex items-center justify-center border border-white/5 group-hover:scale-110 transition-transform">
                                <span v-if="notification.data.type?.includes('TicketCreated')" class="text-xl">🎫</span>
                                <span v-else-if="notification.data.type?.includes('TicketAssigned')" class="text-xl">👤</span>
                                <span v-else-if="notification.data.type?.includes('SlaWarning')" class="text-xl">🚨</span>
                                <span v-else class="text-xl">🔔</span>
                            </div>
                            
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-primary">
                                        {{ notification.data.ticket_number || 'SYSTEM' }}
                                    </p>
                                    <span class="text-[9px] font-bold text-text-dim uppercase tracking-widest">
                                        {{ formatTime(notification.created_at) }}
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-text-main group-hover:text-primary transition-colors">
                                    {{ notification.data.title }}
                                </p>
                                <p class="text-[11px] text-text-dim leading-relaxed line-clamp-2">
                                    {{ notification.data.message }}
                                </p>
                            </div>

                            <!-- Read Indicator -->
                            <div v-if="!notification.read_at" class="w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-4 bg-surface-light/30 border-t border-white/5">
                    <router-link 
                        to="/settings/notifications" 
                        class="w-full py-3 flex items-center justify-center gap-2 rounded-xl bg-surface-light border border-white/5 text-[10px] font-black uppercase tracking-widest text-text-dim hover:text-text-main hover:bg-white/10 transition-all"
                    >
                        ⚙️ Global Preferences
                    </router-link>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useNotificationsStore } from '@/stores/notifications';
import { formatDistanceToNow } from 'date-fns';
import { useRouter } from 'vue-router';

const store = useNotificationsStore();
const isOpen = ref(false);
const router = useRouter();

onMounted(() => {
    store.fetchNotifications();
    store.listen();
});

const formatTime = (date) => {
    return formatDistanceToNow(new Date(date), { addSuffix: true });
};

const handleNotificationClick = async (notification) => {
    if (!notification.read_at) {
        await store.markAsRead(notification.id);
    }
    
    // Auto-navigate based on ticket number
    if (notification.data.ticket_number) {
        const path = router.currentRoute.value.path.includes('/agent') 
            ? `/agent/tickets/${notification.data.ticket_number}` 
            : `/tickets/${notification.data.ticket_number}`;
        router.push(path);
    }
    
    isOpen.value = false;
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
</style>
