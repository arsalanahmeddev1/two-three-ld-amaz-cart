<?php

namespace App\Http\Controllers\Frontend;

class DummySeller
{
    public $id = 1;
    public $first_name = 'Dummy';
    public $last_name = 'Seller';

    public function sellerReviews()
    {
        return collect([]);
    }
}