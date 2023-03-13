<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class ExampleTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testUsersReturnsAllUsersByDefault()
    {
        Artisan::call('migrate');

        // Crear algunos usuarios de prueba
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Hacer una solicitud de prueba a la ruta users
        $response = $this->get('/api/users');

        // Asegurarse de que la respuesta sea un código 200 (OK)
        $response->assertStatus(200);

        // Asegurarse de que la respuesta contiene los tres usuarios creados
        $response->assertJsonCount(3);

        // Asegurarse de que los atributos de los usuarios son correctos
        $response->assertJsonFragment([
            'id'=> $user1->id,
            'name' => $user1->name,
            'email' => $user1->email
        ]);
        $response->assertJsonFragment([
            'id'=> $user2->id,
            'name' => $user2->name,
            'email' => $user2->email
        ]);
        $response->assertJsonFragment([
            'id'=> $user3->id,
            'name' => $user3->name,
            'email' => $user3->email  
        ]);
    }
  
    /** @test */
    public function it_returns_only_active_users_when_active_flag_is_provided()
    {
        Artisan::call('migrate');

        $activeUser = User::factory()->create(['active' => true]);
        $inactiveUser = User::factory()->create(['active' => false]);
        $response = $this->get('/api/users?active=1');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'id' => $activeUser->id
        ]);
        $response->assertJsonMissing([
            'id' => $inactiveUser->id
        ]);
    }

    public function test_Login()
    {
        Artisan::call('migrate');
        // Crear un usuario para la prueba
         $password = $this->faker->password;
        $user = User::factory()->create([
            'email' => $this->faker->email,
            'password' => Hash::make($password),
            //'password' => bcrypt($password),
        ]);

        // Envía una solicitud de inicio de sesión con credenciales válidas
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // Verifica que la respuesta tenga un estado exitoso
        $response->assertStatus(200);

         // Verifica que la respuesta contenga un token de acceso
         $response->assertJsonStructure([
            'status',
            'msg',
        ]);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue(!empty($responseData['msg']));

        // Verifica que el token de acceso sea válido
        $token = $responseData['msg'];
        $userFromToken = $user->tokens()->where('name', 'example')->first()->token;

        $this->assertEquals($token, $userFromToken);
}

public function testInvalidLogin()
{
    Artisan::call('migrate');
    // Envía una solicitud de inicio de sesión con credenciales inválidas
    $response = $this->postJson('/api/login', [
        'email' => 'invalid@example.com',
        'password' => 'invalidpassword',
    ]);

    // Verifica que la respuesta tenga un estado de error
    $response->assertStatus(200);

    // Verifica que la respuesta contenga un mensaje de error
    $response->assertJsonStructure([
        'status',
        'msg',
    ]);
}
}
