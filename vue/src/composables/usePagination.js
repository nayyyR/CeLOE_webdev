import { ref, computed, watch } from 'vue'

export function usePagination(sortedItems, options = {}) {
  const { perPage = 10, watchDeps = [] } = options
  const currentPage = ref(1)

  const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return sortedItems.value.slice(start, start + perPage)
  })

  if (watchDeps.length > 0) {
    watch(watchDeps, () => { currentPage.value = 1 })
  }

  return { currentPage, paginatedItems, perPage }
}
