<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Checkouts\Store;
use App\Mail\Checkout\AfterCheckout;
use App\Models\Camp;
use App\Models\Checkouts;
use App\Models\Discount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Midtrans;

class CheckoutController extends Controller
{

    public function __construct()
    {
        Midtrans\Config::$serverKey = env('MIDTRANS_SERVERKEY');
        Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
        Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Camp $camp, Request $request)
    {        
        if ($camp->isRegistered) {
            $request->session()->flash('error', "You already registered on {$camp->title} camp.");
            return redirect(route('user.dashboard'));
        }

        return view('layouts.checkouts', [
            'camp'      => $camp
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request, Camp $camp)
    {
        // mapping request data
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['camp_id'] = $camp->id;

        // update data user
        $user = Auth::user();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->occupation = $data['occupation'];
        $user->phone = $data['phone'];
        $user->address = $data['address'];
        $user->save();

        //checkout discount
        if ($request->discount) {
            $discount = Discount::whereCode($request->discount)->first();
            $data['discount_id'] = $discount->id;
            $data['discount_percentage'] = $discount->percentage;
        }

        // create checkout

        $checkout = Checkouts::create($data);
        $this->getSnapRedirect($checkout);

        //sending email
        Mail::to(Auth::user()->email)->send(new AfterCheckout($checkout));

        return  redirect(route('success_checkouts'));

        // return redirect(route('checkout_success', compact('checkout')));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function show(Checkouts $checkout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function edit(Checkouts $checkout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Checkouts $checkout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function destroy(Checkouts $checkout)
    {
        //
    }

    public function success()
    {
        return view('layouts.success_checkouts');
    }

    public function dashboard()
    {
        switch (Auth::user()->is_admin) {
            case true:
                return redirect(route('admin.dashboard'));
                break;
            
            default:
            return redirect(route('user.dashboard'));
                break;
        }
    }

    //Midtrans Handler

    public function getSnapRedirect(Checkouts $checkout)
    {
        $price = $checkout->Camp->price * 1000;
        $orderId = $checkout->id.'-'.Str::random(5);

        $checkout->midtrans_booking_code = $orderId;

        $item_details[] = [
            'id'    => $orderId,
            'price' => $price,
            'quantity'  => 1,
            'name'  => "Payment for {$checkout->Camp->title} Camp"
        ];

        $discountPrice = 0;
        if ($checkout->Discount) {
            $discountPrice = $price * $checkout->discount_percentage / 100;
            $item_details[] = [
                'id'    => $checkout->Discount->code,
                'price' => -$discountPrice,
                'quantity'  => 1,
                'name'  => "Discount {$checkout->Discount->name} ({$checkout->discount_percentage}%)"
            ];
        }

        $total = $price - $discountPrice;

        $transaction_details = [
            'order_id'  => $orderId,
            'gross_amount' =>$total
        ];

        $userData = [
            "first_name"    => $checkout->User->name,
            "last_name"    => "",
            "address"    => $checkout->User->address,
            "city"    => "",
            "postal_code"    => "",
            "phone"    => $checkout->User->phone,
            "country_code"    => "IDN",
        ];

        $customor_details = [
            "first_name"    => $checkout->User->name,
            "last_name"    => "",
            "email"    => $checkout->User->email,
            "phone"    => $checkout->User->phone,
            "billing_address"    => $userData,
            "shippind_address"    => $userData,
        ];

        $midtrans_params = [
            'transaction_details'   => $transaction_details,
            'customor_details'   => $customor_details,
            'item_details'   => $item_details,
        ];

        try {
            
            // get snap payment page url

            $paymentUrl = \Midtrans\Snap::createTransaction($midtrans_params)->redirect_url;
            $checkout->midtrans_url = $paymentUrl;
            $checkout->total = $total;
            $checkout->save();

            return $paymentUrl;

        } catch (Exception $e) {
            return false;
        }
    }

    public function midtransCallback(Request $request)
    {
        $notif = $request->method() == 'POST' ? new Midtrans\Notification() : Midtrans\Transaction::status($request->order_id);

        $transaction_status = $notif->transaction_status;
        $fraud = $notif->transaction_status;

        $checkout_id = explode('-', $notif->order_id)[0];
        $checkout = Checkouts::find($checkout_id);

        if ($transaction_status == 'capture') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'challenge'
                $checkout->payment_status = 'pending';
            }
            else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'success'
                $checkout->payment_status = 'paid';
            }
        }
        else if ($transaction_status == 'cancel') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'failure'
                $checkout->payment_status = 'failed';
            }
            else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                $checkout->payment_status = 'failed';
            }
        }
        else if ($transaction_status == 'deny') {
            // TODO Set payment status in merchant's database to 'failure'
            $checkout->payment_status = 'failed';
        }
        else if ($transaction_status == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $checkout->payment_status = 'paid';
        }
        else if ($transaction_status == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $checkout->payment_status = 'pending';
        }
        else if ($transaction_status == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $checkout->payment_status = 'failed';
        }

        $checkout->save();
        return view('layouts.success_checkouts');
    }
}
