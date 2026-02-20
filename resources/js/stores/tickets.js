import { defineStore } from 'pinia';
import axios from '@/plugins/axios';

export const useTicketStore = defineStore('tickets', {
    state: () => ({
        categories: [],
        myTickets: [],
        currentTicket: null,
        loading: false,
        error: null,
    }),

    actions: {
        async fetchCategories() {
            this.loading = true;
            try {
                const response = await axios.get('/categories');
                this.categories = response.data.data;
            } catch (err) {
                this.error = 'Failed to load categories';
            } finally {
                this.loading = false;
            }
        },

        async fetchMyTickets(params = {}) {
            this.loading = true;
            try {
                const response = await axios.get('/tickets/my-tickets', { params });
                this.myTickets = response.data.data;
                return response.data;
            } catch (err) {
                this.error = 'Failed to load tickets';
            } finally {
                this.loading = false;
            }
        },

        async fetchTicketByNumber(number) {
            this.loading = true;
            try {
                const response = await axios.get(`/tickets/${number}`);
                this.currentTicket = response.data.data;
            } catch (err) {
                this.error = 'Ticket not found';
            } finally {
                this.loading = false;
            }
        },

        async createTicket(formData) {
            this.loading = true;
            try {
                const response = await axios.post('/tickets', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                return response.data;
            } catch (err) {
                throw err.response?.data || { message: 'Failed to create ticket' };
            } finally {
                this.loading = false;
            }
        },

        async checkDuplicate(title) {
            try {
                const response = await axios.post('/tickets/check-duplicate', { title });
                return response.data;
            } catch (err) {
                return { is_duplicate: false };
            }
        }
    }
});
