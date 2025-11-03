# Product Backlog (Blade + Filament CSS)

This document captures user stories, acceptance criteria, Blade screens/routes, tables, and implementation notes to recreate worker.nu parity without a SPA.

## 0) Tech stack constraints
- Laravel 12+, Blade views only; Tailwind with Filament design tokens (CSS utilities only)
- DB: SQLite  for notifications/reports
- Auth: Fortify (password) + optional magic-link login
- I18n: en, lt

## 1) Roles & permissions
Roles: Admin,Worker
- RBAC with policies on all routes
- Clients only see shared read-only reports for their projects

## 2) Account onboarding & 14-day trial
User stories
Acceptance
- Required fields + password strength
Blades & routes
- GET/POST /register (multi-step), GET /login, forgot/reset
Tables
- companies (name, phone_country, phone, size_band, language, trial_ends_at)
- users
Notes: country code picker, language selection

## 3) Projects & tasks
User stories
- Manager creates project (address, client, budget, dates)
- Foreman defines daily tasks and assigns workers
Acceptance
- Status: planned/active/paused/closed; visibility scoped to assigned
Blades & routes
- projects.index/create/show/edit; tasks nested
Tables
- projects (client_id, name, code, address, start_at, end_at, status, budgets...)
- tasks (project_id, title, notes, scheduled_date)
Notes: point-in-polygon check server-side

## 4) Time tracking (clock-in/out)
User stories
- Worker clocks in/out on a project; capture GPS + photo/comment
- Foreman bulk-logs hours for a crew
- Manager sees summaries and anomalies
Acceptance
- Project required
- Photos/comments allowed; out-of-geofence flagged; off-site clock-out alerts
Blades & routes
- GET /timesheet; POST /timesheet/clock-in; POST /timesheet/clock-out; POST /timesheet/entry/{id}/attachments
Tables
- time_entries (user_id, project_id, task_id, started_at, stopped_at, duration_minutes, start_geo, stop_geo, notes, status)
- attachments (time_entry_id, type, s3_path)

## 5) Calendar (weekly)
- GET /calendar?view=week&user_id&project_id
- Week grid with totals; filters by user/project
- Uses time_entries and tasks

## 6) Hours submission & approval workflow
User stories
- Worker submits week; Manager approves/rejects with comment
Acceptance
- Status: draft → submitted → approved/rejected; history log
- Batch actions per person/week; export approved only
Routes
- GET /approvals; POST /approvals/{user}/{week}/approve; POST /approvals/{entry}/reject
Tables
- approval_batches (user_id, week_start, status)
- approval_events (batch_id, actor_id, action, note)


## 8) Tool / asset management
User stories
- Assign tools; record transfers; see holder history
Acceptance
- Tool: category, serial/QR, value, service dates, current holder
- Transfers with from→to, timestamp, optional photo
Tables
- tools; tool_assignments; tool_transfers

## 9) Project documentation & quality control
User stories
- Daily photos/comments; digital signatures; simple costs
Routes
- projects/{id}/log (GET/POST); projects/{id}/report/{date} (PDF)
Tables
- project_logs; log_attachments; signatures; project_costs

## 10) Reporting & exports
User stories
- Export weekly/monthly hours to CSV/PDF; client read-only share link
Routes
- GET /reports/hours; GET /reports/hours/export.csv; GET /r/{token}
Tables
- shared_links (token, resource_type, resource_id, expires_at, allowed_ips?)

## 11) Staff management
User stories
- Admin adds workers, roles, pay rates, documents
- Foreman invites worker via SMS/email to complete profile
Tables
- user_profiles; user_docs; invitations

## 13) Billing & plans
- Stripe checkout; seat-based pricing; proration
Tables: subscriptions (provider, status, quantity, current_period_end)

## 14) Legal & compliance (EU)
- Retention rules; data export; delete on termination

## 15) Settings & localization
- Working hours, overtime rules, languages; translations for UI/PDFs

## Information Architecture
Top-level nav: Dashboard, Projects, Calendar, Timesheet, Approvals, Tools, Staff, Reports, Settings, Billing.
Example Blade tree: see Architecture doc.

## Non-functional
- Security: CSRF; policies; encrypt GPS at rest; S3 presigned URLs
- Performance: eager-load; cache weekly totals
- A11y: keyboard support; alt text; high-contrast mode
- Observability: audit log for time/approvals
- Backups: nightly DB;
- Analytics: basic counters

## Milestones
M1: Foundation & Onboarding
M2: Projects, Tasks, Timesheet
M3: Calendar & Approvals
M4: Tools
M5: Docs & Reports
M6: Notifications & Billing

## Tracker-ready one-liners
- Weekly calendar for team; submit/approve hours with comments
- Daily project log with photos/comments; digital signatures; CSV/PDF exports with client share links

## Implementation notes (Blade + Filament CSS)
- Tailwind with Filament tokens; Blade components for cards/forms/tables/badges
- Images: accept image/* capture; server-side compression
- Keep interactivity minimal; PRG pattern

## Assumptions
- Mobile-first web app; transactional tool tracking; Stripe billing
