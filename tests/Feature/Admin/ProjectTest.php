<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function admin(): User
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $roleId = Role::where('name', 'admin')->value('id');
        $admin->roles()->attach($roleId);

        return $admin;
    }

    public function test_index_lists_projects(): void
    {
        $admin = $this->admin();
        Project::factory()->count(2)->create();

        $this->actingAs($admin)
            ->get(route('admin.projects.index'))
            ->assertOk()
            ->assertSee('Projects');
    }

    public function test_create_stores_project(): void
    {
        $admin = $this->admin();

        $company = Company::factory()->create();
        $response = $this->actingAs($admin)->post(route('admin.projects.store'), [
            'company_id' => $company->id,
            'name' => 'Bridge A',
            'code' => 'BR-A',
            'description' => 'Test project',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Bridge A',
            'code' => 'BR-A',
            'is_active' => 1,
        ]);
    }

    public function test_edit_updates_project(): void
    {
        $admin = $this->admin();
        $project = Project::factory()->create(['name' => 'Old']);
        $newCompany = Company::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.projects.update', $project), [
            'company_id' => $newCompany->id,
            'name' => 'New',
            'code' => $project->code,
            'description' => $project->description,
            'is_active' => $project->is_active,
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'New',
        ]);
    }

    public function test_destroy_deletes_when_no_timesheets(): void
    {
        $admin = $this->admin();
        $project = Project::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.projects.destroy', $project));
        $response->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
