<template>
    <div class="space-y-12 pb-24">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-2">
                <h1 class="text-4xl font-black text-text-main tracking-tight">System <span class="text-gradient">Intelligence</span></h1>
                <p class="text-text-dim font-medium">Global infrastructure metrics and support performance audit.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex bg-surface-light p-1 rounded-xl border border-glass-border">
                    <button v-for="range in ranges" :key="range.label" 
                        @click="selectedRange = range.value; fetchData()"
                        class="px-5 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all"
                        :class="selectedRange === range.value ? 'bg-primary text-white shadow-lg' : 'text-text-dim hover:text-text-main'"
                    >
                        {{ range.label }}
                    </button>
                </div>
                <button class="p-3 glass-card rounded-xl text-primary hover:bg-primary/10 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div v-for="kpi in kpis" :key="kpi.label" class="glass-card p-8 rounded-2xl relative overflow-hidden group hover-lift">
                <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-full -mr-12 -mt-12 transition-transform duration-700 group-hover:scale-150"></div>
                <div class="relative z-10 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-light flex items-center justify-center text-2xl shadow-sm border border-glass-border">
                        {{ kpi.icon }}
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-text-dim mb-1">{{ kpi.label }}</p>
                        <h2 class="text-3xl font-black text-text-main">{{ kpi.value }}<span class="text-xs font-bold text-text-dim ml-1">{{ kpi.unit }}</span></h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="kpi.trendUp ? 'text-emerald-500' : 'text-rose-500'" class="text-[10px] font-black uppercase tracking-widest">
                            {{ kpi.trendUp ? '↑' : '↓' }} {{ kpi.trend }}%
                        </span>
                        <span class="text-[10px] text-text-dim font-bold uppercase tracking-widest opacity-50">vs last period</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Volume Trend -->
            <div class="lg:col-span-2 glass-card p-10 rounded-2xl space-y-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">Incident Volume Dynamics</h3>
                    <div class="w-3 h-3 rounded-full bg-primary animate-pulse"></div>
                </div>
                <div class="h-80">
                    <apexchart type="area" height="100%" :options="volumeChart.options" :series="volumeChart.series"></apexchart>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="glass-card p-10 rounded-2xl space-y-8 flex flex-col">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">Distribution Strategy</h3>
                <div class="flex-1 flex items-center justify-center">
                    <apexchart type="donut" width="100%" :options="categoryChart.options" :series="categoryChart.series"></apexchart>
                </div>
            </div>
        </div>

        <!-- Lower Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Heatmap -->
            <div class="glass-card p-10 rounded-2xl space-y-8">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">Support Density (Heatmap)</h3>
                <div class="h-80">
                    <apexchart type="heatmap" height="100%" :options="heatmapChart.options" :series="heatmapChart.series"></apexchart>
                </div>
            </div>

            <!-- Agent Performance -->
            <div class="glass-card p-10 rounded-2xl space-y-8 overflow-hidden">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">Elite Service Providers</h3>
                <div class="space-y-4">
                    <div v-for="agent in leaderboard" :key="agent.name" class="flex items-center justify-between p-4 bg-surface-light/50 rounded-xl border border-glass-border group hover:border-primary/30 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center font-black text-primary border border-primary/20">
                                {{ agent.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold text-text-main text-sm">{{ agent.name }}</p>
                                <p class="text-[10px] font-bold text-text-dim uppercase tracking-widest">{{ agent.tickets_closed }} Resolves</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-emerald-500">{{ agent.avg_csat || '5.0' }}★</p>
                            <p class="text-[9px] font-bold text-text-dim uppercase tracking-widest">CSAT INDEX</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import axios from '@/plugins/axios';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();
const chartTheme = computed(() => themeStore.theme === 'dark' ? 'dark' : 'light');
const labelColor = computed(() => themeStore.theme === 'dark' ? '#9ca3af' : '#64748b');

const selectedRange = ref(30);
const ranges = [
    { label: '7D', value: 7 },
    { label: '30D', value: 30 },
    { label: '90D', value: 90 },
];

const kpis = ref([
    { label: 'Throughput', value: '0', unit: '', icon: '⚡', trend: '12', trendUp: true },
    { label: 'Resolution Lag', value: '0', unit: 'm', icon: '⏱️', trend: '5', trendUp: false },
    { label: 'SLA Health', value: '0', unit: '%', icon: '🛡️', trend: '2', trendUp: true },
    { label: 'Satisfaction', value: '0', unit: '/5', icon: '⭐', trend: '8', trendUp: true },
]);

const chartData = reactive({
    volumeCategories: [],
    volumeSeries: [],
    categoryLabels: [],
    categorySeries: [],
    heatmapSeries: []
});

const volumeChart = computed(() => ({
    series: [{ name: 'Incidents', data: chartData.volumeSeries }],
    options: {
        chart: { toolbar: { show: false }, background: 'transparent' },
        stroke: { curve: 'smooth', width: 3, colors: ['#6366f1'] },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100] } },
        dataLabels: { enabled: false },
        xaxis: { categories: chartData.volumeCategories, labels: { style: { colors: labelColor.value, fontWeight: 600 } } },
        yaxis: { labels: { style: { colors: labelColor.value, fontWeight: 600 } } },
        grid: { borderColor: themeStore.theme === 'dark' ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
        theme: { mode: chartTheme.value }
    }
}));

