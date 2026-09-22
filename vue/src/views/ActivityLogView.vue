<script setup>
import { computed } from 'vue'
import { useTicketStore } from '@/stores/ticketStore'
import AppPagination from '@/components/AppPagination.vue'
import { usePagination } from '@/composables/usePagination'
import { formatDate, statusColor, actionColor, actionLabel } from '@/utils/format'

const store = useTicketStore()

const sortedLogs = computed(() => {
  return [...store.activityLogs].sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
})

const { currentPage, paginatedItems: paginatedLogs, perPage } = usePagination(sortedLogs, { perPage: 10 })

function getUserName(userId) {
  const user = store.getUserById(userId)
  return user?.name ?? 'Unknown'
}

function getTicketTitle(ticketId) {
  const ticket = store.tickets.find((t) => t.id === ticketId)
  return ticket?.subject ?? `#${ticketId}`
}

function formatTime(dateString) {
  return new Date(dateString).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <div class="p-6">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-zinc-900 tabular-nums">{{ store.activityLogs.length }}</p>
        <p class="text-[12px] text-zinc-400 font-medium mt-1">Total Actions</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-emerald-600 tabular-nums">{{ store.activityLogs.filter((l) => l.action === 'ticket_created').length }}</p>
        <p class="text-[12px] text-zinc-400 font-medium mt-1">Tickets Created</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-blue-600 tabular-nums">{{ store.activityLogs.filter((l) => l.action === 'ticket_assigned').length }}</p>
        <p class="text-[12px] text-zinc-400 font-medium mt-1">Assignments</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-amber-600 tabular-nums">{{ store.activityLogs.filter((l) => l.action === 'status_changed').length }}</p>
        <p class="text-[12px] text-zinc-400 font-medium mt-1">Status Changes</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-zinc-100">
        <h2 class="text-[14px] font-semibold text-zinc-900">Recent Activity</h2>
        <p class="text-[12px] text-zinc-400 mt-0.5">All actions across tickets</p>
      </div>

      <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-zinc-100">
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Action</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Ticket</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">User</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Status Change</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="log in paginatedLogs"
              :key="log.id"
              class="border-b border-zinc-50 last:border-0 hover:bg-zinc-50/50 transition-colors"
            >
              <td class="px-5 py-3.5">
                <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-md', actionColor(log.action)]">
                  {{ actionLabel(log.action) }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <span class="text-[13px] text-zinc-700 font-medium">{{ getTicketTitle(log.ticketId) }}</span>
              </td>
              <td class="px-5 py-3.5">
                <span class="text-[13px] text-zinc-500">{{ getUserName(log.userId) }}</span>
              </td>
              <td class="px-5 py-3.5">
                <div v-if="log.oldStatus && log.newStatus" class="flex items-center gap-1.5">
                  <span :class="['text-[11px] font-medium px-1.5 py-0.5 rounded', statusColor(log.oldStatus)]">{{ log.oldStatus }}</span>
                  <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  <span :class="['text-[11px] font-medium px-1.5 py-0.5 rounded', statusColor(log.newStatus)]">{{ log.newStatus }}</span>
                </div>
                <span v-else class="text-[12px] text-zinc-300">—</span>
              </td>
              <td class="px-5 py-3.5">
                <div class="text-[12px] text-zinc-400">
                  <p>{{ formatDate(log.createdAt) }}</p>
                  <p class="text-[11px]">{{ formatTime(log.createdAt) }}</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="sm:hidden divide-y divide-zinc-100">
        <div
          v-for="log in sortedLogs"
          :key="log.id"
          class="px-4 py-3.5"
        >
          <div class="flex items-start justify-between mb-1.5">
            <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-md', actionColor(log.action)]">
              {{ actionLabel(log.action) }}
            </span>
            <span class="text-[11px] text-zinc-300">{{ formatDate(log.createdAt) }}</span>
          </div>
          <p class="text-[13px] text-zinc-700 font-medium mb-1">{{ getTicketTitle(log.ticketId) }}</p>
          <div class="flex items-center gap-2">
            <span class="text-[12px] text-zinc-400">{{ getUserName(log.userId) }}</span>
            <span v-if="log.oldStatus && log.newStatus" class="text-[11px] text-zinc-300">
              {{ log.oldStatus }} → {{ log.newStatus }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <AppPagination
      v-if="sortedLogs.length > 0"
      :current-page="currentPage"
      :total-items="sortedLogs.length"
      :per-page="perPage"
      @update:current-page="currentPage = $event"
    />
  </div>
</template>
