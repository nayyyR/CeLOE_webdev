<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useTicketStore } from '@/stores/ticketStore'

const route = useRoute()
const store = useTicketStore()

const emit = defineEmits(['toggle-mobile-menu'])

const isTicketPage = computed(() => route.name === 'tickets')

const pageTitle = computed(() => {
  const titles = {
    dashboard: 'Dashboard',
    tickets: 'Ticket Management',
    activity: 'Activity Log',
    users: 'Users',
    profile: 'Profile',
  }
  return titles[route.name] ?? 'Celoe'
})

const hasActiveFilters = computed(() => {
  return store.filters.search !== '' || store.filters.priority !== null || store.filters.status !== null || store.filters.division !== null
})

function clearFilters() {
  store.filters.search = ''
  store.filters.priority = null
  store.filters.status = null
  store.filters.division = null
}
</script>

<template>
  <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-zinc-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
      <div class="flex items-center gap-3">
        <button
          class="md:hidden p-2 -ml-2 rounded-lg text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 transition-colors"
          @click="emit('toggle-mobile-menu')"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div>
          <h1 class="text-lg font-semibold text-zinc-900 tracking-tight">{{ pageTitle }}</h1>
        </div>
      </div>

      <div v-if="isTicketPage" class="flex items-center gap-2">
        <button
          v-if="hasActiveFilters"
          class="flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium text-zinc-500 bg-zinc-50 border border-zinc-200 rounded-lg hover:bg-zinc-100 transition-colors"
          @click="clearFilters"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Clear filters
        </button>
      </div>
    </div>
  </header>
</template>
