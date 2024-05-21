<?php

namespace App\Listeners;

use App\Events\TT2ReportUploaded;
use App\Models\TT2Member;
use App\Models\TT2Raid;
use App\Models\TT2RaidStatistic;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnalyzeTT2Report
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TT2ReportUploaded $event): void
    {
        // Save file
        $event->file->store('', 'tt2Raid');

        // Process and save data to database
        $fileContents = $event->file->getContent();

        $rows = array_map('str_getcsv', preg_split('/\n/', $fileContents, -1, PREG_SPLIT_NO_EMPTY));
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
        foreach ($processedData as $key => $value) {
            list($memberName, $memberCode, $raidAttackTimes) = preg_split('/-/', $key);

            $member = TT2Member::firstOrCreate([
                'name'        => $memberName,
                'member_code' => $memberCode,
            ]);

            $raid = TT2Raid::firstOrCreate([
                'batch_name' => $event->file->hashName(),
            ]);

            TT2RaidStatistic::firstOrCreate([
                'member_id'  => $member->id,
                'raid_id'    => $raid->id,
                'attendance' => boolval($raidAttackTimes),
            ]);
        }
    }
}
