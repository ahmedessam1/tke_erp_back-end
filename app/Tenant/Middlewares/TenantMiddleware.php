<?php

namespace App\Tenant\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use DB;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // GET USER TENANT
        $user_tenant_id = Auth::user()->tenant_id;

        // GET TENANT NAME
        $tenant = DB::table('tenants')->where('id', $user_tenant_id)->first();
        if(!$tenant)
            abort(403, 'Wrong tenant, contact the admin...');

        // SWITCH TENANT
        config()->set('database.default', 'tenant');
        config()->set('database.connections.tenant.database', 'tke_'.$tenant->name);

        return $next($request);
    }
}
