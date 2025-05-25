<?php

use Laravel\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\WithoutMiddleware;

class GetEnrichedStreamsControllerTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Carga la aplicación Lumen para las pruebas.
     */
    public function createApplication()
    {
        return require __DIR__ . '/../../../../../bootstrap/app.php';
    }

    /**
     * Configuración antes de cada prueba: deshabilita middleware (incluida auth).
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /**
     * Prueba que al llamar a /analytics/streams/enriched sin parámetro "limit" devuelve error 400.
     */
    public function testMissingLimitParamReturns400()
    {
        $this->json('GET', '/analytics/streams/enriched', [])
             ->seeStatusCode(400)
             ->seeJsonStructure(['error']);
    }

    /**
     * Prueba que al llamar a /analytics/streams/enriched con "limit" no numérico devuelve error 400.
     */
    public function testInvalidLimitParamReturns400()
    {
        $this->json('GET', '/analytics/streams/enriched', ['limit' => 'invalid'])
             ->seeStatusCode(400)
             ->seeJsonStructure(['error']);
    }

    /**
     * Prueba que al llamar a /analytics/streams/enriched con "limit" válido devuelve 200.
     */
    public function testProperLimitParamReturns200()
    {
        $this->json('GET', '/analytics/streams/enriched', ['limit' => 10])
             ->seeStatusCode(200)
             ->seeJsonStructure([
                 'data',
                 'meta' => ['limit', 'total']
             ]);
    }
}
