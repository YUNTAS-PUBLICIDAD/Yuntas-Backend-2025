<?php
 
namespace Tests\Feature;
 
use App\Models\TrackingPage;
use App\Models\TrackingPageView;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
 
class TrackingTest extends TestCase
{
    use RefreshDatabase;
 
    private TrackingPage $monitoredPage;
 
    protected function setUp(): void
    {
        parent::setUp();
 
        // Crear página monitoreada de prueba
        $this->monitoredPage = TrackingPage::create([
            'route' => '/productos',
            'name' => 'Productos',
        ]);
    }
 
    /**
     * Test: Un usuario anónimo puede registrar una vista de página monitoreada.
     */
    public function test_public_endpoint_registers_page_view(): void
    {
        $payload = [
            'route' => '/productos',
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
        ];
 
        $response = $this->postJson('/api/page-view', $payload);
 
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Vista de página registrada exitosamente.',
            ]);
 
        $this->assertDatabaseHas('tracking_page_views', [
            'tracking_page_id' => $this->monitoredPage->id,
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
        ]);
    }
 
    /**
     * Test: El endpoint ignora páginas que no están monitoreadas.
     */
    public function test_public_endpoint_ignores_non_monitored_pages(): void
    {
        $payload = [
            'route' => '/ruta-cualquiera',
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
        ];
 
        $response = $this->postJson('/api/page-view', $payload);
 
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Página no monitoreada para tracking.',
            ]);
 
        $this->assertDatabaseEmpty('tracking_page_views');
    }
 
    /**
     * Test: El endpoint ignora las visitas de administradores autenticados.
     */
    public function test_public_endpoint_ignores_authenticated_users(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);
 
        $payload = [
            'route' => '/productos',
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
        ];
 
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/page-view', $payload);
 
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Visita de administrador ignorada (no se registra).',
            ]);
 
        $this->assertDatabaseEmpty('tracking_page_views');
    }
 
    /**
     * Test: El endpoint normaliza las rutas largas a su path.
     */
    public function test_public_endpoint_normalizes_route(): void
    {
        $payload = [
            'route' => 'http://yuntaspublicidad.com/productos?param=1',
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
        ];
 
        $response = $this->postJson('/api/page-view', $payload);
 
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Vista de página registrada exitosamente.',
            ]);
 
        $this->assertDatabaseHas('tracking_page_views', [
            'tracking_page_id' => $this->monitoredPage->id,
        ]);
    }
 
    /**
     * Test: Cooldown de 15 segundos evita registros duplicados de la misma sesión en la misma página.
     */
    public function test_cooldown_prevents_duplicate_inserts(): void
    {
        $payload = [
            'route' => '/productos',
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
        ];
 
        // Primer envío (exitoso)
        $this->postJson('/api/page-view', $payload)->assertStatus(201);
 
        // Segundo envío inmediato (debe ignorarse por cooldown)
        $response = $this->postJson('/api/page-view', $payload);
 
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Vista omitida por cooldown de 15 segundos.',
            ]);
 
        $this->assertEquals(1, TrackingPageView::count());
    }
 
    /**
     * Test: Administrador puede consultar las páginas más vistas.
     */
    public function test_admin_can_retrieve_most_viewed_pages(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);
 
        // Registrar una visita
        TrackingPageView::create([
            'tracking_page_id' => $this->monitoredPage->id,
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
            'viewed_at' => now(),
        ]);
 
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/dashboard/most-viewed-pages');
 
        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Productos',
                'total_views' => 1,
            ]);
    }
 
    /**
     * Test: Administrador puede consultar estadísticas por tipo de usuario.
     */
    public function test_admin_can_retrieve_user_type_stats(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);
 
        // Registrar visita anónima
        TrackingPageView::create([
            'tracking_page_id' => $this->monitoredPage->id,
            'session_id' => '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
            'viewed_at' => now(),
        ]);
 
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/dashboard/user-type-stats');
 
        $response->assertStatus(200)
            ->assertJsonFragment([
                'tipo_usuario' => 'Anonimo',
                'total_views' => 1,
            ]);
    }
 
    /**
     * Test: Usuarios no autenticados no pueden acceder a las estadísticas.
     */
    public function test_unauthenticated_user_cannot_access_stats(): void
    {
        $this->getJson('/api/dashboard/most-viewed-pages')
            ->assertStatus(401);
 
        $this->getJson('/api/dashboard/user-type-stats')
            ->assertStatus(401);
    }
}
