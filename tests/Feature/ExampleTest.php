<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */


     public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_register(): void{
        Artisan::call('migrate');

        $carga=$this->get(route('registro'));
        $carga->assertStatus(200)->assertSee("register");

        //Registro corecto

        $registerBien=$this->post(route('validar-registro',["name"=>"test","email" => "aaaa7@gmail.com","password"=>"123"]));

        $registerBien->assertStatus(302)->assertRedirect(route('privada'));

        $this->assertDatabaseHas('users',["name"=>"test","email" => "aaaa@gmail.com"]);

    }

    public function test_login(): void{
        //Artisan::call('migrate');

        $carga=$this->get(route('login'));
        $carga->assertStatus(200)->assertSee("login");

        //Registro corecto

        $loginBien=$this->post(route('inicia-session',
        ["name"=>"Andre Wisoky","email" => "moore.kamryn@example.com","password"=>"password"]));

        $loginBien->assertStatus(302)->assertRedirect(route('privada'));

        $carga=$this->get(route('logout'));
        $carga->assertStatus(302)->assertRedirect(route('login'));
    }

}
