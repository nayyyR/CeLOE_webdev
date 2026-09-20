# Celoe API Reference

> Multi-Role Ticketing System - Backend API Documentation
> Version: 1.0 | Last Updated: 2026-09-20

## Table of Contents

- [Base URL](#base-url)
- [Authentication](#authentication)
- [Response Format](#response-format)
- [Error Codes](#error-codes)
- [RBAC Matrix](#rbac-matrix)
- [Status Transitions](#status-transitions)
- [Field Vocabulary](#field-vocabulary)
- [Endpoints](#endpoints)
  - [Public - Authentication](#public---authentication)
  - [User - Ticket Management](#user---ticket-management)
  - [Admin - Ticket Management](#admin---ticket-management)
  - [Employee - Ticket Management](#employee---ticket-management)
  - [Super Admin - User Management](#super-admin---user-management)
  - [Super Admin - Role Management](#super-admin---role-management)
  - [Super Admin - Division Management](#super-admin---division-management)
  - [Super Admin - Permission Management](#super-admin---permission-management)
  - [Super Admin - Master Ticket Management](#super-admin---master-ticket-management)

---

## Base URL

`
http://localhost:8000/api
`

All endpoints are prefixed with /api.

---

## Authentication

Celoe uses **Laravel Sanctum** for API authentication. Every authenticated request must include a Bearer token in the Authorization header.

`
Authorization: Bearer {token}
`

Obtain a token via POST /api/auth/login. The token is returned in the data.token field of the login response.

---

## Response Format

All endpoints return a standardized JSON envelope via the ApiResponse trait.

### Success Response (200 / 201)

`json
{
  "success": true,
  "message": "Operation successful.",
  "data": {},
  "errors": null
}
`

### Error Response (4xx / 5xx)

`json
{
  "success": false,
  "message": "Something went wrong.",
  "data": null,
  "errors": {
    "field": ["Error message for this field."]
  }
}
`

When errors is an object, keys are field names and values are arrays of validation error messages. When the error is not a validation error (e.g., 403 Forbidden), errors is Null.

### Paginated Response (200)

`json
{
  "success": true,
  "message": "Retrieved successfully.",
  "data": [],
  "errors": null,
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 50
  }
}
`

---

## Error Codes

| HTTP Code | Meaning |
|-----------|---------|
| 200 | OK |
| 201 | Created |
| 401 | Unauthenticated (missing or invalid token) |
| 403 | Forbidden (role+division mismatch or missing permission) |
| 404 | Resource not found |
| 422 | Validation error (invalid input) |

---

## RBAC Matrix

Every authenticated request passes through two gatekeeping middlewares:

1. **role.division** - Validates the role+division combination is one of the 4 allowed pairs.
2. **permission:{name}** - Validates the user's role has the required permission.

| Role | Division | Allowed Access |
|------|----------|----------------|
| User | general | Create tickets, reply, close/reopen own resolved tickets |
| Employee | *(any non-general)* | View assigned tickets, add threads, resolve assigned tickets |
| Admin | general | View all tickets, assign tickets to employees |
| Super Admin | general | Full CRUD on users, roles, divisions, permissions, and all tickets |

Any role+division combination not in this table returns **403** with "Your role and division combination is not authorized.".

---

## Status Transitions

Tickets follow a strict state machine:

`
open  --------->  in_progress  --------->  resolved  --------->  closed
 ^                       ^                       |
 |                       |                       |
 +-----------------------+-----------------------+
              (User reopens)
`

| Current Status | Allowed New Status | Who Can Do It |
|---------------|-------------------|---------------|
| open | in_progress | Admin (via assign) |
| in_progress | resolved | Employee (assigned to ticket) |
| resolved | closed | User (ticket creator) |
| resolved | in_progress | User (ticket creator, reopen) |

**Super Admin** can set any ticket to any status without transition validation.

Invalid transitions return **422**.

---

## Field Vocabulary

Use these exact field names. Do not substitute synonyms.

| Context | Correct Field | Wrong (Do Not Use) |
|---------|--------------|-------------------|
| Thread content | body | message, content, 	ext |
| Ticket destination division | 	arget_division_id | division_id |
| Assigned employee | assigned_employee_id | assigned_to, employee_id |
| Ticket number | 	icket_number | number, code |
| Creator user ID | created_by | user_id (on ticket), author_id |
---

## Endpoints

---

### PUBLIC - Authentication

---

### POST /api/auth/register

Register a new user account. The user is automatically assigned the User role and general division.

**Middleware:** None

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | required | max:255 |
| email | string | required | email, unique:users,email |
| password | string | required | min:8, confirmed |
| password_confirmation | string | required | Must match password |

**Response (201):**

`json
{
  "success": true,
  "message": "Registration successful.",
  "data": {
    "id": 1,
    "name": "John Doe",
    "username": "john",
    "email": "john@example.com",
    "role_id": 1,
    "division_id": 1,
    "role": { "id": 1, "name": "User" },
    "division": { "id": 1, "name": "general" }
  },
  "errors": null
}
`

> **Note:** The username is auto-generated from the email prefix. If it already exists, incrementing numbers are appended (e.g., john1, john2).

---

### POST /api/auth/login

Authenticate and receive a Bearer token.

**Middleware:** None

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| login | string | required | Email or username |
| password | string | required | |

**Response (200):**

`json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "1|abc123...",
    "token_type": "Bearer",
    "dashboard": "user",
    "user": {
      "id": 1,
      "name": "John Doe",
      "username": "john",
      "email": "john@example.com",
      "role": { "id": 1, "name": "User" },
      "division": { "id": 1, "name": "general" }
    }
  },
  "errors": null
}
`

The dashboard field indicates which frontend view to render: "user", "employee", "admin", or "superadmin".

**Error Responses:**

| Status | Message | When |
|--------|---------|------|
| 401 | "Invalid credentials." | Wrong email/username or password |
| 403 | "Your role and division combination is not authorized." | Invalid role+division pair |

---

### POST /api/auth/logout

Revoke the current access token.

**Middleware:** auth:sanctum, role.division

**Request Body:** None

**Response (200):**

`json
{
  "success": true,
  "message": "Logout successful.",
  "data": null,
  "errors": null
}
`

---

### USER - Ticket Management

All User endpoints require: auth:sanctum + role.division + permission:create_tickets

---

### GET /api/user/tickets

List the authenticated user's own tickets (paginated, 10 per page).

**Middleware:** auth:sanctum, role.division, permission:create_tickets

**Query Parameters (optional):**

| Param | Type | Description |
|-------|------|-------------|
| search | string | Filter by subject (LIKE match) |
| status | string | Filter by exact status |
| division_id | integer | Filter by target division |

**Response (200):** Paginated ticket array. See [Response Format](#response-format).

Each ticket object:

`json
{
  "id": 1,
  "ticket_number": "TKT-000001",
  "subject": "Laptop tidak bisa menyala",
  "description": "...",
  "priority": "medium",
  "status": "open",
  "created_by": 1,
  "target_division_id": 2,
  "assigned_employee_id": null,
  "resolved_at": null,
  "closed_at": null,
  "last_user_response_at": "2026-09-20T10:00:00.000000Z",
  "last_activity_at": "2026-09-20T10:00:00.000000Z",
  "creator": { "id": 1, "name": "John Doe", "username": "john" },
  "targetDivision": { "id": 2, "name": "IT Support" },
  "assignedEmployee": null
}
`

---

### POST /api/user/tickets

Create a new ticket. Automatically creates the first thread (using description as the body) and an activity log.

**Middleware:** auth:sanctum, role.division, permission:create_tickets

**Request Body (multipart/form-data):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| subject | string | required | max:255 |
| description | string | required | |
| target_division_id | integer | required | exists:divisions,id (excludes general) |
| attachments | file[] | nullable | Max 5 files, 2MB each |
| attachments.* | file | if present | mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx |

**Response (201):**

`json
{
  "success": true,
  "message": "Ticket created successfully.",
  "data": {
    "id": 1,
    "ticket_number": "TKT-000001",
    "subject": "Laptop tidak bisa menyala",
    "description": "...",
    "priority": "medium",
    "status": "open",
    "created_by": 1,
    "target_division_id": 2,
    "assigned_employee_id": null
  },
  "errors": null
}
`

---

### GET /api/user/tickets/{ticket}

View a single ticket. Users can only view their own tickets.

**Middleware:** auth:sanctum, role.division, permission:create_tickets

**Route Parameter:** 	icket (integer, auto-resolved via route model binding)

**Response (200):** Single ticket object with loaded creator, targetDivision, assignedEmployee.

**Error Responses:**

| Status | Message |
|--------|---------|
| 403 | "You cannot view this ticket." |
| 404 | "Ticket not found." |

---

### POST /api/user/tickets/{ticket}/threads

Add a reply/thread to a ticket.

**Middleware:** auth:sanctum, role.division, permission:create_tickets

**Route Parameter:** 	icket (integer)

**Request Body (multipart/form-data):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| body | string | required | "Body wajib diisi." |
| attachments | file[] | nullable | Max 5 files |
| attachments.* | file | if present | 2MB max, mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx |

**Response (201):**

`json
{
  "success": true,
  "message": "Thread added successfully.",
  "data": {
    "_id": "507f1f77bcf86cd799439011",
    "ticket_id": 1,
    "user_id": 1,
    "body": "Sudah diinstall, mohon dicek.",
    "attachments": [],
    "created_at": "2026-09-20T10:00:00.000000Z"
  },
  "errors": null
}
`

---

### PATCH /api/user/tickets/{ticket}/status

Update ticket status. Users can only transition from resolved to closed or in_progress.

**Middleware:** auth:sanctum, role.division, permission:create_tickets, permission:confirm_resolution

**Route Parameter:** 	icket (integer)

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| status | string | required | in:resolved,closed,in_progress |

**Response (200):** Full ticket object with updated status.

**Error Responses:**

| Status | Message |
|--------|---------|
| 403 | "You cannot modify this ticket." (not the ticket creator) |
| 422 | "Invalid user status transition." |

---

### ADMIN - Ticket Management

All Admin endpoints require: auth:sanctum + role.division + permission:view_all_tickets

---

### GET /api/admin/tickets

List ALL tickets in the system (paginated, 10 per page). No user scoping.

**Middleware:** auth:sanctum, role.division, permission:view_all_tickets

**Query Parameters (optional):** search, status, division_id (same as User index)

**Response (200):** Paginated ticket array (same structure as User index).

---

### PATCH /api/admin/tickets/{ticket}/assign

Assign a ticket to an employee. Changes ticket status to in_progress.

**Middleware:** auth:sanctum, role.division, permission:view_all_tickets, permission:assign_tickets

**Route Parameter:** 	icket (integer)

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| assigned_employee_id | integer | required | exists:users,id |

**Business Rules:**

- Target user must have role Employee
- Employee's division_id must match ticket's target_division_id
- Ticket must be in open status

**Response (200):**

`json
{
  "success": true,
  "message": "Ticket assigned successfully.",
  "data": {
    "id": 1,
    "status": "in_progress",
    "assigned_employee_id": 5,
    "assignedEmployee": { "id": 5, "name": "Jane Tech" }
  },
  "errors": null
}
`

**Error Responses (422 Validation):**

| Message | When |
|---------|------|
| "Selected user is not an employee." | Target user is not an Employee |
| "Employee must belong to the ticket target division." | Division mismatch |
| "Only open tickets can be assigned." | Ticket not in open status |

---

### EMPLOYEE - Ticket Management

All Employee endpoints require: auth:sanctum + role.division + permission:view_assigned_tickets

---

### GET /api/employee/tickets

List tickets assigned to the authenticated employee (paginated, 10 per page).

**Middleware:** auth:sanctum, role.division, permission:view_assigned_tickets

**Query Parameters (optional):** search, status, division_id

**Response (200):** Paginated ticket array (same structure as User index).

---

### PATCH /api/employee/tickets/{ticket}/status

Resolve an assigned ticket. Employees can only transition from in_progress to resolved.

**Middleware:** auth:sanctum, role.division, permission:view_assigned_tickets, permission:resolve_tickets

**Route Parameter:** 	icket (integer)

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| status | string | required | in:resolved,closed,in_progress |

**Response (200):** Full ticket object with status: "resolved" and resolved_at timestamp.

**Error Responses:**

| Status | Message |
|--------|---------|
| 403 | "You are not assigned to this ticket." |
| 422 | "Invalid employee status transition." |

---

### POST /api/employee/tickets/{ticket}/threads

Add a reply/thread to an assigned ticket.

**Middleware:** auth:sanctum, role.division, permission:view_assigned_tickets, permission:reply_tickets

**Route Parameter:** 	icket (integer)

**Request Body (multipart/form-data):** Same as User POST /threads.

**Response (201):** Thread object (same structure as User thread response).

---

### SUPER ADMIN - User Management

All Super Admin endpoints require: auth:sanctum + role.division + permission:manage_users

---

### GET /api/superadmin/users

List all users (paginated, 10 per page) with loaded role and division.

**Response (200):** Paginated user array.

`json
{
  "id": 1,
  "name": "John Doe",
  "username": "john",
  "email": "john@example.com",
  "role_id": 1,
  "division_id": 1,
  "role": { "id": 1, "name": "User" },
  "division": { "id": 1, "name": "general" }
}
`

---

### POST /api/superadmin/users

Create a new user.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | required | max:255 |
| username | string | required | unique:users |
| email | string | required | email, unique:users |
| password | string | required | min:8 |
| role_id | integer | required | exists:roles,id |
| division_id | integer | required | exists:divisions,id |

**Response (201):** Created user object with loaded role and division.

---

### GET /api/superadmin/users/{user}

View a single user.

**Response (200):** User object with loaded role and division.

---

### PUT /api/superadmin/users/{user}

Update a user. All fields are optional (partial update).

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | sometimes | max:255 |
| username | string | sometimes | unique:users,username,{id} |
| email | string | sometimes | email, unique:users,email,{id} |
| password | string | sometimes | min:8 |
| role_id | integer | sometimes | exists:roles,id |
| division_id | integer | sometimes | exists:divisions,id |

**Response (200):** Updated user object with loaded 
ole and division.

---

### DELETE /api/superadmin/users/{user}

Delete a user.

**Response (200):**

`json
{
  "success": true,
  "message": "User deleted successfully.",
  "data": null,
  "errors": null
}
`

---

### SUPER ADMIN - Role Management

Additional middleware: permission:manage_roles

---

### GET /api/superadmin/roles

List all roles with loaded permissions. **Not paginated.**

**Response (200):**

`json
{
  "data": [
    {
      "id": 1,
      "name": "User",
      "permissions": [
        { "id": 1, "name": "create_tickets", "description": "Can create tickets" }
      ]
    }
  ]
}
`

---

### POST /api/superadmin/roles

Create a new role.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | required | max:255, unique:roles |

**Response (201):** Created role object.

---

### GET /api/superadmin/roles/{role}

View a single role with loaded permissions.

---

### PUT /api/superadmin/roles/{role}

Update a role.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | required | max:255, unique:roles,name,{id} |

**Response (200):** Updated role object.

---

### DELETE /api/superadmin/roles/{role}

Delete a role.

**Response (200):** Success message.

---

### SUPER ADMIN - Division Management

---

### GET /api/superadmin/divisions

List all divisions with users_count and 	ickets_count. **Not paginated.**

**Response (200):**

`json
{
  "data": [
    {
      "id": 1,
      "name": "general",
      "users_count": 15,
      "tickets_count": 42
    }
  ]
}
`

---

### POST /api/superadmin/divisions

Create a new division.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| 
ame | string | required | max:255, unique:divisions |

**Response (201):** Created division object.

---

### GET /api/superadmin/divisions/{division}

View a single division with users_count and 	ickets_count.

---

### PUT /api/superadmin/divisions/{division}

Update a division.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| 
ame | string | required | max:255, unique:divisions,name,{id} |

**Response (200):** Updated division object.

---

### DELETE /api/superadmin/divisions/{division}

Delete a division.

**Response (200):** Success message.

---

### SUPER ADMIN - Permission Management

Additional middleware: permission:manage_permissions

---

### GET /api/superadmin/permissions

List all permissions with loaded roles. **Not paginated.**

**Response (200):**

`json
{
  "data": [
    {
      "id": 1,
      "name": "create_tickets",
      "description": "Can create tickets",
      "roles": [{ "id": 1, "name": "User" }]
    }
  ]
}
`

---

### POST /api/superadmin/permissions

Create a new permission.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | required | max:255, unique:permissions |
| description | string | nullable | |

**Response (201):** Created permission object.

---

### GET /api/superadmin/permissions/{permission}

View a single permission with loaded 
oles.

---

### PUT /api/superadmin/permissions/{permission}

Update a permission.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | required | max:255, unique:permissions,name,{id} |
| description | string | nullable | |

**Response (200):** Updated permission object.

---

### DELETE /api/superadmin/permissions/{permission}

Delete a permission.

**Response (200):** Success message.

---

### PUT /api/superadmin/roles/{role}/permissions

Replace all permissions for a role (sync/overwrite). This is a full replacement, not a merge.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| permission_ids | array | required | array |
| permission_ids.* | integer | required | exists:permissions,id |

**Example Request Body:**

`json
{
  "permission_ids": [1, 2, 3]
}
`

**Response (200):**

`json
{
  "success": true,
  "message": "Role permissions updated successfully.",
  "data": {
    "id": 1,
    "name": "User",
    "permissions": [
      { "id": 1, "name": "create_tickets" },
      { "id": 2, "name": "confirm_resolution" }
    ]
  },
  "errors": null
}
`

---

### SUPER ADMIN - Master Ticket Management

Full CRUD on all tickets without status transition validation. Useful for administrative overrides.

---

### GET /api/superadmin/tickets

List ALL tickets (paginated, 10 per page). No scoping.

**Middleware:** auth:sanctum, role.division, permission:manage_users

**Query Parameters (optional):** search, status, division_id

**Response (200):** Paginated ticket array.

---

### POST /api/superadmin/tickets

Create a ticket directly. Does NOT create a thread or activity log (unlike User ticket creation).

**Middleware:** auth:sanctum, role.division, permission:manage_users

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| subject | string | required | max:255 |
| description | string | required | |
| priority | string | required | in:low,medium,high,urgent |
| status | string | required | in:open,in_progress,resolved,closed |
| created_by | integer | required | exists:users,id |
| target_division_id | integer | required | exists:divisions,id |
| assigned_employee_id | integer | nullable | exists:users,id |

**Response (201):** Raw Ticket object.

---

### GET /api/superadmin/tickets/{ticket}

View a single ticket with loaded relationships.

---

### PUT /api/superadmin/tickets/{ticket}

Update a ticket. All fields are optional (partial update). No transition validation.

**Request Body (JSON):**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| subject | string | sometimes | max:255 |
| description | string | sometimes | |
| priority | string | sometimes | in:low,medium,high,urgent |
| status | string | sometimes | in:open,in_progress,resolved,closed |
| target_division_id | integer | sometimes | exists:divisions,id |
| assigned_employee_id | integer | nullable | exists:users,id |

**Response (200):** Updated Ticket object.

---

### DELETE /api/superadmin/tickets/{ticket}

Delete a ticket.

**Response (200):** Success message.

---

## Permission Reference

These are the permission string names checked by middleware throughout the routes:

| Permission Name | Description | Assigned To |
|----------------|-------------|-------------|
| create_tickets | Can create tickets and reply to own tickets | User |
| confirm_resolution | Can close or reopen resolved tickets | User |
| view_all_tickets | Can view all tickets in the system | Admin |
| assign_tickets | Can assign tickets to employees | Admin |
| view_assigned_tickets | Can view tickets assigned to them | Employee |
| resolve_tickets | Can resolve assigned tickets | Employee |
| reply_tickets | Can add threads to assigned tickets | Employee |
| manage_users | Can CRUD users, divisions, and all tickets | Super Admin |
| manage_roles | Can CRUD roles | Super Admin |
| manage_permissions | Can CRUD permissions and sync role permissions | Super Admin |

---

## Architecture Notes

1. **Dual Database:** MySQL stores relational data (users, 
oles, divisions, permissions, 	ickets, permission_role). MongoDB stores 	icket_threads and activity_logs.

2. **Ticket Number Format:** TKT- + zero-padded auto-increment ID (6 digits), e.g., TKT-000001.

3. **File Attachments:** Stored on the public disk under 	ickets/ directory. Each attachment record: { disk, path, original_name, mime_type, size }.

4. **Super Admin Master Routes:** Bypass TicketService business logic. No automatic thread or activity log creation. No status transition validation.

5. **Partial Updates:** All PUT endpoints use sometimes validation rules, meaning only fields present in the request body are validated and updated.
