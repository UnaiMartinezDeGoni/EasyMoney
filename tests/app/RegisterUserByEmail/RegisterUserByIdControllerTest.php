<?php

namespace Tests\app\RegisterUserByEmail;

use Tests\TestCase;

class RegisterUserByIdControllerTest extends TestCase
{
    /**
     * @test
     */
    public function gets400WhenEmailIsMissing(): void
    {
        $response = $this->call(
            'POST',
            '/register'
        );

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'The email is mandatory',
        ]);
    }


}
