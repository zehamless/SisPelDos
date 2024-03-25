<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;

class UserDashboardController extends Controller
{
    public function index()
    {
       $pelatihan =  Pelatihan::paginate(5);
        return view('dashboard', compact('pelatihan'));
    }
}
