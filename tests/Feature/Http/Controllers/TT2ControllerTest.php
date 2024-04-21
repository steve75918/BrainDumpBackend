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

        $response = $this->postJson('/api/tt2/raid_target', $inputParams);

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

    /**
     * Don't have required parameters
     *
     * @return void
     */
    public function test_missing_required_parameters()
    {
        $inputParams = [
            'inputs' => [],
        ];

        $response = $this->postJson('/api/tt2/raid_target', $inputParams);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'inputs'
            ]);
    }

    /**
     * Input type error
     *
     * @return void
     */
    public function test_invalid_parameter_type()
    {
        $inputParams = [
            'inputs' => ['abc', 'def', 'ghi', 'jkl', 'mno', 'pqr', 'stu', 'vwx', 'yz'],
        ];

        $response = $this->postJson('/api/tt2/raid_target', $inputParams);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'inputs.0',
                'inputs.1',
                'inputs.2',
                'inputs.3',
                'inputs.4',
                'inputs.5',
                'inputs.6',
                'inputs.7',
                'inputs.8',
            ])
            ->assertJsonFragment([
                'inputs.0' => ['The inputs.0 field must be a number.'],
                'inputs.1' => ['The inputs.1 field must be a number.'],
                'inputs.2' => ['The inputs.2 field must be a number.'],
                'inputs.3' => ['The inputs.3 field must be a number.'],
                'inputs.4' => ['The inputs.4 field must be a number.'],
                'inputs.5' => ['The inputs.5 field must be a number.'],
                'inputs.6' => ['The inputs.6 field must be a number.'],
                'inputs.7' => ['The inputs.7 field must be a number.'],
                'inputs.8' => ['The inputs.8 field must be a number.'],
            ]);
    }

    /**
     * Input out of range
     *
     * @return void
     */
    public function test_invalid_parameter_range()
    {
        $inputParams = [
            'inputs' => [1000.0, -10.0, 999.9999999, 0.00000001, 1.0, 2.0, 3.2, 4.0, 120.90],
        ];

        $response = $this->postJson('/api/tt2/raid_target', $inputParams);

        $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'inputs.0',
            'inputs.1',
            'inputs.2',
            'inputs.3',
        ])
        ->assertJsonFragment([
            'inputs.0' => ['The inputs.0 field must be between 0.1 and 999.9.'],
            'inputs.1' => ['The inputs.1 field must be between 0.1 and 999.9.'],
            'inputs.2' => ['The inputs.2 field must be between 0.1 and 999.9.'],
            'inputs.3' => ['The inputs.3 field must be between 0.1 and 999.9.'],
        ]);
    }
}
