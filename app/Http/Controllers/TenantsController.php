<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantsController extends Controller
{
    

    public function store()
    {
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'subdomain' => ['required','string','unique:domains,domain'],     
            'phone' => ['required','string',],     
        ]);
        
        $tenant = Tenant::create();

        $tenant->domains()->create([
            'domain' => request('subdomain'),
        ]);

        $user = '';
        $tenant->run(function() use(&$user){
            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'phone' => request('phone'),
                'password' => Hash::make(request('password')),
                'group_id' => 1,
                'channels' => serialize([1002]), // Serialize Channels Codes Here
                'status' => 1,
                'sort' => User::newSortIndex(),
                'created_at' => now(),
            ]);
        });
        $redirectUrl = '/dashboard';
        $token = tenancy()->impersonate($tenant, $user->id, $redirectUrl);

        return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
            'token' => $token
        ]));

    }
}

