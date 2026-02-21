import { defineStore } from 'pinia';
import axios from '@/plugins/axios';
import { useAuthStore } from './auth';

export const useNotificationsStore = defineStore('notifications', {
    state: () => ({
        notifications: [],
        unreadCount: 0,
        loading: false,
    }),

    actions: {
        async fetchNotifications() {
            this.loading = true;
            try {
                // Laravel's built-in notifications are usually queried via /api/user/notifications
                // We'll assume a standard route or create one.
                const response = await axios.get('/user/notifications');
                this.notifications = response.data.data;
                this.unreadCount = response.data.unread_count;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        async markAsRead(id) {
            try {
                await axios.post(`/user/notifications/${id}/read`);
                const notification = this.notifications.find(n => n.id === id);
                if (notification && !notification.read_at) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount--;
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await axios.post('/user/notifications/read-all');
                this.notifications.forEach(n => n.read_at = n.read_at || new Date().toISOString());
                this.unreadCount = 0;
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        listen() {
            const auth = useAuthStore();
            if (!auth.user) return;

            // Listen for Laravel's broadcast notifications
            window.Echo.private(`App.Models.User.${auth.user.id}`)
                .notification((notification) => {
                    this.notifications.unshift({
                        id: notification.id,
                        data: notification,
                        created_at: new Date().toISOString(),
                        read_at: null
                    });
                    this.unreadCount++;
                });
        }
    }
});
