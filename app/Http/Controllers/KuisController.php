<?php

namespace App\Http\Controllers;

use App\Http\Resources\kuisResource;
use App\Models\MateriTugas;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    public function index()
    {

    }

    public function show($kuis)
    {
        $data = MateriTugas::with('kuis')->where('id', $kuis)->first();
        $jsonData = $data->toJson();
        return view('kuis.kuis', compact('jsonData'));
    }

    public function store(Request $request)
    {
        $data = MateriTugas::with('kuis')->where('id', $request->kuis_id)->first()->toArray();
        $arrayAnswer = $request->data;
        $corrects = 0;
        $totalQuestion = 0;
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

        foreach ($arrayAnswer as $key => $value) {
            $jawaban = $data['kuis'][$key - 1]['jawaban'][0]['data']['jawaban_benar'];
            $jawabanOption = $data['kuis'][$key - 1]['jawaban'][0]['data']['jawaban_option'];

            if (!is_array($value)) {
                $realJawaban = $jawabanOption[$jawaban];
                if ($value == $realJawaban) {
                    $corrects++;
                }
                $totalQuestion++;
            } else {
                $numbers = array_filter($jawaban, 'is_numeric');
                $realJawaban = array_intersect_key($jawaban, array_flip($numbers));
                $totalQuestion += count($realJawaban);
                $arrayAnswer = array_flatten($arrayAnswer);
                $corrects += count(array_intersect($arrayAnswer, $realJawaban));
            }
        }

        $arrData = ['jawaban' => $request->data, 'correct' => $corrects, 'total' => $totalQuestion];
        auth()->user()->mengerjakan()->attach($request->kuis_id, ['files' => json_encode($arrData),'penilaian' => $corrects/$totalQuestion*100, 'status' => 'selesai', 'is_kuis' => true]);
        return response()->json(['correct' => $corrects, 'total' => $arrData]);
    }

}
