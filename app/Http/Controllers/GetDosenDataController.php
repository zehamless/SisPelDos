<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GetDosenDataController extends Controller
{
    public function __invoke(Request $request)
    {
        $link = $request->query('link');
        try {
            return Http::get('https://api-frontend.kemdikbud.go.id/detail_dosen/'.$link)->json();
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
