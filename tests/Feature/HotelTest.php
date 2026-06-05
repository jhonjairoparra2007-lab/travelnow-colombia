<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class HotelTest extends TestCase
{
    /**
     * Verificar que la página de inicio cargue correctamente y muestre los hoteles.
     */
    public function test_index_page_loads_and_shows_hotels(): void
    {
        // Simulamos la respuesta de la API GET
        Http::fake([
            'jsonplaceholder.typicode.com/posts' => Http::response([
                ['id' => 1, 'title' => 'Post 1', 'body' => 'description 1'],
                ['id' => 2, 'title' => 'Post 2', 'body' => 'description 2'],
            ], 200)
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('TravelNow Colombia S.A.S.');
        $response->assertSee('Catálogo de Hoteles Disponibles');
        $response->assertSee('Hotel Cartagena Caribe');
        $response->assertSee('Hotel Medellín Plaza');
        $response->assertSee('Creado por Jhon Jairo Parra Obando del ADSO 35 SENA');
        $response->assertSee('ADSO 35 SENA - Jhon Jairo Parra Obando');
    }

    /**
     * Verificar el envío exitoso de una reserva mediante el método POST.
     */
    public function test_submitting_reservation_successfully(): void
    {
        // Simulamos la respuesta de la API POST
        Http::fake([
            'jsonplaceholder.typicode.com/posts' => Http::response([
                'id' => 101,
                'title' => 'Reserva Hotel Cartagena',
                'body' => 'Reserva para 2 personas'
            ], 201)
        ]);

        $response = $this->post('/reservar', [
            'title' => 'Reserva Hotel Cartagena',
            'body' => 'Reserva para 2 personas',
        ]);

        $response->assertStatus(302); // Redirección de vuelta (back())
        $response->assertSessionHas('success');
        
        $success = session('success');
        $this->assertEquals('¡Reserva registrada con éxito mediante POST!', $success);
        $this->assertEquals('Reserva Hotel Cartagena', session('sent_title'));
        $this->assertEquals('Reserva para 2 personas', session('sent_body'));
        
        $response_json = session('response_json');
        $this->assertArrayHasKey('id', $response_json);
        $this->assertEquals(101, $response_json['id']);
    }

    /**
     * Verificar que el formulario valide los campos vacíos obligatorios.
     */
    public function test_submitting_invalid_reservation_fails_validation(): void
    {
        $response = $this->post('/reservar', [
            'title' => '', // Título vacío (inválido)
            'body' => '',  // Cuerpo vacío (inválido)
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title', 'body']);
    }
}
