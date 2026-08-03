<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    public function test_a_404_shows_the_branded_error_page(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/una-ruta-que-no-existe-de-verdad');

        $response->assertStatus(404);
        $response->assertSee('Página no encontrada');
        $response->assertSee('Cateura Accesorios', false);
    }

    public function test_the_403_view_renders_with_the_expected_content(): void
    {
        $response = $this->view('errors.403');

        $response->assertSee('Acceso no autorizado');
    }

    public function test_the_500_view_renders_with_the_expected_content(): void
    {
        $response = $this->view('errors.500');

        $response->assertSee('Algo salió mal');
    }
}
