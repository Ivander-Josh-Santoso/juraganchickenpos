<?php

use App\Services\Helper;

return [
    'label' =>  __( 'Produk' ),
    'fields'    =>  [
        [
            'type'          =>  'switch',
            'name'          =>  'ns_notifications_products_stock_enabled',
            'label'         =>  __( 'Produk Stok Rendah' ),
            'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
            'value'         =>  intval( $options->get( 'ns_notifications_products_stock_enabled' ) ),
            'description'   =>  __( 'Tentukan apakah notifikasi harus diaktifkan untuk produk stok rendah' )
        ], [
            'type'          =>  'multiselect',
            'name'          =>  'ns_notifications_products_stock_channel',
            'label'         =>  __( 'Saluran Stok Rendah' ),
            'options'       =>  Helper::kvToJsOptions([
                'sms'       =>  __( 'SMS' ),
                'email'     =>  __( 'Email' ),
            ]),
            'value'         =>  $options->get( 'ns_notifications_products_stock_channel' ),
            'description'   =>  __( 'Tentukan saluran notifikasi untuk produk stok rendah.' )
        ], [
            'type'          =>  'switch',
            'name'          =>  'ns_notifications_products_expired_enabled',
            'label'         =>  __( 'Produk Kadaluarsa' ),
            'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
            'value'         =>  intval( $options->get( 'ns_notifications_products_expired_enabled' ) ),
            'description'   =>  __( 'Tentukan apakah notifikasi harus diaktifkan untuk produk kadaluarsa' )
        ], [
            'type'          =>  'multiselect',
            'name'          =>  'ns_notifications_products_expired_channel',
            'label'         =>  __( 'Saluran Kadaluarsa' ),
            'options'       =>  Helper::kvToJsOptions([
                'sms'       =>  __( 'SMS' ),
                'email'     =>  __( 'Email' ),
            ]),
            'value'         =>  $options->get( 'ns_notifications_products_expired_channel' ),
            'description'   =>  __( 'Tentukan saluran notifikasi untuk produk kadaluarsa.' )
        ]
    ]
];
