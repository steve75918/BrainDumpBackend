<?php

namespace App\Http\Controllers;

use App\Events\TT2ReportUploaded;
use App\Http\Requests\TT2Request;
use App\Models\TT2Member;
use Illuminate\Http\Request;

class TT2Controller extends Controller
{
    public function raidTargetCalc(TT2Request $request)
    {
        // process data
        $params = $request->validated('inputs');
        $parts = [];

        // Head
        $head = [
            'name' => 'Head',
            'hp' => $params[0],
            'ap' => $params[1],
            'ratio' => round($params[1] / $params[0], 4),
        ];

        array_push($parts, $head);

        // Torso
        $torso = [
            'name' => 'Torso',
            'hp' => $params[2],
            'ap' => $params[3],
            'ratio' => round($params[3] / $params[2], 4),
        ];

        array_push($parts, $torso);

        // Arms
        $armHp = round(($params[4] / 4), 4);
        $armAp = round(($params[5] / 4), 4);

        $arm = [
            'name' => 'Arm',
            'hp' => $armHp,
            'ap' => $armAp,
            'ratio' => round($armAp / $armHp, 4),
        ];

        for ($i = 0; $i < 4; $i ++) {
            $arm['name'] = sprintf('%s-%d', 'Arm', $i + 1);
            array_push($parts, $arm);
        }

        // Legs
        $legHp = round(($params[6] / 2), 4);
        $legAp = round(($params[7] / 2), 4);

        $leg = [
            'name' => 'Leg',
            'hp' => $legHp,
            'ap' => $legAp,
            'ratio' => round($legAp / $legHp, 4),
        ];

        for ($i = 0; $i < 2; $i ++) {
            $leg['name'] = sprintf('%s-%d', 'Leg', $i + 1);
            array_push($parts, $leg);
        }

        // Gatering all necessary parameters
        $collection = collect($parts);
        $targetValue = $params[8];

        // Select targets by ratio
        $sortedTargets = $collection->sortBy('ratio');

        $totalHp = 0.0;
        $count = 1;
        foreach ($sortedTargets as $target) {
            $totalHp += $target['hp'];

            if ($totalHp >= $targetValue) {
                break;
            }

            $count ++;
        }

        $selectedTargets = $sortedTargets->chunk($count)->first();

        // Exclude certain result from exceed more than a target part
        $exceedValue = $totalHp - $targetValue;

        foreach ($selectedTargets->reverse() as $key => $target) {
            if ($exceedValue >= $target['hp']) {
                $selectedTargets->pull($key);
                $exceedValue -= $target['hp'];
            }
        }

        return response()->json($selectedTargets);
    }

    /**
     * Receive uploaded raid report file
     */
    public function uploadRaidReport(Request $request)
    {
        $file = $request->file('file');

        TT2ReportUploaded::dispatch($file);

        return response()->json(['message' => 'File uploaded successfully!']);
    }

    public function raidAttendance(Request $request)
    {
        $members = TT2Member
            ::with([
                'raidStatistics' => function ($query) {
                    $query->orderBy('created_at', 'DESC')->limit(5);
                },
                'raidStatistics.raid'
            ])
            ->get();

        $output = $members->map(function ($member) {
            return [
                'memberName' => $member->name,
                'memberCode' => $member->member_code,
                'raids'  => $member->raidStatistics->map(function ($stat) {
                    return [
                        'raidBatch'   => $stat->raid->batch_name,
                        'attendance' => $stat->attendance,
                    ];
                }),
            ];
        });

        return response()->json($output);
    }
}
