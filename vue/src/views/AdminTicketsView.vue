<script setup>
import { ref, computed } from 'vue'
import { useTicketStore } from '@/stores/ticketStore'
import AppPagination from '@/components/AppPagination.vue'
import { usePagination } from '@/composables/usePagination'
import { formatDate, priorityColor, statusColor } from '@/utils/format'

const store = useTicketStore()

const selectedTicket = ref(null)
const showAssignModal = ref(false)
const showStatusModal = ref(false)
const showCreateModal = ref(false)
const assignTarget = ref(null)
const newStatus = ref(null)
const createForm = ref({ subject: '', description: '', priority: 'medium', targetDivisionId: 2 })

const priorities = ['low', 'medium', 'high', 'urgent']
const statusOptions = ['open', 'in_progress', 'resolved', 'closed']

const sortedTickets = computed(() => {
  return [...store.filteredTickets].sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
})

const { currentPage, paginatedItems: paginatedTickets, perPage } = usePagination(sortedTickets, {
  perPage: 10,
  watchDeps: [() => store.filters.search, () => store.filters.priority, () => store.filters.status, () => store.filters.division],
})

const availableStatusOptions = computed(() => {
  if (!selectedTicket.value) return []
  const current = selectedTicket.value.status
  if (current === 'in_progress') return ['resolved', 'closed']
  if (current === 'resolved') return ['in_progress', 'closed']
  return []
})

function getAssigneeName(id) {
  if (!id) return null
  const user = store.getUserById(id)
  return user?.name ?? null
}

function getAssigneeInitials(id) {
  const name = getAssigneeName(id)
  if (!name) return '?'
  return name.split(' ').map((n) => n[0]).join('')
}

function getDivisionName(id) {
  return store.getDivisionById(id)?.name ?? 'Unknown'
}

function getReporterName(id) {
  const user = store.getUserById(id)
  return user?.name ?? 'Unknown'
}

function openAssignModal(ticket) {
  selectedTicket.value = ticket
  assignTarget.value = null
  showAssignModal.value = true
}

function confirmAssign() {
  if (selectedTicket.value && assignTarget.value) {
    store.assignTicket(selectedTicket.value.id, assignTarget.value)
    showAssignModal.value = false
    selectedTicket.value = null
    assignTarget.value = null
  }
}

function openStatusModal(ticket) {
  selectedTicket.value = ticket
  const current = ticket.status
  if (current === 'in_progress') newStatus.value = 'resolved'
  else if (current === 'resolved') newStatus.value = 'in_progress'
  else newStatus.value = null
  showStatusModal.value = true
}

function confirmStatusChange() {
  if (selectedTicket.value && newStatus.value) {
    store.updateTicketStatus(selectedTicket.value.id, newStatus.value)
    showStatusModal.value = false
    selectedTicket.value = null
    newStatus.value = null
  }
}

function closeModals() {
  showAssignModal.value = false
  showStatusModal.value = false
  showCreateModal.value = false
  selectedTicket.value = null
}

function openCreateModal() {
  createForm.value = { subject: '', description: '', priority: 'medium', targetDivisionId: 2 }
  showCreateModal.value = true
}

function confirmCreate() {
  if (!createForm.value.subject.trim()) return
  store.addTicket(createForm.value)
  showCreateModal.value = false
}

const availableEmployees = computed(() => {
  if (!selectedTicket.value) return []
  const divisionEmployees = store.getEmployeesByDivision(selectedTicket.value.targetDivisionId)
  return divisionEmployees.map((emp) => ({
    ...emp,
    activeCount: store.tickets.filter((t) => t.assignedEmployeeId === emp.id && t.status === 'in_progress').length,
    resolvedCount: store.tickets.filter((t) => t.assignedEmployeeId === emp.id && (t.status === 'resolved' || t.status === 'closed')).length,
  }))
})
</script>

