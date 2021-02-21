<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stancl\Tenancy\Features\UserImpersonation;

class ImpersonatesController extends Controller
{
    

    public function index($token)
    {	
        return UserImpersonation::makeResponse($token);
    }
}
