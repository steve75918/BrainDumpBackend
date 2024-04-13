<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TT2ControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_calc_raid_target(): void
    {
        $inputParams = [
            'inputs' => [1.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0],
        ];

        $response = $this->post('/api/tt2/raid_target', $inputParams);

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'hp',
                    'ap',
                    'ratio',
                ]
            ]);
    }
}
