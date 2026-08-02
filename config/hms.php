<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hotel Settings
    |--------------------------------------------------------------------------
    */
    'hotel_name'    => env('HMS_HOTEL_NAME', 'Grand Hotel HMS'),
    'hotel_address' => env('HMS_HOTEL_ADDRESS', 'Accra, Ghana'),
    'hotel_phone'   => env('HMS_HOTEL_PHONE', '+233 302 000 000'),
    'hotel_email'   => env('HMS_HOTEL_EMAIL', 'info@grandhotel.hms'),

    /*
    |--------------------------------------------------------------------------
    | Billing Settings
    |--------------------------------------------------------------------------
    | Tax rate: 0.00 = 0% VAT (placeholder for student deployment).
    | Flag: Business Decision — Set actual rate in production via HMS_TAX_RATE env var.
    */
    'tax_rate' => (float) env('HMS_TAX_RATE', 0.00),

    /*
    |--------------------------------------------------------------------------
    | Booking Policy
    |--------------------------------------------------------------------------
    | Cancellation window in hours before check-in.
    | Business Decision: Free cancellation up to 24h before check-in (default).
    */
    'cancellation_hours' => (int) env('HMS_CANCELLATION_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */
    'currency'        => env('HMS_CURRENCY', 'GHS'),
    'currency_symbol' => env('HMS_CURRENCY_SYMBOL', 'GHS'),
];
