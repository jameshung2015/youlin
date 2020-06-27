<?php

namespace Tests\Feature;

use Carbon\Carbon;
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

       dd( Carbon::parse('2020-05-15')->addDay(100));
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
