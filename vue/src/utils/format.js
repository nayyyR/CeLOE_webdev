export function getInitials(name) {
  return name.split(' ').map((n) => n[0]).join('')
}

export function formatDate(dateString, options = { month: 'short', day: 'numeric', year: 'numeric' }) {
  return new Date(dateString).toLocaleDateString('en-US', options)
}

export function timeAgo(dateString) {
  const seconds = Math.floor((new Date() - new Date(dateString)) / 1000)
  const intervals = [
    { label: 'd', seconds: 86400 },
    { label: 'h', seconds: 3600 },
    { label: 'm', seconds: 60 },
  ]
  for (const interval of intervals) {
    const count = Math.floor(seconds / interval.seconds)
    if (count >= 1) return `${count}${interval.label}`
  }
  return 'just now'
}

export function statusColor(status) {
  const map = {
    open: 'bg-zinc-100 text-zinc-600',
    in_progress: 'bg-blue-100 text-blue-700',
    resolved: 'bg-emerald-100 text-emerald-700',
    closed: 'bg-zinc-100 text-zinc-500',
    review: 'bg-amber-100 text-amber-700',
    done: 'bg-zinc-100 text-zinc-500',
    backlog: 'bg-zinc-100 text-zinc-400',
    todo: 'bg-sky-100 text-sky-700',
  }
  return map[status] ?? 'bg-zinc-100 text-zinc-500'
}

export function priorityColor(priority) {
  const map = {
    urgent: 'bg-red-50 text-red-700',
    high: 'bg-amber-50 text-amber-700',
    medium: 'bg-blue-50 text-blue-700',
    low: 'bg-zinc-100 text-zinc-500',
  }
  return map[priority] ?? 'bg-zinc-100 text-zinc-500'
}

export function actionColor(action) {
  const map = {
    ticket_created: 'bg-emerald-50 text-emerald-700',
    ticket_assigned: 'bg-blue-50 text-blue-700',
    status_changed: 'bg-amber-50 text-amber-700',
    thread_added: 'bg-violet-50 text-violet-700',
  }
  return map[action] ?? 'bg-zinc-50 text-zinc-600'
}

export function actionLabel(action, style = 'title') {
  const titleMap = {
    ticket_created: 'Created',
    ticket_assigned: 'Assigned',
    status_changed: 'Status Changed',
    thread_added: 'Reply Added',
  }
  const sentenceMap = {
    ticket_created: 'created a ticket',
    ticket_assigned: 'assigned a ticket',
    status_changed: 'changed status',
    thread_added: 'replied to a ticket',
  }
  const map = style === 'sentence' ? sentenceMap : titleMap
  return map[action] ?? action
}
