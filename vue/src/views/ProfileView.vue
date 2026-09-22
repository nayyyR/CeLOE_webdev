<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useTicketStore } from '@/stores/ticketStore'
import { getInitials, formatDate } from '@/utils/format'

const store = useTicketStore()
const router = useRouter()

const profileForm = ref({
  name: store.currentUser.name,
  email: store.currentUser.email,
  username: store.currentUser.username,
})

const passwordForm = ref({
  current: '',
  new: '',
  confirm: '',
})

const profileSaved = ref(false)
const passwordSaved = ref(false)
const profileTouched = ref(false)
const passwordTouched = ref(false)

const profileErrors = computed(() => {
  if (!profileTouched.value) return {}
  const errors = {}
  if (!profileForm.value.name.trim()) errors.name = 'Name is required'
  if (!profileForm.value.email.trim()) errors.email = 'Email is required'
  if (!profileForm.value.username.trim()) errors.username = 'Username is required'
  return errors
})

const passwordErrors = computed(() => {
  if (!passwordTouched.value) return {}
  const errors = {}
  if (!passwordForm.value.current) errors.current = 'Current password is required'
  if (!passwordForm.value.new) errors.new = 'New password is required'
  if (passwordForm.value.new && passwordForm.value.new.length < 8) errors.new = 'At least 8 characters'
  if (passwordForm.value.new && passwordForm.value.confirm && passwordForm.value.new !== passwordForm.value.confirm) errors.confirm = 'Passwords do not match'
  return errors
})

function saveProfile() {
  profileTouched.value = true
  if (Object.keys(profileErrors.value).length > 0) return
  store.updateUserProfile(profileForm.value)
  profileSaved.value = true
  setTimeout(() => { profileSaved.value = false }, 2000)
}

function savePassword() {
  passwordTouched.value = true
  if (Object.keys(passwordErrors.value).length > 0) return
  store.updateUserPassword()
  passwordSaved.value = true
  passwordForm.value = { current: '', new: '', confirm: '' }
  passwordTouched.value = false
  setTimeout(() => { passwordSaved.value = false }, 2000)
}

function handleLogout() {
  alert('You have been signed out.')
  router.push({ name: 'dashboard' })
}

const userStats = computed(() => {
  const role = store.currentRole?.name
  if (role === 'Admin' || role === 'Super Admin') {
    return [
      { label: 'Total Routed', value: () => store.tickets.length },
      { label: 'Active Employees', value: () => store.users.filter((u) => u.roleId === 3 && u.status === 'active').length },
      { label: 'Divisions', value: () => store.divisions.length },
    ]
  }
  if (role === 'Employee') {
    return [
      { label: 'In Progress', value: () => store.tickets.filter((t) => t.assignedEmployeeId === store.currentUser.id && t.status === 'in_progress').length },
      { label: 'Resolved', value: () => store.tickets.filter((t) => t.assignedEmployeeId === store.currentUser.id && (t.status === 'resolved' || t.status === 'closed')).length },
      { label: 'Activity Entries', value: () => store.activityLogs.filter((l) => l.userId === store.currentUser.id).length },
    ]
  }
  return [
    { label: 'Tickets Created', value: () => store.tickets.filter((t) => t.createdBy === store.currentUser.id).length },
    { label: 'Open Tickets', value: () => store.tickets.filter((t) => t.createdBy === store.currentUser.id && t.status === 'open').length },
    { label: 'Activity Entries', value: () => store.activityLogs.filter((l) => l.userId === store.currentUser.id).length },
  ]
})
</script>

