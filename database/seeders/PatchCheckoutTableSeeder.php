<?php

namespace Database\Seeders;

use App\Models\Checkouts;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatchCheckoutTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $checkouts = Checkouts::whereTotal(0)->get();
        foreach ($checkouts as $key => $checkout) {
            $checkout->update([
                'total' => $checkout->Camp->price
            ]);
        }
    }
}
