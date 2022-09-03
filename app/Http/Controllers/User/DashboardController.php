<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Checkouts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $checkouts = Checkouts::with('Camp')->whereUserId(Auth::id())->get();
        return view('layouts.invoice', [
            'checkouts' =>$checkouts
        ]);
    }
}