const categoryChart = computed(() => ({
    series: chartData.categorySeries,
    options: {
        chart: { background: 'transparent' },
        labels: chartData.categoryLabels,
        colors: ['#6366f1', '#a855f7', '#ec4899', '#f97316', '#3b82f6'],
        legend: { position: 'bottom', labels: { colors: labelColor.value } },
        stroke: { width: 0 },
        theme: { mode: chartTheme.value }
    }
}));

const heatmapChart = computed(() => ({
    series: chartData.heatmapSeries,
    options: {
        chart: { toolbar: { show: false }, background: 'transparent' },
        dataLabels: { enabled: false },
        colors: ["#6366f1"],
        xaxis: { type: 'category', labels: { style: { colors: labelColor.value } } },
        yaxis: { labels: { style: { colors: labelColor.value } } },
        theme: { mode: chartTheme.value }
    }
}));

const leaderboard = ref([]);

const fetchData = async () => {
    try {
        const perf = await axios.get('/reports/performance', { params: { days: selectedRange.value } });
        const d = perf.data.data;
        
        kpis.value[0].value = d.total_tickets;
        kpis.value[1].value = d.avg_resolution_time;
        kpis.value[2].value = d.sla_compliance_rate;
        kpis.value[3].value = d.avg_csat_score || '5.0';

        chartData.categorySeries = d.volume_by_category.map(c => c.count);
        chartData.categoryLabels = d.volume_by_category.map(c => c.name);

        const trends = await axios.get('/reports/trends', { params: { days: selectedRange.value } });
        chartData.volumeSeries = trends.data.data.map(t => t.count);
        chartData.volumeCategories = trends.data.data.map(t => t.date);

        const heat = await axios.get('/reports/heatmap', { params: { days: selectedRange.value } });
        processHeatmap(heat.data.data);

        const agents = await axios.get('/reports/agents', { params: { days: selectedRange.value } });
        leaderboard.value = agents.data.data;

    } catch (err) {
        console.error('Insight fetch failed', err);
    }
};

const processHeatmap = (raw) => {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const formatted = days.map((day, dIdx) => {
        const data = Array.from({ length: 24 }, (_, hour) => {
            const found = raw.find(r => r.day === (dIdx + 1) && r.hour === hour);
            return { x: `${hour}:00`, y: found ? found.count : 0 };
        });
        return { name: day, data };
    });
    chartData.heatmapSeries = formatted;
};

onMounted(fetchData);
</script>

<style scoped>
.text-gradient {
    background: linear-gradient(to right, var(--color-primary, #6366f1), var(--color-secondary, #a855f7));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}
</style>
