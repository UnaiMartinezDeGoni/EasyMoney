<?php

use tests\TestCase;
class GetUserByIdControllerTest extends TestCase
{
    /**
     * @test
     */
    public function getsErrorIfIdParameterIsMissing(): void
    {
        $response = $this->call(
            method: 'GET',
            uri: 'analytics/user',
            parameters: [],
            cookies: [],
            files: [],
            server: ['HTTP_AUTHORIZATION' => 'Bearer 06e5ea3b26fc05aa']
        );

        $response->assertStatus(status: 400);
        $response->assertJson([
            'error' => 'Missing id parameter.'
        ]);
    }
}
