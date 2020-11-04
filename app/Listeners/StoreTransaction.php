<?php

namespace App\Listeners;

use App\Events\TransactionHappened;
use App\Models\Transaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreTransaction
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TransactionHappened  $event
     * @return void
     */
    public function handle(TransactionHappened $event)
    {
        $action_data   = $event -> data;

        Transaction::create([
            'model_type'      => $action_data['model_type'],
            'model_id'        => $action_data['model_id'],
            'case'            => $action_data['case'],
            'payment_type_id' => $action_data['payment_type_id'],
            'amount'          => $action_data['amount'],
            'created_by'      => $action_data['created_by'],
        ]);
    }
}
