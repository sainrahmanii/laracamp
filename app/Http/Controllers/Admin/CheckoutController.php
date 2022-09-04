<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Checkout\Paid;
use App\Models\Checkouts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function update(Request $request, Checkouts $checkout)
    {
        // return $checkout;

        $checkout->is_paid = true;
        $checkout->save();

        //send email to user
        Mail::to($checkout->User->email)->send(new Paid($checkout));

        $request->session()->flash('success', "Checkout with ID {$checkout->id} has been updated!");

        return redirect(route('admin.dashboard'));
    }
}