<template>
  <div class="p-6">
    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-6">
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-zinc-900 tabular-nums">{{ store.ticketStats.total }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Total</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-amber-600 tabular-nums">{{ store.ticketStats.open }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Open</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-blue-600 tabular-nums">{{ store.ticketStats.inProgress }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">In Progress</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-emerald-600 tabular-nums">{{ store.ticketStats.resolved }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Resolved</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-zinc-500 tabular-nums">{{ store.ticketStats.closed }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Closed</p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 mb-4">
      <button
        v-if="store.canCreateTickets"
        class="flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-white bg-zinc-800 rounded-lg hover:bg-zinc-900 transition-colors"
        @click="openCreateModal"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
        </svg>
        New Ticket
      </button>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-300 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input
          v-model="store.filters.search"
          type="text"
          placeholder="Search tickets..."
          class="w-64 pl-9 pr-3 py-2 text-[13px] text-zinc-700 bg-white border border-zinc-200 rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
        />
      </div>
      <select
        v-model="store.filters.status"
        class="appearance-none pl-3 pr-8 py-2 text-[13px] font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg cursor-pointer hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
      >
        <option :value="null">All Status</option>
        <option v-for="s in statusOptions" :key="s" :value="s" class="capitalize">{{ s.replace('_', ' ') }}</option>
      </select>
      <select
        v-model="store.filters.priority"
        class="appearance-none pl-3 pr-8 py-2 text-[13px] font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg cursor-pointer hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
      >
        <option :value="null">All Priority</option>
        <option v-for="p in priorities" :key="p" :value="p" class="capitalize">{{ p }}</option>
      </select>
      <select
        v-model="store.filters.division"
        class="appearance-none pl-3 pr-8 py-2 text-[13px] font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg cursor-pointer hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
      >
        <option :value="null">All Divisions</option>
        <option v-for="d in store.divisions.filter((d) => d.name !== 'General')" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-zinc-100">
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Ticket</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Status</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Priority</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Division</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Assignee</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Reporter</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Created</th>
              <th class="text-right text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="ticket in paginatedTickets"
              :key="ticket.id"
              class="border-b border-zinc-50 last:border-0 hover:bg-zinc-50/50 transition-colors"
            >
              <td class="px-5 py-3.5">
                <div>
                  <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[11px] font-mono text-zinc-300 tabular-nums">{{ ticket.ticketNumber }}</span>
                  </div>
                  <p class="text-[13px] font-medium text-zinc-900 leading-snug">{{ ticket.subject }}</p>
                </div>
              </td>
              <td class="px-5 py-3.5">
                <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-md capitalize', statusColor(ticket.status)]">
                  {{ ticket.status.replace('_', ' ') }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-md capitalize', priorityColor(ticket.priority)]">
                  {{ ticket.priority }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <span class="text-[13px] text-zinc-500">{{ getDivisionName(ticket.targetDivisionId) }}</span>
              </td>
              <td class="px-5 py-3.5">
                <div v-if="getAssigneeName(ticket.assignedEmployeeId)" class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full bg-celoe-100 flex items-center justify-center shrink-0">
                    <span class="text-[10px] font-bold text-celoe-600">{{ getAssigneeInitials(ticket.assignedEmployeeId) }}</span>
                  </div>
                  <span class="text-[12px] text-zinc-600">{{ getAssigneeName(ticket.assignedEmployeeId)?.split(' ')[0] }}</span>
                </div>
                <span v-else class="text-[12px] text-zinc-300 italic">Unassigned</span>
              </td>
              <td class="px-5 py-3.5">
                <span class="text-[12px] text-zinc-500">{{ getReporterName(ticket.createdBy).split(' ')[0] }}</span>
              </td>
              <td class="px-5 py-3.5">
                <span class="text-[12px] text-zinc-400 tabular-nums">{{ formatDate(ticket.createdAt) }}</span>
              </td>
              <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    v-if="ticket.status === 'open' && store.canAssignTickets"
                    class="text-[11px] font-semibold px-3 py-1 rounded-md border border-slate-200 bg-slate-50 text-slate-700 shadow-sm hover:bg-slate-100 hover:shadow active:scale-95 cursor-pointer transition-all duration-150"
                    @click="openAssignModal(ticket)"
                  >
                    Assign
                  </button>
                  <button
                    v-else-if="ticket.status === 'in_progress' || ticket.status === 'resolved'"
                    class="text-[11px] font-semibold px-3 py-1 rounded-md border border-emerald-200 bg-emerald-50 text-emerald-700 shadow-sm hover:bg-emerald-100 hover:shadow active:scale-95 cursor-pointer transition-all duration-150"
                    @click="openStatusModal(ticket)"
                  >
                    Status
                  </button>
                  <span
                    v-else-if="ticket.status === 'closed'"
                    class="text-[11px] font-semibold px-3 py-1 rounded-md bg-zinc-100 text-zinc-500"
                  >
                    Closed
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="lg:hidden divide-y divide-zinc-100">
        <div
          v-for="ticket in paginatedTickets"
          :key="ticket.id"
          class="p-4 space-y-2.5"
        >
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-mono text-zinc-300 tabular-nums">{{ ticket.ticketNumber }}</span>
            <div class="flex items-center gap-1.5">
              <span :class="['text-[10px] font-semibold px-1.5 py-0.5 rounded capitalize', statusColor(ticket.status)]">
                {{ ticket.status.replace('_', ' ') }}
              </span>
              <span :class="['text-[10px] font-semibold px-1.5 py-0.5 rounded capitalize', priorityColor(ticket.priority)]">
                {{ ticket.priority }}
              </span>
            </div>
          </div>
          <p class="text-[13px] font-medium text-zinc-900">{{ ticket.subject }}</p>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-[11px] text-zinc-400">{{ getDivisionName(ticket.targetDivisionId) }}</span>
              <span v-if="getAssigneeName(ticket.assignedEmployeeId)" class="text-[11px] text-zinc-400">
                · {{ getAssigneeName(ticket.assignedEmployeeId)?.split(' ')[0] }}
              </span>
            </div>
            <div class="flex items-center gap-1.5">
              <button
                v-if="ticket.status === 'open' && store.canAssignTickets"
                class="text-[10px] font-semibold px-2.5 py-1 rounded border border-slate-200 bg-slate-50 text-slate-700 shadow-sm hover:bg-slate-100 hover:shadow active:scale-95 cursor-pointer transition-all duration-150"
                @click="openAssignModal(ticket)"
              >
                Assign
              </button>
              <button
                v-else-if="ticket.status === 'in_progress' || ticket.status === 'resolved'"
                class="text-[10px] font-semibold px-2.5 py-1 rounded border border-emerald-200 bg-emerald-50 text-emerald-700 shadow-sm hover:bg-emerald-100 hover:shadow active:scale-95 cursor-pointer transition-all duration-150"
                @click="openStatusModal(ticket)"
              >
                Status
              </button>
              <span
                v-else-if="ticket.status === 'closed'"
                class="text-[10px] font-semibold px-2.5 py-1 rounded bg-zinc-100 text-zinc-500"
              >
                Closed
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="sortedTickets.length === 0" class="flex flex-col items-center justify-center py-16">
        <svg class="w-10 h-10 text-zinc-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-[13px] font-medium text-zinc-400">No tickets match your filters</p>
      </div>
    </div>

    <AppPagination
      v-if="sortedTickets.length > 0"
      :current-page="currentPage"
      :total-items="sortedTickets.length"
      :per-page="perPage"
      @update:current-page="currentPage = $event"
    />

    <Teleport to="body">
      <transition
        enter-active-class="transition-opacity duration-150 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-100 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showAssignModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
          <div class="absolute inset-0 bg-zinc-900/20 backdrop-blur-sm" @click="closeModals" />
          <div class="relative bg-white rounded-2xl border border-zinc-200 shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100">
              <h3 class="text-[15px] font-semibold text-zinc-900">Assign Ticket</h3>
              <p class="text-[12px] text-zinc-400 mt-0.5">{{ selectedTicket?.ticketNumber }} — {{ selectedTicket?.subject }}</p>
            </div>
            <div class="px-6 py-5">
              <label class="block text-[12px] font-medium text-zinc-500 mb-2">Select Employee</label>
              <p class="text-[11px] text-zinc-400 mb-3">Only employees from the ticket's division ({{ getDivisionName(selectedTicket?.targetDivisionId) }}) are shown.</p>
              <div v-if="availableEmployees.length === 0" class="text-[13px] text-zinc-400 italic py-4 text-center bg-zinc-50 rounded-lg">
                No employees available in this division.
              </div>
              <div v-else class="space-y-2 max-h-48 overflow-y-auto">
                <label
                  v-for="emp in availableEmployees"
                  :key="emp.id"
                  :class="[
                    'flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all duration-150',
                    assignTarget === emp.id
                      ? 'border-celoe-300 bg-celoe-50/50 ring-1 ring-celoe-200'
                      : 'border-zinc-200 hover:bg-zinc-50',
                  ]"
                >
                  <input
                    v-model="assignTarget"
                    type="radio"
                    :value="emp.id"
                    class="sr-only"
                  />
                  <div class="w-8 h-8 rounded-full bg-celoe-100 flex items-center justify-center shrink-0">
                    <span class="text-[11px] font-bold text-celoe-600">{{ emp.name.split(' ').map((n) => n[0]).join('') }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium text-zinc-900">{{ emp.name }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span class="text-[11px] text-zinc-400">@{{ emp.username }}</span>
                      <span class="text-[10px] text-zinc-300">·</span>
                      <span class="text-[11px] text-zinc-400">
                        <span class="font-medium text-blue-600">{{ emp.activeCount }}</span> active
                        <span class="text-zinc-300 mx-0.5">|</span>
                        <span class="font-medium text-emerald-600">{{ emp.resolvedCount }}</span> done
                      </span>
                    </div>
                  </div>
                </label>
              </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-zinc-100 bg-zinc-50/50">
              <button
                class="px-4 py-2 text-[13px] font-medium text-zinc-600 hover:bg-zinc-100 rounded-lg transition-colors"
                @click="closeModals"
              >
                Cancel
              </button>
              <button
                :disabled="!assignTarget"
                :class="[
                  'px-4 py-2 text-[13px] font-medium rounded-lg transition-colors',
                  assignTarget
                    ? 'bg-zinc-800 text-white hover:bg-zinc-900'
                    : 'bg-zinc-100 text-zinc-400 cursor-not-allowed',
                ]"
                @click="confirmAssign"
              >
                Assign Ticket
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>

    <Teleport to="body">
      <transition
        enter-active-class="transition-opacity duration-150 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-100 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showStatusModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
          <div class="absolute inset-0 bg-zinc-900/20 backdrop-blur-sm" @click="closeModals" />
          <div class="relative bg-white rounded-2xl border border-zinc-200 shadow-xl w-full max-w-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100">
              <h3 class="text-[15px] font-semibold text-zinc-900">Update Status</h3>
              <p class="text-[12px] text-zinc-400 mt-0.5">{{ selectedTicket?.ticketNumber }}</p>
            </div>
            <div class="px-6 py-5">
              <div class="space-y-2">
                <label
                  v-for="status in availableStatusOptions"
                  :key="status"
                  :class="[
                    'flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all duration-150',
                    newStatus === status
                      ? 'border-celoe-300 bg-celoe-50/50 ring-1 ring-celoe-200'
                      : 'border-zinc-200 hover:bg-zinc-50',
                  ]"
                >
                  <input
                    v-model="newStatus"
                    type="radio"
                    :value="status"
                    class="sr-only"
                  />
                  <div :class="['w-2.5 h-2.5 rounded-full shrink-0', statusColor(status).split(' ')[0].replace('bg-', 'bg-')]" />
                  <span class="text-[13px] font-medium text-zinc-700 capitalize">{{ status.replace('_', ' ') }}</span>
                </label>
              </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-zinc-100 bg-zinc-50/50">
              <button
                class="px-4 py-2 text-[13px] font-medium text-zinc-600 hover:bg-zinc-100 rounded-lg transition-colors"
                @click="closeModals"
              >
                Cancel
              </button>
              <button
                :disabled="newStatus === selectedTicket?.status"
                :class="[
                  'px-4 py-2 text-[13px] font-medium rounded-lg transition-colors',
                  newStatus !== selectedTicket?.status
                    ? 'bg-zinc-800 text-white hover:bg-zinc-900'
                    : 'bg-zinc-100 text-zinc-400 cursor-not-allowed',
                ]"
                @click="confirmStatusChange"
              >
                Update Status
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>

    <Teleport to="body">
      <transition
        enter-active-class="transition-opacity duration-150 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-100 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showCreateModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
          <div class="absolute inset-0 bg-zinc-900/20 backdrop-blur-sm" @click="closeModals" />
          <div class="relative bg-white rounded-2xl border border-zinc-200 shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100">
              <h3 class="text-[15px] font-semibold text-zinc-900">Create New Ticket</h3>
              <p class="text-[12px] text-zinc-400 mt-0.5">Submit a new support request</p>
            </div>
            <div class="px-6 py-5 space-y-4">
              <div>
                <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Subject</label>
                <input
                  v-model="createForm.subject"
                  type="text"
                  class="w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border border-zinc-200 rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
                  placeholder="Brief summary of the issue"
                />
              </div>
              <div>
                <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Description</label>
                <textarea
                  v-model="createForm.description"
                  rows="3"
                  class="w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border border-zinc-200 rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150 resize-none"
                  placeholder="Detailed description of the problem"
                />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Priority</label>
                  <select
                    v-model="createForm.priority"
                    class="w-full appearance-none px-3 py-2 text-[13px] font-medium text-zinc-600 bg-zinc-50 border border-zinc-200 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
                  >
                    <option v-for="p in priorities" :key="p" :value="p" class="capitalize">{{ p }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Target Division</label>
                  <select
                    v-model="createForm.targetDivisionId"
                    class="w-full appearance-none px-3 py-2 text-[13px] font-medium text-zinc-600 bg-zinc-50 border border-zinc-200 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
                  >
                    <option v-for="d in store.divisions.filter((d) => d.name !== 'General')" :key="d.id" :value="d.id">{{ d.name }}</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-zinc-100 bg-zinc-50/50">
              <button
                class="px-4 py-2 text-[13px] font-medium text-zinc-600 hover:bg-zinc-100 rounded-lg transition-colors"
                @click="closeModals"
              >
                Cancel
              </button>
              <button
                :disabled="!createForm.subject.trim()"
                :class="[
                  'px-4 py-2 text-[13px] font-medium rounded-lg transition-colors',
                  createForm.subject.trim()
                    ? 'bg-zinc-800 text-white hover:bg-zinc-900'
                    : 'bg-zinc-100 text-zinc-400 cursor-not-allowed',
                ]"
                @click="confirmCreate"
              >
                Create Ticket
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>
