<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class downloadFileController extends Controller
{
    public function __invoke($file)
    {
        return response()->download(storage_path('app/public/materi/' . $file));
    }
}
