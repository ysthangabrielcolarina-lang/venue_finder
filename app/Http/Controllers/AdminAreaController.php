<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venue;

class AdminAreaController extends Controller
{
    public function __invoke()
    {
        return view('admin.index', [
            'users' => User::orderBy('role')->orderBy('name')->get(),
            'venues' => Venue::with(['barangay', 'owners'])->orderBy('name')->get(),
        ]);
    }
}
