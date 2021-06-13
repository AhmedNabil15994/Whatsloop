<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        '/users/add/uploadImage',
        '/users/edit/*/editImage',

        '/bots/add/uploadImage/*',
        '/bots/edit/editImage/*',

        '/groupMsgs/add/uploadImage/*',

        '/profile/personalInfo/uploadImage',

        '/whatsloop/webhooks/*',
        '/livechatApi/*',

        '/faqs/add/uploadImage',
        '/faqs/edit/*/editImage',
        
        '/tickets/add/uploadImage',
        '/tickets/edit/*/editImage',

        '/changeLogs/add/uploadImage',
        '/changeLogs/edit/*/editImage',

        '/clients/add/uploadImage',
        '/clients/edit/*/editImage',

        '/pushInvoice',
        '/invoices/*/pushInvoice',

    ];
}
