<?php

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\ExpenseCategory;
use App\Services\Helper;

$expenses   =   ExpenseCategory::get();

return [
    'label'     =>  __( 'Umum' ),
    'fields'    =>  [
        [
            'label'         =>  __( 'Akun Arus Kas Pengadaan' ),
            'name'          =>  'ns_procurement_cashflow_account',
            'value'         =>  ns()->option->get( 'ns_procurement_cashflow_account' ),
            'description'   =>  __( 'Setiap pengadaan akan ditambahkan ke akun arus kas yang dipilih' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Arus Kas Penjualan' ),
            'name'          =>  'ns_sales_cashflow_account',
            'value'         =>  ns()->option->get( 'ns_sales_cashflow_account' ),
            'description'   =>  __( 'Setiap penjualan akan ditambahkan ke akun arus kas yang dipilih' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Kredit Pelanggan (kredit)' ),
            'name'          =>  'ns_customer_crediting_cashflow_account',
            'value'         =>  ns()->option->get( 'ns_customer_crediting_cashflow_account' ),
            'description'   =>  __( 'Setiap kredit pelanggan akan ditambahkan ke akun arus kas yang dipilih' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Kredit Pelanggan (debit)' ),
            'name'          =>  'ns_customer_debitting_cashflow_account',
            'value'         =>  ns()->option->get( 'ns_customer_debitting_cashflow_account' ),
            'description'   =>  __( 'Setiap pengurangan kredit pelanggan akan ditambahkan ke akun arus kas yang dipilih' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Refund Penjualan' ),
            'name'          =>  'ns_sales_refunds_account',
            'value'         =>  ns()->option->get( 'ns_sales_refunds_account' ),
            'description'   =>  __( 'Refund penjualan akan dilampirkan pada akun arus kas ini' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Retur Stok (Barang Rusak)' ),
            'name'          =>  'ns_stock_return_spoiled_account',
            'value'         =>  ns()->option->get( 'ns_stock_return_spoiled_account' ),
            'description'   =>  __( 'Retur stok untuk barang rusak akan dilampirkan pada akun ini' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Retur Stok (Barang Tidak Rusak)' ),
            'name'          =>  'ns_stock_return_unspoiled_account',
            'value'         =>  ns()->option->get( 'ns_stock_return_unspoiled_account' ),
            'description'   =>  __( 'Retur stok untuk barang tidak rusak akan dilampirkan pada akun ini' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Kas Masuk Kasir' ),
            'name'          =>  'ns_cashregister_cashin_cashflow_account',
            'value'         =>  ns()->option->get( 'ns_cashregister_cashin_cashflow_account' ),
            'description'   =>  __( 'Kas masuk kasir akan ditambahkan ke akun arus kas ini' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ], [
            'label'         =>  __( 'Akun Kas Keluar Kasir' ),
            'name'          =>  'ns_cashregister_cashout_cashflow_account',
            'value'         =>  ns()->option->get( 'ns_cashregister_cashout_cashflow_account' ),
            'description'   =>  __( 'Kas keluar kasir akan ditambahkan ke akun arus kas ini' ),
            'options'       =>  Helper::toJsOptions( $expenses, [ 'id', 'name' ]),
            'type'          =>  'select',
        ]
    ]
];
