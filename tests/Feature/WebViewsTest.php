<?php

namespace Tests\Feature;

use App\Models\UserAccount;
use Tests\TestCase;

class WebViewsTest extends TestCase
{
    private function actingAsAdmin(): UserAccount
    {
        $admin = UserAccount::where('username', 'admin')->firstOrFail();
        $this->actingAs($admin);

        return $admin;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_dashboard_page_loads_with_database_data(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Welcome To Dashboard TPS Key Person Development Web');
        $response->assertSee('Total Agents');
        $response->assertSee('Kaizen Projects');
        $response->assertSee('Under Review');
        $response->assertSee('Success Rate');
    }

    public function test_activity_monitoring_page_loads_with_database_data(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/activity');

        $response->assertStatus(200);
        $response->assertSee('Activity Monitoring Log');
        $response->assertSee('Real-time tracking of Kaizen');
        $response->assertSee('All Departments');
        $response->assertSee('Project ID');
        $response->assertSee('Agent / Key Person');
    }

    public function test_activity_search_and_filter(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/activity?search=Kaizen');

        $response->assertStatus(200);
    }

    public function test_login_screen_renders(): void
    {
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Masuk');
    }
}
