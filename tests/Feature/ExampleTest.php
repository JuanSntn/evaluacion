<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_root_redirects_to_login_for_a_guest(): void
    {
        $this->get('/')
            ->assertRedirectToRoute('login');
    }
}
