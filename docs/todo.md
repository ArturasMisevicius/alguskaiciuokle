# TODO

## M1 – Foundation & Onboarding
- [x] Implement Fortify auth (login/reset)
- [x] Install Laravel Boost (dev tooling)
- [ ] create companies, make assign company to user, and user to company, make all features, make all frontend, make all database
- [ ] Configure SQLite DB and local storage
- [ ] Locale switcher and base translations (en, lt)
- [ ] Dashboard skeleton

## M2 – Projects, Tasks, Timesheet
- [x] CRUD projects
- [ ] Task management under projects
- [ ] Timesheet clock in/out capture
- [ ] Photo/comment attachments to entries
- [ ] Staff management (profiles, roles, email invite)

## M3 – Calendar & Approvals
- [ ] Weekly calendar (filters: user/project)
- [ ] Submit weekly hours workflow
- [ ] Approve/reject with comments
- [ ] Batch approve per user/week

## M4 – Tools
- [ ] Tools inventory CRUD
- [ ] Assignments and transfers with history

## M5 – Documentation & Reports
- [ ] Project daily log with photos/comments
- [ ] Hours Excel/CSV/PDF exports

## M6 – Tariff System (Тарифы)
- [x] Create Tariff model with fields: name, start_time, end_time, price_per_hour, is_active
- [x] Create migration for tariffs table
- [x] Create TariffFactory for testing
- [x] Create TariffController (CRUD operations) in Admin namespace
- [x] Create Blade views for tariffs (index, create, edit, show)
- [x] Add tariff routes to web.php (admin. tariffs.*)
- [x] Add "Tariff" navigation link to admin menu on top
- [x] Update PricingEngineService to use Tariffs for salary calculation based on time bands
- [x] Add validation rules for tariffs (overlapping time ranges, etc.)
- [x] Create tariff seeder with sample data
- [x] Write tests for Tariff model and controller
- [x] Update timesheet calculation to split hours by tariff time bands and calculate salary accordingly

