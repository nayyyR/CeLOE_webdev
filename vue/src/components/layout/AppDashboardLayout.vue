<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppTopBar from '@/components/layout/AppTopBar.vue'

const route = useRoute()
const mobileMenuOpen = ref(false)

watch(() => route.fullPath, () => {
  mobileMenuOpen.value = false
})
</script>

<template>
  <div class="min-h-screen bg-zinc-50">
    <!-- Desktop sidebar -->
    <div class="hidden md:block fixed inset-y-0 left-0 w-60 z-30">
      <AppSidebar />
    </div>

    <!-- Mobile sidebar overlay -->
    <div
      v-if="mobileMenuOpen"
      class="fixed inset-0 z-40 bg-zinc-900/20 backdrop-blur-sm md:hidden"
      @click="mobileMenuOpen = false"
    />
    <transition
      enter-active-class="transition-transform duration-200 ease-out"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-150 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div
        v-if="mobileMenuOpen"
        class="fixed inset-y-0 left-0 z-50 w-60 bg-white shadow-xl md:hidden"
      >
        <AppSidebar />
      </div>
    </transition>

    <!-- Main content -->
    <div class="md:pl-60 flex flex-col min-h-screen">
      <AppTopBar @toggle-mobile-menu="mobileMenuOpen = !mobileMenuOpen" />
      <main class="flex-1 overflow-hidden">
        <slot />
      </main>
    </div>
  </div>
</template>
