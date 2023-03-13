<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    
    public function test_user_route_returns_authenticated_user()
    {
        // Crear un usuario y autenticarlo con Sanctum
        $user = User::factory()->create();

        // Autenticar al usuario con Sanctum
        Sanctum::actingAs($user); //recibe un modelo de usuario y crea un token de acceso para ese usuario

        // Hacer una solicitud GET a la ruta /user
        $response = $this->get('/api/user');

        // Verificar que la respuesta sea un objeto JSON que contiene los detalles del usuario autenticado
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $response->assertJsonFragment(
            [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
            ]
        );

        $response->assertJsonStructure([
            'id',
            'name',
            'email'
        ]);
        
    }
}
