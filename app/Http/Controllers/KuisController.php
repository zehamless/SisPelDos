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
        try {
            $model = MateriTugas::with('kuis')->where('id', $request->kuis_id)->first();
            if (!$model) {
                throw new \Exception("Quiz not found", 404);
            }

            $data = $model->toArray();
            $arrayAnswer = $request->data;
            $corrects = 0;
            $totalQuestion = 0;

            foreach ($arrayAnswer as $key => $value) {
                if (is_int($key)) {
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
                        $corrects += count(array_intersect($value, $realJawaban));
                    }
                }
            }

            $arrData = ['pertanyaan' => $request->pertanyaan, 'jawaban' => $request->data, 'correct' => $corrects, 'total' => $totalQuestion];
            $status = 'belum';
            if ($data['tgl_selesai'] > now()) {
                $status = $data['tgl_tenggat'] > now() ? 'selesai' : 'telat';
            }

            $penilaian = 0;
            if (!empty($arrayAnswer) && is_array($arrayAnswer)) {
            $penilaian = number_format($corrects / $totalQuestion * 100, 2);
            }

            $userKuis = auth()->user()->kuis();
            if ($userKuis->where('materi_tugas_id', $request->kuis_id)->where('status', 'selesai')->count() < $data['max_attempt']) {
                $userKuis->syncWithPivotValues($request->kuis_id, [
                    'files' => json_encode($arrData),
                    'penilaian' => $penilaian,
                    'status' => $status,
                    'is_kuis' => true
                ]);
                activity('mengerjakan')
                    ->performedOn($model)
                    ->event('kuis')
                    ->log('mengerjakan kuis ' . $data['judul']);
                return response()->json(['correct' => $corrects, 'total' => $arrData, 'data' => $data['kuis']]);
            }
            return response()->json(['error' => 'You have reached the maximum attempt'], 400);
        } catch (\Exception $e) {
            // Log the error or handle it as per your requirement
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function review($kuis)
    {
        $jawaban = auth()->user()->kuis()->wherePivot('id', $kuis)->first();
        $jsonData = MateriTugas::with('kuis')->where('id', $jawaban->pivot->materi_tugas_id)->first()->toJson();
        $jsonJawaban = $jawaban->toJson();
//        return response()->json($data);
        return view('kuis.reviewKuis', compact('jsonJawaban'));
    }

    public function adminPreview($kuis)
    {
        $data = MateriTugas::with('kuis')->where('id', $kuis)->first();
        $jsonData = $data->toJson();
        return view('kuis.adminPreviewKuis', compact('jsonData'));
    }

}
