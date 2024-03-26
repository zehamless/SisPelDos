<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query')?? '';

        $pelatihan = Pelatihan::when($query, function ($q) use ($query) {
            return $q->where('judul', 'like', '%' . $query . '%');
        })->paginate(8);
        return view('dashboard', compact('pelatihan'));
    }
}
