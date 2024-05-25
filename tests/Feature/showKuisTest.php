<?php

namespace Tests\Feature;

use App\Http\Resources\KuisCollection;
use App\Http\Resources\kuisResource;
use App\Models\MateriTugas;
use App\Models\User;
use Spatie\Activitylog\Contracts\Activity;
use Tests\TestCase;

class showKuisTest extends TestCase
{
    public function testBasic()
    {
        $data = MateriTugas::with('kuis')->where('id', 3)->first()->toArray();
        $userAnswer = '{"1":"g","2":["gg","df"]}';
        $arrayAnswer = json_decode($userAnswer, true);
        $corrects = 0;
        $totalQuestion = 0;
        foreach ($arrayAnswer as $key => $value) {
            if (!is_array($value)) {
                $jawaban = $data['kuis'][$key - 1]['jawaban'][0]['data']['jawaban_benar'];
                $jawabanOption = $data['kuis'][$key - 1]['jawaban'][0]['data']['jawaban_option'];

                $realJawaban = $jawabanOption[$jawaban];
                if ($value == $realJawaban) {
                    $corrects++;
                }
                $totalQuestion++;
            }
//            dump($corrects);
            if (is_array($value)) {
                $jawaban = $data['kuis'][$key - 1]['jawaban'][0]['data']['jawaban_benar'];
                $numbers = array_filter($jawaban, function ($value) {
                    return is_numeric($value);
                });
                $realJawaban = array_intersect_key($jawaban, array_flip($numbers));
                $totalQuestion += count($realJawaban);
                function array_flatten($array)
                {
                    $return = array();
                    foreach ($array as $key => $value) {
                        if (is_array($value)) {
                            $return = array_merge($return, array_flatten($value));
                        } else {
                            $return[$key] = $value;
                        }
                    }
                    return $return;
                }

                $arrayAnswer = array_flatten($arrayAnswer);
                $matches = array_intersect($arrayAnswer, $realJawaban);
                $count = count($matches);
                $corrects += $count;
            }
        }
        dump('corrects: ' . $corrects, 'totalQuestion: ' . $totalQuestion);

        self::assertIsArray($data);
//        assertIsArray($jawaban);
//        dd($arrayAnswer[2]);
//        dd($realJawaban);
//        $corrects = 0;
//        function array_flatten($array) {
//            $return = array();
//            foreach ($array as $key => $value) {
//                if (is_array($value)){ $return = array_merge($return, array_flatten($value));}
//                else {$return[$key] = $value;}
//            }
//            return $return;
//        }
//
//        $arrayAnswer = array_flatten($arrayAnswer);
//        $matches = array_intersect($arrayAnswer, $realJawaban);
//        $count = count($matches);

//        dd($count);


    }

    public function testReviewKuis()
    {
        $user = User::admin()->first();
        $this->actingAs($user);
        $response = $this->get(route('kuis.review', 2));
        $response->assertJson([
            'data' => [
                'jawaban' => 'g',
                'correct' => 1,
                'total' => 1,
            ],
        ]);
        $response->assertStatus(200);
    }

    public function testActivitylog()
    {
        $user = User::admin()->with('peserta')->first();
//        $lastActivity = \Spatie\Activitylog\Models\Activity::causedBy($user)->latest()->first();
        dump($user);
    }
}
