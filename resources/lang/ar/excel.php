<?php

return [
    'currency' => 'ج.م',
    'invoices' => [
        'approved' => 'مرحلة',
        'not_approved' => 'غير مرحلة',
        'total_before_discount' => 'الاجمالي قبل الخصم و الضريبة:',
        'total_after_discount' => 'الاجمالي قبل الضريبة وبعد الخصم:',
        'total_after_tax' => 'صافي الاجمالي:',
        'discount' => 'الخصم',
        'tax' => 'الضريبة',
        'export_invoices' => [
            'title' => 'فاتورة بيع',
            'products_table' => [
                'name' => 'الصنف',
                'code' => 'الكود',
                'barcode' => 'الباركود',
                'quantity' => 'الكمية',
                'discount' => 'الخصم(%)',
                'sold_price' => 'سعر البيع',
            ],
        ],
        'import_invoices' => [
            'title' => 'فاتورة شراء',
            'products_table' => [
                'name' => 'الصنف',
                'code' => 'الكود',
                'barcode' => 'الباركود',
                'quantity' => 'الكمية',
                'discount' => 'الخصم(%)',
                'purchase_price' => 'سعر الشراء',
            ],
        ],
        'refund_invoices' => [
            'title' => 'امر مرتحجع',
            'products_table' => [
                'name' => 'الصنف',
                'code' => 'الكود',
                'barcode' => 'الباركود',
                'quantity' => 'الكمية',
                'discount' => 'الخصم(%)',
                'price' => 'سعر المرتجع',
            ],
        ],
    ],
    'customers_statement' => [
        'excel_file_name' => 'تقرير كشف حساب',
        'title' => 'كشف حساب',
        'refund' => 'مرتجع',
        'payment' => 'دفعة',
        'initiatory_credit' => 'رصيد افتتاحي',
        'check' => 'شيك',
        'table' => [
            'date' => 'التاريخ',
            'statement' => 'بيان',
            'creditor' => 'دائن',
            'debtor' => 'مدين',
            'credit' => 'الرصيد',
            'branch_name' => 'اسم العميل والفرع',
            'total' => 'اجمالي',
        ]
    ],
];
