<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkouts;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function update(Request $request, Checkouts $checkouts)
    {
        return $checkouts;
        // $checkouts->is_paid = true;
        // $checkouts->save();
        // $request->session()->flash('success', "Checkout with ID {$checkouts->id} has been updated!");
        // return redirect(route('admin.dashboard'));
    }
}
