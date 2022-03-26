<?php

namespace App\Http\Middleware;

use Closure;
use Redirect;
use Session;
use Illuminate\Http\Request;
use App\Models\Invoice;

class HasMembershipConstraints
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        if((!Session::has('membership') || Session::get('membership') == null) && !Session::has('hasJob') && Session::get('group_id') == 1){
            
            if(Session::get('is_old') == 1 &&  Session::get('is_synced') != 1 && Session::get('group_id') == 1){       
                if( (in_array($request->segment(1),['sync','logout','menu']))){
                    return $next($request);
                }else{
                    return Redirect('/sync');
                }
            }elseif(Session::get('is_synced') != 1){
                if( (in_array($request->segment(1),['postBundle','checkout','packages','logout','completeOrder','completeJob','pushInvoice','pushInvoice2']))){
                    return $next($request);
                }else{
                    return Redirect('/packages');
                }   
            }elseif(Session::get('is_synced') == 1){
                return $next($request);
            }
        }elseif(Session::has('invoice_id') && Session::get('invoice_id') != 0 && Session::get('group_id') == 1){            
            if( (in_array($request->segment(1),['updateSubscription','dashboard','logout','menu','completeOrder','pushInvoice','pushInvoice2'])) ||
                ($request->segment(1) == 'profile' && $request->segment(2) == 'subscription') || 
                ($request->segment(1) == 'invoices' && $request->segment(2) == 'view') || 
                ($request->segment(1) == 'invoices' && $request->segment(3) == 'pushInvoice') ||
                ($request->segment(1) == 'checkout' && $request->segment(2) == 'bankTransfer') ){
                return $next($request);
            }else{
                return Redirect('/dashboard');
            }
        }elseif(Session::has('hasJob') && Session::get('hasJob') == 1 && Session::get('group_id') == 1){
            if( (in_array($request->segment(1),['logout','completeJob','dashboard']))){
                return $next($request);
            }else{
                return Redirect('/dashboard');
            }
        }else{
            return $next($request);
        }
    }
}
