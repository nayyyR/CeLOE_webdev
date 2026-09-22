<script setup>
import { computed } from 'vue'
import { useTicketStore } from '@/stores/ticketStore'
import AppPagination from '@/components/AppPagination.vue'
import { usePagination } from '@/composables/usePagination'
import { formatDate, getInitials } from '@/utils/format'

const store = useTicketStore()

const sortedUsers = computed(() => {
  return [...store.filteredUsers].sort((a, b) => a.name.localeCompare(b.name))
})

const { currentPage, paginatedItems: paginatedUsers, perPage } = usePagination(sortedUsers, {
  perPage: 10,
  watchDeps: [() => store.userFilters.search, () => store.userFilters.role, () => store.userFilters.division],
})

function getRoleName(roleId) {
  return store.getRoleById(roleId)?.name ?? 'Unknown'
}

function getDivisionName(divisionId) {
  return store.getDivisionById(divisionId)?.name ?? 'Unknown'
}

function roleBadgeColor(roleId) {
  const map = {
    1: 'bg-violet-50 text-violet-700',
    2: 'bg-celoe-50 text-celoe-700',
    3: 'bg-emerald-50 text-emerald-700',
    4: 'bg-zinc-100 text-zinc-600',
  }
  return map[roleId] ?? 'bg-zinc-100 text-zinc-600'
}

function statusBadge(status) {
  return status === 'active'
    ? 'bg-emerald-50 text-emerald-700'
    : 'bg-zinc-100 text-zinc-500'
}

function hasActiveFilters() {
  return store.userFilters.search !== '' || store.userFilters.role !== null || store.userFilters.division !== null
}

function clearFilters() {
  store.userFilters.search = ''
  store.userFilters.role = null
  store.userFilters.division = null
}
</script>

<template>
  <div class="p-6">
    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-6">
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-zinc-900 tabular-nums">{{ store.users.length }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Total Users</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-violet-600 tabular-nums">{{ store.users.filter((u) => u.roleId === 1).length }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Super Admin</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-celoe-600 tabular-nums">{{ store.users.filter((u) => u.roleId === 2).length }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Admin</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-emerald-600 tabular-nums">{{ store.users.filter((u) => u.roleId === 3).length }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">Employee</p>
      </div>
      <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <p class="text-2xl font-semibold text-blue-600 tabular-nums">{{ store.users.filter((u) => u.roleId === 4).length }}</p>
        <p class="text-[11px] text-zinc-400 font-medium mt-1">User</p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 mb-4">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-300 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input
          v-model="store.userFilters.search"
          type="text"
          placeholder="Search by name or email..."
          class="w-64 pl-9 pr-3 py-2 text-[13px] text-zinc-700 bg-white border border-zinc-200 rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
        />
      </div>
      <select
        v-model="store.userFilters.role"
        class="appearance-none pl-3 pr-8 py-2 text-[13px] font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg cursor-pointer hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
      >
        <option :value="null">All Roles</option>
        <option v-for="role in store.roles" :key="role.id" :value="role.id">{{ role.name }}</option>
      </select>
      <select
        v-model="store.userFilters.division"
        class="appearance-none pl-3 pr-8 py-2 text-[13px] font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg cursor-pointer hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150"
      >
        <option :value="null">All Divisions</option>
        <option v-for="d in store.divisions" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
      <button
        v-if="hasActiveFilters()"
        class="flex items-center gap-1.5 px-3 py-2 text-[12px] font-medium text-zinc-500 bg-zinc-50 border border-zinc-200 rounded-lg hover:bg-zinc-100 transition-colors"
        @click="clearFilters"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Clear
      </button>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-zinc-100">
        <h2 class="text-[14px] font-semibold text-zinc-900">All Users</h2>
        <p class="text-[12px] text-zinc-400 mt-0.5">Team members, roles, and division assignments</p>
      </div>

      <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-zinc-100">
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">User</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Role</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Division</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Status</th>
              <th class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wider px-5 py-3">Joined</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="user in paginatedUsers"
              :key="user.id"
              class="border-b border-zinc-50 last:border-0 hover:bg-zinc-50/50 transition-colors"
            >
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-celoe-100 flex items-center justify-center shrink-0">
                    <span class="text-[11px] font-bold text-celoe-600">{{ getInitials(user.name) }}</span>
                  </div>
                  <div>
                    <p class="text-[13px] font-medium text-zinc-900">{{ user.name }}</p>
                    <p class="text-[12px] text-zinc-400">{{ user.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-3.5">
                <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-md', roleBadgeColor(user.roleId)]">
                  {{ getRoleName(user.roleId) }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <span class="text-[13px] text-zinc-500">{{ getDivisionName(user.divisionId) }}</span>
              </td>
              <td class="px-5 py-3.5">
                <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-md capitalize', statusBadge(user.status)]">
                  {{ user.status }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <span class="text-[12px] text-zinc-400">{{ formatDate(user.createdAt) }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="sm:hidden divide-y divide-zinc-100">
        <div
          v-for="user in paginatedUsers"
          :key="user.id"
          class="px-4 py-3.5"
        >
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-celoe-100 flex items-center justify-center shrink-0">
              <span class="text-[12px] font-bold text-celoe-600">{{ getInitials(user.name) }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-0.5">
                <p class="text-[13px] font-medium text-zinc-900 truncate">{{ user.name }}</p>
                <span :class="['text-[10px] font-semibold px-1.5 py-0.5 rounded', statusBadge(user.status)]">
                  {{ user.status }}
                </span>
              </div>
              <p class="text-[12px] text-zinc-400 truncate">{{ user.email }}</p>
              <div class="flex items-center gap-2 mt-1">
                <span :class="['text-[10px] font-semibold px-1.5 py-0.5 rounded', roleBadgeColor(user.roleId)]">
                  {{ getRoleName(user.roleId) }}
                </span>
                <span class="text-[11px] text-zinc-300">·</span>
                <span class="text-[11px] text-zinc-400">{{ getDivisionName(user.divisionId) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="sortedUsers.length === 0" class="flex flex-col items-center justify-center py-16">
        <svg class="w-10 h-10 text-zinc-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <p class="text-[13px] font-medium text-zinc-400">No users match your filters</p>
      </div>
    </div>

    <AppPagination
      v-if="sortedUsers.length > 0"
      :current-page="currentPage"
      :total-items="sortedUsers.length"
      :per-page="perPage"
      @update:current-page="currentPage = $event"
    />
  </div>
</template>
