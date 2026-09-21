<script setup>
import { computed } from 'vue'
import { Line, Doughnut, Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  ArcElement,
  BarElement,
  Filler,
  Tooltip,
  Legend,
} from 'chart.js'
import { useTicketStore } from '@/stores/ticketStore'
import { timeAgo, actionLabel } from '@/utils/format'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, BarElement, Filler, Tooltip, Legend)

const store = useTicketStore()

const chartTooltip = {
  backgroundColor: '#18181b',
  titleColor: '#fafafa',
  bodyColor: '#d4d4d8',
  borderColor: '#27272a',
  borderWidth: 1,
  padding: 10,
  cornerRadius: 8,
  titleFont: { size: 12, weight: 600 },
  bodyFont: { size: 12 },
}

const chartScaleX = {
  grid: { display: false },
  ticks: { color: '#a1a1aa', font: { size: 11, weight: 500 }, padding: 8 },
  border: { display: false },
}

const chartScaleY = {
  beginAtZero: true,
  grid: { color: '#f4f4f5' },
  ticks: { color: '#a1a1aa', font: { size: 11 }, padding: 8, stepSize: 1 },
  border: { display: false },
}

const metrics = computed(() => [
  {
    label: 'Total Tickets',
    value: store.ticketStats.total,
    color: 'text-zinc-900',
    iconBg: 'bg-zinc-100',
    iconColor: 'text-zinc-500',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  },
  {
    label: 'Open',
    value: store.ticketStats.open,
    color: 'text-amber-600',
    iconBg: 'bg-amber-50',
    iconColor: 'text-amber-500',
    icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  },
  {
    label: 'In Progress',
    value: store.ticketStats.inProgress,
    color: 'text-blue-600',
    iconBg: 'bg-blue-50',
    iconColor: 'text-blue-500',
    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
  },
  {
    label: 'Resolved',
    value: store.ticketStats.resolved,
    color: 'text-emerald-600',
    iconBg: 'bg-emerald-50',
    iconColor: 'text-emerald-500',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
  },
])

const chartData = computed(() => ({
  labels: store.ticketsCreatedLast7Days.map((d) => d.label),
  datasets: [
    {
      label: 'Tickets Created',
      data: store.ticketsCreatedLast7Days.map((d) => d.count),
      borderColor: '#6366f1',
      backgroundColor: 'rgba(99, 102, 241, 0.08)',
      borderWidth: 2,
      pointBackgroundColor: '#6366f1',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6,
      tension: 0.3,
      fill: true,
    },
  ],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { ...chartTooltip, displayColors: false },
  },
  scales: { x: chartScaleX, y: chartScaleY },
}

const statusDoughnutData = computed(() => ({
  labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
  datasets: [
    {
      data: [
        store.ticketStats.open,
        store.ticketStats.inProgress,
        store.ticketStats.resolved,
        store.ticketStats.closed,
      ],
      backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#a1a1aa'],
      borderColor: '#ffffff',
      borderWidth: 2,
      hoverOffset: 4,
    },
  ],
}))

const statusDoughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '65%',
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        usePointStyle: true,
        pointStyle: 'circle',
        padding: 16,
        font: { size: 11, weight: 500 },
        color: '#52525b',
      },
    },
    tooltip: chartTooltip,
  },
}

const divisionBarData = computed(() => {
  const divisions = store.divisions.filter((d) => d.name.toLowerCase() !== 'general')
  const divisionCounts = {}
  divisions.forEach((d) => { divisionCounts[d.id] = 0 })
  store.tickets.forEach((t) => { divisionCounts[t.targetDivisionId] = (divisionCounts[t.targetDivisionId] ?? 0) + 1 })
  return {
    labels: divisions.map((d) => d.name),
    datasets: [
      {
        label: 'Tickets',
        data: divisions.map((d) => divisionCounts[d.id] ?? 0),
        backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899'],
        borderRadius: 6,
        barThickness: 28,
      },
    ],
  }
})

const divisionBarOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { ...chartTooltip, displayColors: false },
  },
  scales: { x: chartScaleX, y: chartScaleY },
}

const priorityBarData = computed(() => {
  const priorityCounts = { low: 0, medium: 0, high: 0, urgent: 0 }
  store.tickets.forEach((t) => { priorityCounts[t.priority] = (priorityCounts[t.priority] ?? 0) + 1 })
  return {
    labels: ['Low', 'Medium', 'High', 'Urgent'],
    datasets: [
      {
        label: 'Tickets',
        data: [priorityCounts.low, priorityCounts.medium, priorityCounts.high, priorityCounts.urgent],
        backgroundColor: ['#a1a1aa', '#3b82f6', '#f59e0b', '#ef4444'],
        borderRadius: 6,
        barThickness: 28,
      },
    ],
  }
})

const priorityBarOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { ...chartTooltip, displayColors: false },
  },
  scales: { x: chartScaleX, y: chartScaleY },
}

const recentActivity = computed(() => {
  return [...store.activityLogs]
    .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
    .slice(0, 5)
})

function actionBadgeColor(action) {
  const map = {
    ticket_created: 'bg-blue-50 text-blue-600',
    ticket_assigned: 'bg-amber-50 text-amber-600',
    status_changed: 'bg-emerald-50 text-emerald-600',
    thread_added: 'bg-violet-50 text-violet-600',
  }
  return map[action] ?? 'bg-zinc-100 text-zinc-500'
}
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Metric cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        v-for="metric in metrics"
        :key="metric.label"
        class="bg-white rounded-xl border border-zinc-200 p-5 hover:shadow-[0_2px_8px_rgba(0,0,0,0.04)] transition-shadow"
      >
        <div class="flex items-center justify-between mb-3">
          <span class="text-[12px] font-medium text-zinc-400">{{ metric.label }}</span>
          <div :class="['w-8 h-8 rounded-lg flex items-center justify-center', metric.iconBg]">
            <svg :class="['w-4 h-4', metric.iconColor]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="metric.icon" />
            </svg>
          </div>
        </div>
        <p :class="['text-3xl font-semibold tabular-nums tracking-tight', metric.color]">{{ metric.value }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Line chart -->
      <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 p-5">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h3 class="text-[14px] font-semibold text-zinc-900">Ticket Trends</h3>
            <p class="text-[12px] text-zinc-400 mt-0.5">Tickets created over the last 7 days</p>
          </div>
        </div>
        <div class="h-64">
          <Line :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Recent activity -->
      <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-zinc-100">
          <h3 class="text-[14px] font-semibold text-zinc-900">Recent Activity</h3>
          <p class="text-[12px] text-zinc-400 mt-0.5">Latest actions across tickets</p>
        </div>
        <div class="divide-y divide-zinc-50">
          <div
            v-for="log in recentActivity"
            :key="log.id"
            class="px-5 py-3.5"
          >
            <div class="flex items-start gap-3">
              <div class="w-7 h-7 rounded-full bg-celoe-100 flex items-center justify-center shrink-0 mt-0.5">
                <span class="text-[10px] font-bold text-celoe-600">
                  {{ (store.getUserById(log.userId)?.name ?? '?').split(' ').map((n) => n[0]).join('') }}
                </span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[12px] text-zinc-600 leading-relaxed">
                  <span class="font-medium text-zinc-900">{{ store.getUserById(log.userId)?.name ?? 'Unknown' }}</span>
                  {{ actionLabel(log.action, 'sentence') }}
                </p>
                <div class="flex items-center gap-2 mt-1">
                  <span :class="['text-[10px] font-semibold px-1.5 py-0.5 rounded', actionBadgeColor(log.action)]">
                    {{ log.action.replace('_', ' ') }}
                  </span>
                  <span class="text-[11px] text-zinc-300">{{ timeAgo(log.createdAt) }} ago</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts row: Doughnut + Division Bar + Priority Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Status Doughnut -->
      <div class="bg-white rounded-xl border border-zinc-200 p-5">
        <div class="mb-4">
          <h3 class="text-[14px] font-semibold text-zinc-900">Status Distribution</h3>
          <p class="text-[12px] text-zinc-400 mt-0.5">Tickets by current status</p>
        </div>
        <div class="h-56">
          <Doughnut :data="statusDoughnutData" :options="statusDoughnutOptions" />
        </div>
      </div>

      <!-- Division Bar -->
      <div class="bg-white rounded-xl border border-zinc-200 p-5">
        <div class="mb-4">
          <h3 class="text-[14px] font-semibold text-zinc-900">By Division</h3>
          <p class="text-[12px] text-zinc-400 mt-0.5">Tickets per division</p>
        </div>
        <div class="h-56">
          <Bar :data="divisionBarData" :options="divisionBarOptions" />
        </div>
      </div>

      <!-- Priority Bar -->
      <div class="bg-white rounded-xl border border-zinc-200 p-5">
        <div class="mb-4">
          <h3 class="text-[14px] font-semibold text-zinc-900">By Priority</h3>
          <p class="text-[12px] text-zinc-400 mt-0.5">Tickets per priority level</p>
        </div>
        <div class="h-56">
          <Bar :data="priorityBarData" :options="priorityBarOptions" />
        </div>
      </div>
    </div>
  </div>
</template>
