# Architecture

## MVC structure (Blade-only)
- Models in `app/Models`
- Controllers in `app/Http/Controllers`
- Views in `resources/views` (Blade templates)
- Domain services in `app/Services` (business logic)
- Jobs/Queues in `app/Jobs`
- Events/Listeners in `app/Events` and `app/Listeners`
- Routes in `routes/web.php` (no API layer)
- Config in `config/*`
- Migrations/Seeders in `database/*`

## Roles & permissions
- Roles: Admin, Manager, Foreman, Worker, Client (read-only)
- Enforce RBAC via policies/guards on all routes and models
- Clients only access shared read-only reports for their projects

## Information Architecture
Top-level navigation
- Dashboard, Projects, Calendar, Timesheet, Approvals, Tools, Staff, Reports, Settings, Billing

Example Blade views tree
```
resources/views/
  auth/login.blade.php
  onboarding/register-step1.blade.php
  onboarding/register-step2.blade.php
  dashboard/index.blade.php
  projects/index.blade.php
  projects/show.blade.php
  projects/log.blade.php
  tasks/_form.blade.php
  timesheet/index.blade.php
  approvals/index.blade.php
  calendar/week.blade.php
  tools/index.blade.php
  tools/show.blade.php
  staff/index.blade.php
  staff/show.blade.php
  reports/hours.blade.php
  settings/company.blade.php
  billing/index.blade.php
```

## Route map (selected)
```
GET  /                → DashboardController@index
GET  /login           → AuthController@showLogin
POST /login           → AuthController@login
GET  /register        → RegistrationController@step1
POST /register        → RegistrationController@storeStep1
GET  /register/2      → RegistrationController@step2
POST /register/2      → RegistrationController@complete
GET  /projects        → ProjectController@index
POST /projects        → ProjectController@store
GET  /projects/{id}   → ProjectController@show
POST /projects/{id}/log → ProjectLogController@store
GET  /calendar        → CalendarController@week
GET  /timesheet       → TimesheetController@index
POST /timesheet/clock-in  → TimesheetController@clockIn
POST /timesheet/clock-out → TimesheetController@clockOut
GET  /approvals       → ApprovalController@index
POST /approvals/{user}/{week}/approve → ApprovalController@approve
POST /approvals/{entry}/reject → ApprovalController@reject
GET  /tools           → ToolController@index
POST /tools/{id}/transfer → ToolTransferController@store
GET  /reports/hours   → ReportController@hours
GET  /reports/hours/export.csv → ReportController@exportCsv
GET  /r/{token}       → SharedLinkController@show
```

## Database (ERD outline)
- companies 1→many users, projects, tools
- projects 1→many tasks, project_logs, time_entries
- users 1→many time_entries, tool_assignments
- tools 1→many tool_assignments, tool_transfers
- time_entries 1→many attachments
- approval_batches 1→many approval_events
- shared_links (tokenized read-only access)

## Non-functional requirements
- Security: CSRF; policies per model; encrypt GPS coords at rest; use S3 presigned URLs
- Performance: eager-load for calendar/reports; cache per-project weekly totals
- Accessibility: keyboard-friendly forms; alt text; high-contrast mode
- Observability: activity/audit log for time/approval changes
- Backups: nightly DB backups + S3 versioning for media
- Analytics: basic counters (active users/day, clock-ins/site)

## Implementation notes (Blade + Filament CSS)
- Tailwind with Filament tokens; no Livewire components; Blade components for cards/forms/tables/badges
- Use navigator.geolocation for clock events; post lat/lng with the form
- Images: accept image/* capture; compress server-side
- Signatures: canvas → PNG stored on S3
- Keep interactivity minimal; prefer PRG
