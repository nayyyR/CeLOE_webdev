<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()

const mainNavItems = [
  { label: 'Dashboard', routeName: 'dashboard', icon: 'chart' },
  { label: 'Tickets', routeName: 'tickets', icon: 'ticket' },
  { label: 'Activity', routeName: 'activity', icon: 'activity' },
  { label: 'Users', routeName: 'users', icon: 'users' },
]

const bottomNavItem = { label: 'Profile', routeName: 'profile', icon: 'profile' }

function isActive(routeName) {
  return route.name === routeName
}
</script>

<template>
  <aside class="flex w-60 flex-col bg-white border-r border-zinc-200 h-full">
    <div class="flex flex-col flex-1 min-h-0">
      <div class="flex items-center h-16 flex-shrink-0 px-5 border-b border-zinc-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-celoe-500 flex items-center justify-center">
            <span class="text-white text-sm font-semibold tracking-tight">C</span>
          </div>
          <span class="text-[15px] font-semibold text-zinc-900 tracking-tight">Celoe</span>
        </div>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <RouterLink
          v-for="item in mainNavItems"
          :key="item.label"
          :to="{ name: item.routeName }"
          :class="[
            'group flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors duration-150',
            isActive(item.routeName)
              ? 'bg-celoe-50 text-celoe-700'
              : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900',
          ]"
        >
          <span
            :class="[
              'w-5 h-5 rounded flex items-center justify-center transition-colors duration-150',
              isActive(item.routeName)
                ? 'bg-celoe-500 text-white'
                : 'bg-zinc-200 text-zinc-400 group-hover:bg-zinc-300 group-hover:text-zinc-500',
            ]"
          >
            <svg v-if="item.icon === 'chart'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 16l4-4 4 4 5-5" />
            </svg>
            <svg v-else-if="item.icon === 'ticket'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            <svg v-else-if="item.icon === 'activity'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else-if="item.icon === 'users'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </span>
          {{ item.label }}
        </RouterLink>
      </nav>

      <div class="flex-shrink-0 border-t border-zinc-100 px-3 py-3">
        <RouterLink
          :to="{ name: bottomNavItem.routeName }"
          :class="[
            'group flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors duration-150',
            isActive(bottomNavItem.routeName)
              ? 'bg-celoe-50 text-celoe-700'
              : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900',
          ]"
        >
          <span
            :class="[
              'w-5 h-5 rounded flex items-center justify-center transition-colors duration-150',
              isActive(bottomNavItem.routeName)
                ? 'bg-celoe-500 text-white'
                : 'bg-zinc-200 text-zinc-400 group-hover:bg-zinc-300 group-hover:text-zinc-500',
            ]"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </span>
          {{ bottomNavItem.label }}
        </RouterLink>
      </div>
    </div>
  </aside>
</template>
