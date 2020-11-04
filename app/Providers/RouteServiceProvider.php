<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
//        Route::prefix('api')
//             ->middleware('api')
//             ->namespace($this->namespace)
//             ->group(base_path('routes/api.php'));

        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function () {
            require base_path('routes/api.php');
            require base_path('routes/subroutes/users_routes.php');
            require base_path('routes/subroutes/seasons_routes.php');
            require base_path('routes/subroutes/suppliers_routes.php');
            require base_path('routes/subroutes/warehouses_routes.php');
            require base_path('routes/subroutes/categories_routes.php');
            require base_path('routes/subroutes/products_routes.php');
            require base_path('routes/subroutes/import_invoices_routes.php');
            require base_path('routes/subroutes/requirements_routes.php');
            require base_path('routes/subroutes/initiatory_routes.php');
            require base_path('routes/subroutes/customers_routes.php');
            require base_path('routes/subroutes/export_invoices_routes.php');
            require base_path('routes/subroutes/refunds_routes.php');
            require base_path('routes/subroutes/product_dismiss_orders_routes.php');

            // EXCELS
            require base_path('routes/subroutes/exports/exports_invoices_routes.php');

            // REPORTS
            require base_path('routes/subroutes/reports/products_report_routes.php');
            require base_path('routes/subroutes/reports/sales_report_routes.php');
        });
    }
}
