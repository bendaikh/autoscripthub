<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $this->markTestSkipped('The homepage smoke test needs the marketplace database.');
        }

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
