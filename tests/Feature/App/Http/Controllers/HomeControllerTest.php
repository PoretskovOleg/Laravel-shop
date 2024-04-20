<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\HomeController;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function test_home_page(): void
    {
        $response = $this->get(action(HomeController::class));

        $response->assertOk();
    }
}
