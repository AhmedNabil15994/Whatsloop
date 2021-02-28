<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    /**
     * Show Manager Dashboard.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('manager.dashboard');
    }
}
