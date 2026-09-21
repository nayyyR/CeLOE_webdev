import { defineStore } from 'pinia'
import { computed, reactive } from 'vue'

export const useTicketStore = defineStore('tickets', () => {
  const roles = [
    { id: 1, name: 'Super Admin' },
    { id: 2, name: 'Admin' },
    { id: 3, name: 'Employee' },
    { id: 4, name: 'User' },
  ]

  const divisions = [
    { id: 1, name: 'General' },
    { id: 2, name: 'Engineering' },
    { id: 3, name: 'Design' },
    { id: 4, name: 'Support' },
  ]

  const users = reactive([
    { id: 1, name: 'Arya Wicaksono', username: 'arya', email: 'arya@celoe.id', roleId: 2, divisionId: 1, status: 'active', createdAt: '2026-08-01T08:00:00Z' },
    { id: 2, name: 'Diana Putri', username: 'diana', email: 'diana@celoe.id', roleId: 3, divisionId: 2, status: 'active', createdAt: '2026-08-05T09:00:00Z' },
    { id: 3, name: 'Rizky Pratama', username: 'rizky', email: 'rizky@celoe.id', roleId: 3, divisionId: 2, status: 'active', createdAt: '2026-08-10T10:00:00Z' },
    { id: 4, name: 'Sari Dewi', username: 'sari', email: 'sari@celoe.id', roleId: 4, divisionId: 1, status: 'active', createdAt: '2026-08-12T08:30:00Z' },
    { id: 5, name: 'Budi Santoso', username: 'budi', email: 'budi@celoe.id', roleId: 3, divisionId: 3, status: 'active', createdAt: '2026-08-15T09:15:00Z' },
    { id: 6, name: 'Maya Anggraeni', username: 'maya', email: 'maya@celoe.id', roleId: 1, divisionId: 1, status: 'active', createdAt: '2026-07-20T08:00:00Z' },
    { id: 7, name: 'Andi Firmansyah', username: 'andi', email: 'andi@celoe.id', roleId: 4, divisionId: 1, status: 'inactive', createdAt: '2026-08-20T10:00:00Z' },
  ])

  const tickets = reactive([
    {
      id: 1,
      ticketNumber: 'TKT-000001',
      subject: 'Set up CI/CD pipeline for staging',
      description: 'Configure GitHub Actions to auto-deploy the staging branch on merge.',
      status: 'closed',
      priority: 'high',
      assignedEmployeeId: 2,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-10T08:00:00Z',
      updatedAt: '2026-09-18T14:30:00Z',
    },
    {
      id: 2,
      ticketNumber: 'TKT-000002',
      subject: 'Design system: define color tokens',
      description: 'Establish a consistent palette for all components in the design system.',
      status: 'closed',
      priority: 'medium',
      assignedEmployeeId: 5,
      createdBy: 4,
      targetDivisionId: 3,
      createdAt: '2026-09-11T09:15:00Z',
      updatedAt: '2026-09-17T16:45:00Z',
    },
    {
      id: 3,
      ticketNumber: 'TKT-000003',
      subject: 'Migrate authentication to JWT',
      description: 'Replace session-based auth with JWT tokens for API readiness.',
      status: 'in_progress',
      priority: 'high',
      assignedEmployeeId: 2,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-12T10:00:00Z',
      updatedAt: '2026-09-20T11:20:00Z',
    },
    {
      id: 4,
      ticketNumber: 'TKT-000004',
      subject: 'Optimize database queries on dashboard',
      description: 'The main dashboard query loads in 4s. Target under 500ms.',
      status: 'in_progress',
      priority: 'urgent',
      assignedEmployeeId: 3,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-13T07:30:00Z',
      updatedAt: '2026-09-20T09:10:00Z',
    },
    {
      id: 5,
      ticketNumber: 'TKT-000005',
      subject: 'Implement ticket filtering UI',
      description: 'Add search bar and filter chips for status, priority, and tags.',
      status: 'in_progress',
      priority: 'high',
      assignedEmployeeId: 3,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-14T08:45:00Z',
      updatedAt: '2026-09-21T07:00:00Z',
    },
    {
      id: 6,
      ticketNumber: 'TKT-000006',
      subject: 'Write API docs for ticket endpoints',
      description: 'Document all ticket CRUD endpoints using OpenAPI spec.',
      status: 'resolved',
      priority: 'medium',
      assignedEmployeeId: 2,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-14T13:20:00Z',
      updatedAt: '2026-09-20T15:30:00Z',
    },
    {
      id: 7,
      ticketNumber: 'TKT-000007',
      subject: 'Add dark mode toggle',
      description: 'Implement a theme switcher that persists preference in localStorage.',
      status: 'open',
      priority: 'low',
      assignedEmployeeId: null,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-15T10:00:00Z',
      updatedAt: '2026-09-15T10:00:00Z',
    },
    {
      id: 8,
      ticketNumber: 'TKT-000008',
      subject: 'Refactor notification service',
      description: 'Decouple notification logic from controllers into a dedicated service.',
      status: 'open',
      priority: 'medium',
      assignedEmployeeId: null,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-16T09:00:00Z',
      updatedAt: '2026-09-16T09:00:00Z',
    },
    {
      id: 9,
      ticketNumber: 'TKT-000009',
      subject: 'Mobile responsive sidebar',
      description: 'Sidebar should collapse into a hamburger menu on screens below 768px.',
      status: 'in_progress',
      priority: 'medium',
      assignedEmployeeId: 3,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-16T11:30:00Z',
      updatedAt: '2026-09-16T11:30:00Z',
    },
    {
      id: 10,
      ticketNumber: 'TKT-000010',
      subject: 'Set up error monitoring with Sentry',
      description: 'Integrate Sentry for frontend and backend error tracking.',
      status: 'open',
      priority: 'low',
      assignedEmployeeId: null,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-17T08:00:00Z',
      updatedAt: '2026-09-17T08:00:00Z',
    },
    {
      id: 11,
      ticketNumber: 'TKT-000011',
      subject: 'Implement role-based access control',
      description: 'Enforce permissions for admin, developer, and viewer roles across all views.',
      status: 'open',
      priority: 'high',
      assignedEmployeeId: null,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-18T10:00:00Z',
      updatedAt: '2026-09-18T10:00:00Z',
    },
    {
      id: 12,
      ticketNumber: 'TKT-000012',
      subject: 'Add keyboard shortcuts for power users',
      description: 'Implement common shortcuts: Ctrl+K for search, N for new ticket, etc.',
      status: 'open',
      priority: 'low',
      assignedEmployeeId: null,
      createdBy: 4,
      targetDivisionId: 2,
      createdAt: '2026-09-19T14:00:00Z',
      updatedAt: '2026-09-19T14:00:00Z',
    },
    {
      id: 13,
      ticketNumber: 'TKT-000013',
      subject: 'Fix broken email notifications',
      description: 'Users report not receiving ticket assignment emails since the last deploy.',
      status: 'open',
      priority: 'urgent',
      assignedEmployeeId: null,
      createdBy: 4,
      targetDivisionId: 4,
      createdAt: '2026-09-20T08:00:00Z',
      updatedAt: '2026-09-20T08:00:00Z',
    },
    {
      id: 14,
      ticketNumber: 'TKT-000014',
      subject: 'Update user onboarding flow',
      description: 'Redesign the first-time user experience with a step-by-step wizard.',
      status: 'resolved',
      priority: 'medium',
      assignedEmployeeId: 5,
      createdBy: 4,
      targetDivisionId: 3,
      createdAt: '2026-09-19T10:00:00Z',
      updatedAt: '2026-09-21T09:00:00Z',
    },
  ])

  const activityLogs = reactive([
    { id: 1, ticketId: 1, userId: 2, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 2 }, createdAt: '2026-09-12T08:00:00Z' },
    { id: 2, ticketId: 1, userId: 2, action: 'status_changed', oldStatus: 'in_progress', newStatus: 'resolved', metadata: { changed_by_role: 'employee' }, createdAt: '2026-09-17T14:00:00Z' },
    { id: 3, ticketId: 1, userId: 1, action: 'status_changed', oldStatus: 'resolved', newStatus: 'closed', metadata: { changed_by_role: 'admin' }, createdAt: '2026-09-18T14:30:00Z' },
    { id: 4, ticketId: 2, userId: 5, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 5 }, createdAt: '2026-09-12T10:00:00Z' },
    { id: 5, ticketId: 2, userId: 5, action: 'status_changed', oldStatus: 'in_progress', newStatus: 'resolved', metadata: { changed_by_role: 'employee' }, createdAt: '2026-09-16T16:00:00Z' },
    { id: 6, ticketId: 2, userId: 1, action: 'status_changed', oldStatus: 'resolved', newStatus: 'closed', metadata: { changed_by_role: 'admin' }, createdAt: '2026-09-17T16:45:00Z' },
    { id: 7, ticketId: 3, userId: 2, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 2 }, createdAt: '2026-09-13T09:00:00Z' },
    { id: 8, ticketId: 3, userId: 2, action: 'thread_added', oldStatus: null, newStatus: null, metadata: { thread_id: 't001', attachments_count: 1 }, createdAt: '2026-09-18T10:15:00Z' },
    { id: 9, ticketId: 4, userId: 3, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 3 }, createdAt: '2026-09-14T08:00:00Z' },
    { id: 10, ticketId: 5, userId: 3, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 3 }, createdAt: '2026-09-15T07:30:00Z' },
    { id: 11, ticketId: 5, userId: 3, action: 'thread_added', oldStatus: null, newStatus: null, metadata: { thread_id: 't002', attachments_count: 0 }, createdAt: '2026-09-19T14:00:00Z' },
    { id: 12, ticketId: 6, userId: 2, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 2 }, createdAt: '2026-09-15T09:00:00Z' },
    { id: 13, ticketId: 6, userId: 2, action: 'status_changed', oldStatus: 'in_progress', newStatus: 'resolved', metadata: { changed_by_role: 'employee' }, createdAt: '2026-09-20T15:30:00Z' },
    { id: 14, ticketId: 9, userId: 3, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 3 }, createdAt: '2026-09-16T12:00:00Z' },
    { id: 15, ticketId: 14, userId: 5, action: 'ticket_assigned', oldStatus: 'open', newStatus: 'in_progress', metadata: { assigned_employee_id: 5 }, createdAt: '2026-09-19T11:00:00Z' },
    { id: 16, ticketId: 14, userId: 5, action: 'status_changed', oldStatus: 'in_progress', newStatus: 'resolved', metadata: { changed_by_role: 'employee' }, createdAt: '2026-09-21T09:00:00Z' },
    { id: 17, ticketId: 7, userId: 4, action: 'ticket_created', oldStatus: null, newStatus: 'open', metadata: { subject: 'Add dark mode toggle' }, createdAt: '2026-09-15T10:00:00Z' },
    { id: 18, ticketId: 8, userId: 4, action: 'ticket_created', oldStatus: null, newStatus: 'open', metadata: { subject: 'Refactor notification service' }, createdAt: '2026-09-16T09:00:00Z' },
    { id: 19, ticketId: 10, userId: 4, action: 'ticket_created', oldStatus: null, newStatus: 'open', metadata: { subject: 'Set up error monitoring with Sentry' }, createdAt: '2026-09-17T08:00:00Z' },
    { id: 20, ticketId: 11, userId: 4, action: 'ticket_created', oldStatus: null, newStatus: 'open', metadata: { subject: 'Implement role-based access control' }, createdAt: '2026-09-18T10:00:00Z' },
    { id: 21, ticketId: 13, userId: 4, action: 'ticket_created', oldStatus: null, newStatus: 'open', metadata: { subject: 'Fix broken email notifications' }, createdAt: '2026-09-20T08:00:00Z' },
  ])

  const currentUser = reactive({
    id: 1,
    name: 'Arya Wicaksono',
    username: 'arya',
    email: 'arya@celoe.id',
    roleId: 2,
    divisionId: 1,
    joinedAt: '2026-08-01T08:00:00Z',
  })

  const filters = reactive({
    search: '',
    priority: null,
    status: null,
    division: null,
  })

  const userFilters = reactive({
    search: '',
    role: null,
    division: null,
  })

  const currentRole = computed(() => getRoleById(currentUser.roleId))
  const currentDivision = computed(() => getDivisionById(currentUser.divisionId))
  const isAdmin = computed(() => currentUser.roleId === 1 || currentUser.roleId === 2)
  const canCreateTickets = computed(() => currentUser.roleId === 4)
  const canAssignTickets = computed(() => currentUser.roleId === 1 || currentUser.roleId === 2)

  const visibleTickets = computed(() => {
    if (isAdmin.value) return [...tickets]
    if (currentUser.roleId === 3) {
      return tickets.filter((t) => t.assignedEmployeeId === currentUser.id)
    }
    return tickets.filter((t) => t.createdBy === currentUser.id)
  })

  const filteredTickets = computed(() => {
    return visibleTickets.value.filter((ticket) => {
      if (filters.search) {
        const query = filters.search.toLowerCase()
        if (
          !ticket.subject.toLowerCase().includes(query) &&
          !ticket.ticketNumber.toLowerCase().includes(query)
        ) return false
      }
      if (filters.priority && ticket.priority !== filters.priority) return false
      if (filters.status && ticket.status !== filters.status) return false
      if (filters.division && ticket.targetDivisionId !== filters.division) return false
      return true
    })
  })

  const filteredUsers = computed(() => {
    return users.filter((user) => {
      if (userFilters.search) {
        const query = userFilters.search.toLowerCase()
        if (
          !user.name.toLowerCase().includes(query) &&
          !user.email.toLowerCase().includes(query) &&
          !user.username.toLowerCase().includes(query)
        ) return false
      }
      if (userFilters.role && user.roleId !== userFilters.role) return false
      if (userFilters.division && user.divisionId !== userFilters.division) return false
      return true
    })
  })

  const ticketStats = computed(() => {
    const all = visibleTickets.value
    return {
      total: all.length,
      open: all.filter((t) => t.status === 'open').length,
      inProgress: all.filter((t) => t.status === 'in_progress').length,
      resolved: all.filter((t) => t.status === 'resolved').length,
      closed: all.filter((t) => t.status === 'closed').length,
    }
  })

  const ticketsCreatedLast7Days = computed(() => {
    const now = new Date()
    const result = []
    for (let i = 6; i >= 0; i--) {
      const date = new Date(now)
      date.setDate(date.getDate() - i)
      const dayStr = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
      const dateStr = date.toISOString().slice(0, 10)
      const count = tickets.filter((t) => t.createdAt.slice(0, 10) === dateStr).length
      result.push({ label: dayStr, count })
    }
    return result
  })

  function updateTicketStatus(ticketId, newStatus) {
    const ticket = tickets.find((t) => t.id === ticketId)
    if (ticket) {
      const oldStatus = ticket.status
      ticket.status = newStatus
      ticket.updatedAt = new Date().toISOString()
      activityLogs.push({
        id: Math.max(0, ...activityLogs.map((l) => l.id)) + 1,
        ticketId: ticket.id,
        userId: currentUser.id,
        action: 'status_changed',
        oldStatus,
        newStatus,
        metadata: { changed_by_role: currentRole.value?.name.toLowerCase() },
        createdAt: new Date().toISOString(),
      })
    }
  }

  function assignTicket(ticketId, employeeId) {
    const ticket = tickets.find((t) => t.id === ticketId)
    if (ticket) {
      ticket.assignedEmployeeId = employeeId
      ticket.status = 'in_progress'
      ticket.updatedAt = new Date().toISOString()
      activityLogs.push({
        id: Math.max(0, ...activityLogs.map((l) => l.id)) + 1,
        ticketId: ticket.id,
        userId: currentUser.id,
        action: 'ticket_assigned',
        oldStatus: 'open',
        newStatus: 'in_progress',
        metadata: { assigned_employee_id: employeeId },
        createdAt: new Date().toISOString(),
      })
    }
  }

  function addTicket(data) {
    const id = Math.max(0, ...tickets.map((t) => t.id)) + 1
    const now = new Date().toISOString()
    const ticketNumber = `TKT-${String(id).padStart(6, '0')}`
    tickets.push({
      id,
      ticketNumber,
      subject: data.subject,
      description: data.description ?? '',
      status: 'open',
      priority: data.priority ?? 'medium',
      assignedEmployeeId: null,
      createdBy: currentUser.id,
      targetDivisionId: data.targetDivisionId ?? 2,
      createdAt: now,
      updatedAt: now,
    })
  }

  function getUserById(userId) {
    return users.find((u) => u.id === userId) ?? null
  }

  function getRoleById(roleId) {
    return roles.find((r) => r.id === roleId) ?? null
  }

  function getDivisionById(divisionId) {
    return divisions.find((d) => d.id === divisionId) ?? null
  }

  function getEmployeesByDivision(divisionId) {
    return users.filter((u) => u.roleId === 3 && u.divisionId === divisionId && u.status === 'active')
  }

  function updateUserProfile(data) {
    if (data.name !== undefined) currentUser.name = data.name
    if (data.email !== undefined) currentUser.email = data.email
    if (data.username !== undefined) currentUser.username = data.username
  }

  function updateUserPassword() {
    return true
  }

  return {
    roles,
    divisions,
    users,
    tickets,
    activityLogs,
    currentUser,
    filters,
    userFilters,
    filteredUsers,
    currentRole,
    currentDivision,
    isAdmin,
    canCreateTickets,
    canAssignTickets,
    visibleTickets,
    filteredTickets,
    ticketStats,
    ticketsCreatedLast7Days,
    updateTicketStatus,
    assignTicket,
    addTicket,
    getUserById,
    getRoleById,
    getDivisionById,
    getEmployeesByDivision,
    updateUserProfile,
    updateUserPassword,
  }
})
