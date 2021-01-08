<?php

namespace App\Providers;

use App\Repositories\Contracts\CategoryRepository;
use App\Repositories\Contracts\CustodyRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\ExpensesRepository;
use App\Repositories\Contracts\ExportInvoiceRepository;
use App\Repositories\Contracts\GeneralRepository;
use App\Repositories\Contracts\ImportInvoiceRepository;
use App\Repositories\Contracts\InitiatoryRepository;
use App\Repositories\Contracts\ProductDismissOrderRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\RefundRepository;
use App\Repositories\Contracts\RequirementRepository;
use App\Repositories\Contracts\SeasonRepository;
use App\Repositories\Contracts\SupplierRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\WarehouseRepository;
use App\Repositories\EloquentCategoryRepository;
use App\Repositories\EloquentCustodyRepository;
use App\Repositories\EloquentCustomerRepository;
use App\Repositories\EloquentExpensesRepository;
use App\Repositories\EloquentExportInvoiceRepository;
use App\Repositories\EloquentGeneralRepository;
use App\Repositories\EloquentImportInvoiceRepository;
use App\Repositories\EloquentInitiatoryRepository;
use App\Repositories\EloquentProductDismissOrderRepository;
use App\Repositories\EloquentProductRepository;
use App\Repositories\EloquentRefundRepository;
use App\Repositories\EloquentRequirementRepository;
use App\Repositories\EloquentSeasonRepository;
use App\Repositories\EloquentSupplierRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentWarehouseRepository;
use App\Repositories\Reports\Contracts\ReportProductRepository;
use App\Repositories\Reports\Contracts\ReportSalesRepository;
use App\Repositories\Reports\EloquentReportProductRepository;
use App\Repositories\Reports\EloquentReportSalesRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(SeasonRepository::class, EloquentSeasonRepository::class);
        $this->app->bind(SupplierRepository::class, EloquentSupplierRepository::class);
        $this->app->bind(WarehouseRepository::class, EloquentWarehouseRepository::class);
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(ImportInvoiceRepository::class, EloquentImportInvoiceRepository::class);
        $this->app->bind(RequirementRepository::class, EloquentRequirementRepository::class);
        $this->app->bind(InitiatoryRepository::class, EloquentInitiatoryRepository::class);
        $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
        $this->app->bind(ExportInvoiceRepository::class, EloquentExportInvoiceRepository::class);
        $this->app->bind(RefundRepository::class, EloquentRefundRepository::class);
        $this->app->bind(ProductDismissOrderRepository::class, EloquentProductDismissOrderRepository::class);
        $this->app->bind(GeneralRepository::class, EloquentGeneralRepository::class);

        // REPORTS
        $this->app->bind(ReportProductRepository::class, EloquentReportProductRepository::class);
        $this->app->bind(ReportSalesRepository::class, EloquentReportSalesRepository::class);
        $this->app->bind(ExpensesRepository::class, EloquentExpensesRepository::class);
        $this->app->bind(CustodyRepository::class, EloquentCustodyRepository::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
