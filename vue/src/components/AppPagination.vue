<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  totalItems: { type: Number, required: true },
  perPage: { type: Number, default: 10 },
})

const emit = defineEmits(['update:currentPage'])

const totalPages = computed(() => Math.max(1, Math.ceil(props.totalItems / props.perPage)))

const startItem = computed(() => {
  if (props.totalItems === 0) return 0
  return (props.currentPage - 1) * props.perPage + 1
})

const endItem = computed(() => Math.min(props.currentPage * props.perPage, props.totalItems))

const pageNumbers = computed(() => {
  const total = totalPages.value
  const current = props.currentPage
  const pages = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
    return pages
  }

  pages.push(1)

  if (current > 3) pages.push('...')

  const rangeStart = Math.max(2, current - 1)
  const rangeEnd = Math.min(total - 1, current + 1)
  for (let i = rangeStart; i <= rangeEnd; i++) pages.push(i)

  if (current < total - 2) pages.push('...')

  pages.push(total)
  return pages
})

function goToPage(page) {
  if (page === '...') return
  if (page < 1 || page > totalPages.value) return
  emit('update:currentPage', page)
}
</script>

<template>
  <div
    v-if="totalItems > 0"
    class="flex flex-wrap items-center justify-between gap-2 px-1 py-3"
  >
    <p class="text-[12px] text-zinc-400 tabular-nums">
      Showing <span class="font-medium text-zinc-600">{{ startItem }}–{{ endItem }}</span>
      of <span class="font-medium text-zinc-600">{{ totalItems }}</span>
    </p>

    <nav class="flex items-center gap-0.5">
      <button
        class="inline-flex items-center justify-center w-8 h-8 text-[12px] font-medium text-zinc-400 rounded-md hover:bg-zinc-100 hover:text-zinc-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        :disabled="currentPage === 1"
        @click="goToPage(currentPage - 1)"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <button
        v-for="(page, i) in pageNumbers"
        :key="i"
        :disabled="page === '...'"
        :class="[
          'inline-flex items-center justify-center min-w-[32px] h-8 px-2 text-[12px] font-medium rounded-md transition-colors',
          page === '...'
            ? 'text-zinc-300 cursor-default'
            : page === currentPage
              ? 'bg-zinc-900 text-white'
              : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700',
        ]"
        @click="goToPage(page)"
      >
        {{ page }}
      </button>

      <button
        class="inline-flex items-center justify-center w-8 h-8 text-[12px] font-medium text-zinc-400 rounded-md hover:bg-zinc-100 hover:text-zinc-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        :disabled="currentPage === totalPages"
        @click="goToPage(currentPage + 1)"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </nav>
  </div>
</template>