<template>
  <div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
      <div class="relative h-48 bg-gradient-to-br from-celoe-500 via-celoe-600 to-celoe-800">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wOCI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyem0wLTRWMjhIMjR2Mmgxem0tNC0ydi0ySDE4djJoMnptNCAydjJIMjJ2LTJoNHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-50" />
      </div>

      <div class="px-6 relative">
        <div class="-mt-16 mb-4 flex items-end justify-between">
          <div class="w-32 h-32 rounded-full bg-white border-4 border-white shadow-lg flex items-center justify-center shrink-0">
            <span class="text-3xl font-bold text-celoe-600">{{ getInitials(store.currentUser.name) }}</span>
          </div>
          <button
            class="mb-2 flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-red-600 bg-white border border-red-200 rounded-full hover:bg-red-50 transition-colors shrink-0"
            @click="handleLogout"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Sign Out
          </button>
        </div>

        <div class="mb-5">
          <h2 class="text-xl font-bold text-zinc-900">{{ store.currentUser.name }}</h2>
          <p class="text-[14px] text-zinc-400 mt-0.5">@{{ store.currentUser.username }}</p>
          <div class="flex items-center gap-2 mt-3">
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-celoe-50 text-celoe-700">
              {{ store.currentRole?.name }}
            </span>
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-600">
              {{ store.currentDivision?.name }}
            </span>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 pb-5 border-b border-zinc-100">
          <div v-for="stat in userStats" :key="stat.label" class="flex items-baseline gap-1.5">
            <span class="text-[15px] font-bold text-zinc-900 tabular-nums">{{ stat.value() }}</span>
            <span class="text-[13px] text-zinc-400">{{ stat.label }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-zinc-100">
        <h3 class="text-[14px] font-semibold text-zinc-900">Account Information</h3>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-zinc-50">
        <div class="divide-y divide-zinc-50">
          <div class="flex items-center justify-between px-6 py-3.5">
            <span class="text-[13px] text-zinc-400">Role</span>
            <span class="text-[13px] text-zinc-700 font-medium">{{ store.currentRole?.name }}</span>
          </div>
          <div class="flex items-center justify-between px-6 py-3.5">
            <span class="text-[13px] text-zinc-400">Division</span>
            <span class="text-[13px] text-zinc-700 font-medium">{{ store.currentDivision?.name }}</span>
          </div>
        </div>
        <div class="divide-y divide-zinc-50">
          <div class="flex items-center justify-between px-6 py-3.5">
            <span class="text-[13px] text-zinc-400">Member Since</span>
            <span class="text-[13px] text-zinc-700 font-medium">{{ formatDate(store.currentUser.joinedAt) }}</span>
          </div>
          <div class="flex items-center justify-between px-6 py-3.5">
            <span class="text-[13px] text-zinc-400">Status</span>
            <span class="text-[13px] font-medium text-emerald-600">Active</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-100">
          <h3 class="text-[14px] font-semibold text-zinc-900">Profile Details</h3>
          <p class="text-[12px] text-zinc-400 mt-0.5">Update your personal information</p>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Full Name</label>
            <input
              v-model="profileForm.name"
              type="text"
              :class="[
                'w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150',
                profileErrors.name ? 'border-red-300' : 'border-zinc-200',
              ]"
            />
            <p v-if="profileErrors.name" class="text-[11px] text-red-500 mt-1">{{ profileErrors.name }}</p>
          </div>
          <div>
            <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Email</label>
            <input
              v-model="profileForm.email"
              type="email"
              :class="[
                'w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150',
                profileErrors.email ? 'border-red-300' : 'border-zinc-200',
              ]"
            />
            <p v-if="profileErrors.email" class="text-[11px] text-red-500 mt-1">{{ profileErrors.email }}</p>
          </div>
          <div>
            <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Username</label>
            <input
              v-model="profileForm.username"
              type="text"
              :class="[
                'w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150',
                profileErrors.username ? 'border-red-300' : 'border-zinc-200',
              ]"
            />
            <p v-if="profileErrors.username" class="text-[11px] text-red-500 mt-1">{{ profileErrors.username }}</p>
          </div>
        </div>
        <div class="flex items-center justify-between px-6 py-4 border-t border-zinc-100 bg-zinc-50/50">
          <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="profileSaved" class="text-[12px] font-medium text-emerald-600 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Saved
            </span>
          </transition>
          <button
            :disabled="Object.keys(profileErrors).length > 0"
            :class="[
              'px-4 py-2 text-[13px] font-medium rounded-lg transition-all duration-150 ml-auto',
              Object.keys(profileErrors).length > 0
                ? 'bg-zinc-100 text-zinc-400 cursor-not-allowed'
                : 'bg-zinc-800 text-white hover:bg-zinc-900',
            ]"
            @click="saveProfile"
          >
            Save Changes
          </button>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-100">
          <h3 class="text-[14px] font-semibold text-zinc-900">Change Password</h3>
          <p class="text-[12px] text-zinc-400 mt-0.5">Ensure your account stays secure</p>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Current Password</label>
            <input
              v-model="passwordForm.current"
              type="password"
              :class="[
                'w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150',
                passwordErrors.current ? 'border-red-300' : 'border-zinc-200',
              ]"
              placeholder="Enter current password"
            />
            <p v-if="passwordErrors.current" class="text-[11px] text-red-500 mt-1">{{ passwordErrors.current }}</p>
          </div>
          <div>
            <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">New Password</label>
            <input
              v-model="passwordForm.new"
              type="password"
              :class="[
                'w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150',
                passwordErrors.new ? 'border-red-300' : 'border-zinc-200',
              ]"
              placeholder="Enter new password"
            />
            <p v-if="passwordErrors.new" class="text-[11px] text-red-500 mt-1">{{ passwordErrors.new }}</p>
          </div>
          <div>
            <label class="block text-[12px] font-medium text-zinc-500 mb-1.5">Confirm Password</label>
            <input
              v-model="passwordForm.confirm"
              type="password"
              :class="[
                'w-full px-3 py-2 text-[13px] text-zinc-700 bg-zinc-50 border rounded-lg placeholder:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-celoe-500/20 focus:border-celoe-400 transition-all duration-150',
                passwordErrors.confirm ? 'border-red-300' : 'border-zinc-200',
              ]"
              placeholder="Confirm new password"
            />
            <p v-if="passwordErrors.confirm" class="text-[11px] text-red-500 mt-1">{{ passwordErrors.confirm }}</p>
          </div>
        </div>
        <div class="flex items-center justify-between px-6 py-4 border-t border-zinc-100 bg-zinc-50/50">
          <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="passwordSaved" class="text-[12px] font-medium text-emerald-600 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Updated
            </span>
          </transition>
          <button
            :disabled="Object.keys(passwordErrors).length > 0"
            :class="[
              'px-4 py-2 text-[13px] font-medium rounded-lg transition-all duration-150 ml-auto',
              Object.keys(passwordErrors).length > 0
                ? 'bg-zinc-100 text-zinc-400 cursor-not-allowed'
                : 'bg-zinc-800 text-white hover:bg-zinc-900',
            ]"
            @click="savePassword"
          >
            Update Password
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
