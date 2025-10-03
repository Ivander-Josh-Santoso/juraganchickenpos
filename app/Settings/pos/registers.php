<?php

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\Helper;

$cashRegisters  =   [
    [
        'name'          =>  'ns_pos_registers_enabled',
        'value'         =>  $options->get( 'ns_pos_registers_enabled' ),
        'options'       =>  Helper::kvToJsOptions([
            'yes'       =>  __( 'Ya' ),
            'no'        =>  __( 'Tidak' )
        ]),
        'label'         =>  __( 'Aktifkan Kasir' ), 
        'type'          =>  'select',
        'description'   =>  __( 'Tentukan apakah POS akan mendukung kasir.' ),
    ], [
        'name'          =>  'ns_pos_cashout_expense_category',
        'value'         =>  $options->get( 'ns_pos_cashout_expense_category' ),
        'options'       =>  Helper::toJsOptions( ExpenseCategory::get(), [ 'id', 'name' ]),
        'label'         =>  __( 'Kategori Pengeluaran Kas Keluar' ), 
        'type'          =>  'select',
        'description'   =>  __( 'Setiap kas keluar akan tercatat sebagai pengeluaran pada kategori yang dipilih.' ),
    ], 
];

if ( $options->get( 'ns_pos_registers_enabled' ) === 'yes' ) {
    $cashRegisters[]    =   [
        'label'     =>  __( 'Penghitung Tidak Aktif Kasir' ),
        'name'      =>  'ns_pos_idle_counter',
        'type'      =>  'select',
        'value'     =>  $options->get( 'ns_pos_idle_counter' ),
        'options'   =>  Helper::kvToJsOptions([
            'disabled'  =>  __( 'Dinonaktifkan' ),
            '5min'      =>  __( '5 Menit' ),
            '10min'     =>  __( '10 Menit' ),
            '15min'     =>  __( '15 Menit' ),
            '20min'     =>  __( '20 Menit' ),
            '30min'     =>  __( '30 Menit' ),
        ]),
        'description'   =>  __( 'Tentukan setelah berapa menit sistem akan menganggap kasir tidak aktif.' ),
    ];

    $cashRegisters[]    =   [
        'label'         =>  __( 'Pengeluaran Kas' ),
        'name'          =>  'ns_pos_disbursement',
        'type'          =>  'select',
        'value'         =>  $options->get( 'ns_pos_disbursement_enabled' ),
        'description'   =>  __( 'Izinkan kasir melakukan pengeluaran kas.' ),
        'options'       =>  Helper::kvToJsOptions([
            'yes'       =>  __( 'Ya' ),
            'no'        =>  __( 'Tidak' ),
        ])
    ];
}

return [
    'label' =>  __( 'Kasir' ),
    'fields'    =>  $cashRegisters
];
