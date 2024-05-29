<?php

namespace Tests\Feature\Http\Controllers;

use App\Events\TT2ReportUploaded;
use App\Listeners\AnalyzeTT2Report;
use App\Models\TT2Member;
use App\Models\TT2Raid;
use Database\Seeders\TT2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TT2ControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    #[Test]
    public function calcRaidTarget(): void
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
    #[Test]
    public function missingRequiredParameters()
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
    #[Test]
    public function invalidParameterType()
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
    #[Test]
    public function invalidParameterRange()
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

    /**
     * Upload file
     *
     * @return void
     */
    #[Test]
    public function uploadRaidReport()
    {
        Event::fake();

        $file = UploadedFile::fake()->create('test_tt2_raid_report_20240509.csv', 100);

        $response = $this->post('/api/tt2/upload', ['file' => $file]);

        $response
            ->assertOk()
            ->assertJsonFragment(['message' => 'File uploaded successfully!']);

        Event::assertDispatched(TT2ReportUploaded::class, function ($event) use ($file) {
            return $event->file->hashName() === $file->hashName();
        });
    }

    /**
     * Read csv
     *
     * @param string $csvContent
     * @return array
     */
    private function readCsvData($csvContent)
    {
        $rows = [];
        $lines = explode(PHP_EOL, $csvContent);
        $header = str_getcsv(array_shift($lines));

        foreach ($lines as $line) {
            if (trim($line)) {
                $rows[] = array_combine($header, str_getcsv($line));
            }
        }

        return $rows;
    }

    /**
     * Handle uploaded file
     *
     * @return void
     */
    #[Test]
    public function handleUploadedFile()
    {
        // Prepare
        // fake data for test of handling
        $filePath     = base_path('/tests/Data/test_tt2_raid_report_20240509.csv');
        $testData     = file_get_contents($filePath);
        $uploadedFile = UploadedFile::fake()->create('RaidReport_20240509.csv', $testData);

        Storage::fake();

        // Action
        $event    = new TT2ReportUploaded($uploadedFile);
        $listener = new AnalyzeTT2Report();
        $listener->handle($event);

        // Expections
        // Check file save into storage
        Storage::disk('tt2Raid')->assertExists($event->file->hashName());

        $rows = array_map('str_getcsv', preg_split('/\n/', $testData, -1, PREG_SPLIT_NO_EMPTY));
        $header = array_shift($rows);

        // first process
        $processedData = [];
        foreach ($rows as $row) {
            $data = array_slice($row, 0, 3);
            $key = sprintf("%s-%s-%s", $data[0], $data[1], $data[2]);

            if (isset($processedData[$key])) {
                $processedData[$key] ++;
            } else {
                $processedData[$key] = 0;
            }
        }

        // second process
        $raidBatchName = $uploadedFile->hashName();
        foreach ($processedData as $key => $value) {
            list($memberName, $memberCode, $raidAttackTimes) = preg_split('/-/', $key);

            // Check TT2 member exist
            $this->assertDatabaseHas(
                'tt2_members',
                ['member_code' => $memberCode]
            );

            // Check TT2 raid exist
            $this->assertDatabaseHas(
                'tt2_raids',
                ['batch_name' => $raidBatchName]
            );

            // Check TT2 raid stastistic
            $member = TT2Member::where('member_code', $memberCode)->first();
            $raid   = TT2Raid::where('batch_name', $raidBatchName)->first();

            $this->assertDatabaseHas(
                'tt2_raid_statistics',
                [
                    'member_id'  => $member->id,
                    'raid_id'    => $raid->id,
                    'attendance' => boolval($raidAttackTimes),
                ]
            );
        }
    }

    /**
     * Response of raid attendancy
     *
     * @return void
     */
    #[Test]
    public function raidAttendance()
    {
        // Prepare
        $this->seed(TT2Seeder::class);

        // Action
        $response = $this->postJson('/api/tt2/raid_attendance');

        // Expections
        $response
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'memberName',
                    'memberCode',
                    'raids' => [
                        '*' => [
                            'raidBatch',
                            'attendance',
                        ]
                    ]
                ]
            ]);
    }
}
