<?php

namespace App\Http\Controllers\Exports;

use App\Exports\ExportInvoice;
use App\Http\Controllers\Controller;
use App\Events\ActionHappened;
use Excel;
use Auth;

class InvoicesController extends Controller
{
    private function getAuthUserId() {
        return Auth::user() -> id;
    }

    public function exportExportInvoiceToExcel ($type, $invoice_id) {
        $user = Auth::user();
        if ($type === 'exports') {
            if ($user -> hasRole(['super_admin'])) {
                // STORE ACTION
                event(new ActionHappened('report generate', 'exporting export invoice', $this->getAuthUserId()));
                return Excel::download(new ExportInvoice($type, $invoice_id), date('Y-mm-dd') . '.xlsx');
            }
        } else if ($type === 'imports') {
            if ($user->hasRole('super_admin')) {
                // STORE ACTION
                event(new ActionHappened('report generate', 'exporting import invoice', $this->getAuthUserId()));
                return Excel::download(new ExportInvoice($type, $invoice_id), date('Y-mm-dd') . '.xlsx');
            }
        } else if ($type === 'refunds') {
            if ($user->hasRole('super_admin')) {
                // STORE ACTION
                event(new ActionHappened('report generate', 'exporting refund invoice', $this->getAuthUserId()));
                return Excel::download(new ExportInvoice($type, $invoice_id), date('Y-mm-dd') . '.xlsx');
            }
        }
    }
}
