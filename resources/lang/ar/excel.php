<?php

return [
    'currency' => 'ج.م',
    'invoices' => [
        'approved' => 'مرحلة',
        'not_approved' => 'غير مرحلة',
        'net_total' => 'اجمالي الفاتورة:',
        'discount_amount' => 'مضاف خصم علي الفاتورة:',
        'tax_amount' => 'مضاف ضريبة علي الفاتورة:',
        'total' => 'صافي اجمالي الفاتورة:',
        'discount' => 'الخصم:',
        'tax' => 'الضريبة:',
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
            'title' => 'امر مرتجع',
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
        'expenses' => 'مصاريف',
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
