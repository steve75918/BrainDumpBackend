<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TT2Controller extends Controller
{
    public function raidTargetCalc(Request $request)
    {
        $params = $request->post('inputs');

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
            array_push($parts, $leg);
        }

        // Gatering all necessary parameters
        $collection = collect($parts);
        $targetValue = $params[8];

        // $msg = sprintf("<div>Calculating...</div>");
        // echo $msg;

        // Calculate selected targets until exceed target
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

        // $msg = sprintf("<div>Done! Got %d targets.</div>", $selectedTargets->count());
        // echo $msg;

        // Exclude certain result from exceed more than a target part
        $exceedValue = $totalHp - $targetValue;

        foreach ($selectedTargets->reverse() as $key => $target) {
            if ($exceedValue >= $target['hp']) {
                // $removed = $selectedTargets->get($key);
                // $msg = sprintf('<div>Removing: %s, with %.4f hp</div>', $removed['name'], $removed['hp']);
                // echo $msg;

                $selectedTargets->pull($key);
                $exceedValue -= $target['hp'];
            }
        }

        // echo '<hr />';
        // foreach ($selectedTargets as $finalTarget) {
        //     printf("<div>Name: %s</div>", $finalTarget['name']);
        //     printf("<div>Hp: %.4f</div>", $finalTarget['hp']);
        //     printf("<div>Ap: %.4f</div>", $finalTarget['ap']);
        //     printf("<div>Ratio: %.4f</div>", $finalTarget['ratio']);
        //     echo '<br />';
        // }

        // $actulValue = $selectedTargets->sum('hp');
        // printf(
        //     'Target: %.4f, Total: %.4f, Exceed: %.4f',
        //     $targetValue,
        //     $actulValue,
        //     $actulValue - $targetValue
        // );

        return response()->json($selectedTargets);
    }
}
