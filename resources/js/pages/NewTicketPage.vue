<template>
    <div class="w-full space-y-12 pb-12">
        <!-- Header -->
        <div class="space-y-4">
            <h1 class="text-4xl font-black tracking-tight text-text-main">
                Create New <span class="text-gradient">Request</span>
            </h1>
            <p class="text-text-dim font-medium">Choose a category that best describes your issue to get started.</p>
        </div>

        <!-- Step 1: Category Selection -->
        <div v-if="step === 1" class="space-y-8 animate-fade-in">
            <CategoryTiles 
                :categories="ticketStore.categories" 
                :selected-id="form.category_id"
                @select="selectCategory" 
            />
        </div>

        <!-- Step 2: Form Details -->
        <div v-else class="glass-card rounded-xl p-8 md:p-12 space-y-10 animate-slide-up">
            <div class="flex items-center gap-4 pb-6 border-b border-glass-border">
                <button @click="step = 1" class="p-3 rounded-xl bg-surface-light hover:bg-primary/10 text-text-dim hover:text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-text-main">{{ selectedCategory?.name }}</h2>
                    <p class="text-sm text-text-dim">Step 2: Provide details regarding your request</p>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Duplicate Warning -->
                <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="duplicateWarning" class="p-4 rounded-lg bg-amber-500/10 border border-amber-500/20 flex gap-4 items-start">
                        <span class="text-2xl">⚠️</span>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-amber-500">Possible Duplicate Found</p>
                            <p class="text-xs text-text-dim">You recently submitted a similar request ({{ duplicateWarning.ticket_number }}). Would you like to proceed or view the existing one?</p>
                            <div class="flex gap-4 mt-2">
                                <router-link :to="`/tickets/${duplicateWarning.ticket_number}`" class="text-xs font-bold text-amber-500 underline uppercase tracking-widest">View Existing</router-link>
                                <button type="button" @click="duplicateWarning = null" class="text-xs font-bold text-text-dim uppercase tracking-widest">Ignore</button>
                            </div>
                        </div>
                    </div>
                </transition>

                <div class="space-y-6">
                    <!-- Title -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Summary of request</label>
                        <input 
                            v-model="form.title"
                            @blur="checkDuplicate"
                            type="text" 
                            required
                            placeholder="e.g. Broken laptop screen after travel"
                            class="w-full px-6 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/30 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all"
                        />
                    </div>

                    <!-- Article Suggestions -->
                    <ArticleSuggestions :query="form.title" />

                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Detailed Description</label>
                        <textarea 
                            v-model="form.description"
                            required
                            rows="5"
                            placeholder="Please provide as much detail as possible..."
                            class="w-full px-6 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/30 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all resize-none"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Priority -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Urgency</label>
                            <select 
                                v-model="form.priority"
                                class="w-full px-6 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all appearance-none"
                            >
                                <option value="low">Low - General query</option>
                                <option value="medium">Medium - Normal operations</option>
                                <option value="high">High - Business impact</option>
                                <option value="critical">Critical - Total stoppage</option>
                            </select>
                        </div>

                        <!-- Attachments (Simplified) -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Attachments (Max 5)</label>
                            <div class="relative">
                                <input 
                                    type="file" 
                                    multiple 
                                    @change="handleFileUpload"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip"
                                />
                                <div class="w-full px-6 py-4 bg-surface-light/50 border border-dashed border-glass-border rounded-lg flex items-center gap-3 text-text-dim group-hover:border-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span class="text-sm">{{ files.length ? `${files.length} files selected` : 'Drop files here or click to upload' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-4">
                    <button 
                        type="button" 
                        @click="step = 1"
                        class="px-8 py-4 rounded-lg text-sm font-bold text-text-dim hover:text-text-main transition-colors"
                    >
                        Back
                    </button>
                    <button 
                        type="submit" 
                        :disabled="loading"
                        class="px-10 py-4 bg-primary hover:bg-primary-dark text-white font-black rounded-lg shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all disabled:opacity-50"
                    >
                        <span v-if="loading">Processing...</span>
                        <span v-else>Submit Request</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useTicketStore } from '@/stores/tickets';
import { useToast } from 'vue-toastification';
import CategoryTiles from '@/components/CategoryTiles.vue';
import ArticleSuggestions from '@/components/Kb/ArticleSuggestions.vue';

const router = useRouter();
const ticketStore = useTicketStore();
const toast = useToast();

const step = ref(1);
const loading = ref(false);
const duplicateWarning = ref(null);
const files = ref([]);

const form = ref({
    title: '',
    description: '',
    category_id: null,
    priority: 'medium',
});

const selectedCategory = computed(() => {
    return ticketStore.categories.find(c => c.id === form.value.category_id);
});

onMounted(async () => {
    await ticketStore.fetchCategories();
});

const selectCategory = (category) => {
    form.value.category_id = category.id;
    step.value = 2;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleFileUpload = (e) => {
    files.value = Array.from(e.target.files);
};

const checkDuplicate = async () => {
    if (form.value.title.length < 5) return;
    const result = await ticketStore.checkDuplicate(form.value.title);
    if (result.is_duplicate) {
        duplicateWarning.value = result.duplicate_ticket;
    }
};

const handleSubmit = async () => {
    loading.value = true;
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('description', form.value.description);
        formData.append('category_id', form.value.category_id);
        formData.append('priority', form.value.priority);
        
        files.value.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });

        const response = await ticketStore.createTicket(formData);
        toast.success('Ticket submitted successfully!');
        router.push(`/tickets/${response.data.ticket_number}`);
    } catch (err) {
        console.error(err);
        toast.error(err.message || 'Error creating ticket');
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
.animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
