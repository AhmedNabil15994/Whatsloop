<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantPivot extends Pivot
{
    //
    //
    protected $table = 'tenant_users';
    protected $connection = 'main';
}