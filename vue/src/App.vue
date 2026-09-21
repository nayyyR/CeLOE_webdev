<script setup>
import AppDashboardLayout from '@/components/layout/AppDashboardLayout.vue'
</script>

<template>
  <AppDashboardLayout>
    <RouterView v-slot="{ Component }">
      <transition
        enter-active-class="transition-opacity duration-150 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-100 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
        mode="out-in"
      >
        <component :is="Component" />
      </transition>
    </RouterView>
  </AppDashboardLayout>
</template>
