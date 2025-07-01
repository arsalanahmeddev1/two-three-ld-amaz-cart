<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class MerchantsListController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenance_mode');
    }
    
    public function index()
    {
        // Get all active sellers order by created_at desc
        $data['sellers'] = User::where('role_id', 5)
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->with(['SellerAccount', 'SellerBusinessInformation'])
            ->paginate(12);
        
        return view(theme('pages.merchants'), $data);
    }
}