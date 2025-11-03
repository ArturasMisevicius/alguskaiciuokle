# Projects (Admin)

This page describes managing Projects in the Admin panel.

- Views: `resources/views/admin/projects/{index,create,edit}.blade.php`
- Controller: `App\\Http\\Controllers\\Admin\\ProjectController`
- Routes: Resource routes under `admin.projects.*` (see `routes/web.php`)

## Capabilities
- List projects with pagination and counts of related timesheets
- Assign each project to a company (required)
- Create a project (name, optional code, optional description, active flag)
- Edit and update an existing project
- Delete a project (blocked when related timesheets exist)

## Navigation
The admin navbar links to Projects: `route('admin.projects.index')`.

## Notes
- Validation occurs in controller actions
- Success/error flashes are rendered in views
- Access restricted via `auth` and `role:admin` middleware

