<?php

return [
    // NAMES
    'names' => [
        'min' => '3',
        'max' => '220',
    ],
    // CODE
    'code' => [
        'max' => '10',
    ],
    // ADDRESSES
    'addresses' => [
        'min' => '5',
        'max' => '255',
    ],
    // PHONE NUMBERS
    'phone_numbers' => [
        'min' => '10',
        'max' => '11',
    ],
    // DESCRIPTIONS
    'descriptions' => [
        'min' => '3',
        'max' => '9600',
    ],
    'small_descriptions' => [
        'max' => '255',
    ],
    // TITLES
    'titles' => [
        'min' => '3',
        'max' => '255',
    ],
    // BARCODE
    'barcode' => [
        'size' => '13',
    ],
    // LOCAL CODES
    'local_code' => [
        'size' => '13',
    ],
    // IMAGES NAME
    'images_name' => [
        'size' => '120',
    ],
    // IMAGE SIZES
    'images' => [
        'products' => [
            'large' => [
                'height' => '400'
            ],
            'thumbnail' => [
                'height' => '50'
            ],
            'max_number' => '6',
        ],
        'file_size'  => '20000',
        'extensions' => 'jpg,png,jpeg,gif,svg,JPG,JPEG,PNG',
    ],
    // DISCOUNT
    'discount' => [
        'max' => '60',
        'min' => '0',
    ],
    // QUANTITY
    'quantity' => [
        'max' => '8000',
        'min' => '1',
    ],
    // PAYMENT
    'payment' => [
        'max' => '1000000',
        'min' => '10',
    ],
    // NATIONAL ID
    'national_id' => [
        'max' => '15',
    ],
    // YEAR REPORT
    'year_report' => [
        'min' => '2014',
        'max' => Date('Y'),
    ],
];
