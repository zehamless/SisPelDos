<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class getListDosenController extends Controller
{
    public function __invoke(Request $request)
    {
        $dosen = $request->query('dosen');
        try {
            return Http::get('https://api-frontend.kemdikbud.go.id/hit/'.$dosen)->json();
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
