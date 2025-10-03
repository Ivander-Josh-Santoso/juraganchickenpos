<?php

use App\Services\Helper;
use App\Classes\Hook;

return [
    'label' =>  __( 'Pencetakan' ),
    'fields'    =>  Hook::filter( 'ns-printing-settings-fields', [
        [
            'name'              =>  'ns_pos_printing_document',
            'value'             =>  $options->get( 'ns_pos_printing_document' ),
            'label'             =>  __( 'Dokumen yang Dicetak' ), 
            'type'              =>  'select',
            'options'           =>  Helper::kvToJsOptions([
                'invoice'       =>  __( 'Faktur' ),
                'receipt'       =>  __( 'Struk' )
            ]),
            'description'       =>  __( 'Pilih dokumen yang digunakan untuk mencetak setelah penjualan.' ),
        ], [
            'name'              =>  'ns_pos_printing_enabled_for',
            'value'             =>  $options->get( 'ns_pos_printing_enabled_for' ),
            'label'             =>  __( 'Pencetakan Diaktifkan Untuk' ), 
            'type'              =>  'select',
            'options'           =>  Helper::kvToJsOptions([
                'disabled'              =>  __( 'Dinonaktifkan' ),
                'all_orders'            =>  __( 'Semua Pesanan' ),
                'partially_paid_orders' =>  __( 'Dari Pesanan yang Dibayar Sebagian' ),
                'only_paid_ordes'       =>  __( 'Hanya Pesanan yang Dibayar' ),
            ]),
            'description'       =>  __( 'Tentukan kapan pencetakan harus diaktifkan.' ),
        ], [
            'name'              =>  'ns_pos_printing_gateway',
            'value'             =>  $options->get( 'ns_pos_printing_gateway' ),
            'label'             =>  __( 'Gateway Pencetakan' ), 
            'type'              =>  'select',
            'options'           =>  Helper::kvToJsOptions([
                'default'           =>  __( 'Pencetakan Default (web)' ),
            ]),
            'description'       =>  __( 'Tentukan gateway yang digunakan untuk pencetakan.' ),
        ], 
    ])
];
