<?php

namespace App\Http\Controllers;

use App\Http\Requests\TT2Request;

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
}
