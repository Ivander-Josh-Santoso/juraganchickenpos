<?php

use App\Models\ExpenseCategory;
use App\Services\Helper;

return [
    'label'     =>  __( 'Umum' ),
    'fields'    =>  [
        [
            'type'  =>  'select',
            'label'     =>  __( 'Tipe Kode Pesanan' ),
            'description'   =>  __( 'Tentukan bagaimana sistem akan menghasilkan kode untuk setiap pesanan.' ),
            'name'  =>  'ns_orders_code_type',
            'value'     =>  $options->get( 'ns_orders_code_type' ),
            'options'   =>  Helper::kvToJsOptions([
                'date_sequential'   =>  __( 'Berurutan berdasarkan tanggal' ),
                'random_code'       =>  __( 'Kode Acak' ),
                'number_sequential' =>  __( 'Berurutan berdasarkan nomor' ),
            ])
        ], [
            'type'  =>  'switch',
            'label'     =>  __( 'Izinkan Pesanan Belum Dibayar' ),
            'name'  =>  'ns_orders_allow_unpaid',
            'value'     =>  $options->get( 'ns_orders_allow_unpaid' ),
            'description'   =>  __( 'Mencegah pesanan yang belum lengkap untuk ditempatkan. Jika kredit diizinkan, opsi ini harus diatur ke "ya".' ),
            'options'   =>  Helper::kvToJsOptions([
                'yes'   =>  __( 'Ya' ),
                'no'    =>  __( 'Tidak' ),
            ])
        ], [
            'type'  =>  'switch',
            'label'     =>  __( 'Izinkan Pesanan Parsial' ),
            'name'  =>  'ns_orders_allow_partial',
            'value'     =>  $options->get( 'ns_orders_allow_partial' ),
            'description'   =>  __( 'Mencegah pesanan yang dibayar sebagian untuk ditempatkan.' ),
            'options'   =>  Helper::kvToJsOptions([
                'yes'   =>  __( 'Ya' ),
                'no'    =>  __( 'Tidak' ),
            ])
        ], [
            'type'  =>  'select',
            'label'     =>  __( 'Kadaluarsa Penawaran Harga' ),
            'name'  =>  'ns_orders_quotation_expiration',
            'value'     =>  $options->get( 'ns_orders_quotation_expiration' ),
            'description'   =>  __( 'Penawaran harga akan dihapus setelah mencapai waktu yang ditentukan.' ),
            'options'   =>  Helper::kvToJsOptions( collect([3,5,10,15,30])->mapWithKeys( function( $days ) {
                return [
                    $days  =>  sprintf( __( '%s Hari' ), $days )
                ];
            }))
        ], [
            'type'      =>  'select',
            'label'     =>  __( 'Tindak Lanjut Pesanan' ),
            'name'      =>  'ns_orders_follow_up',
            'value'     =>  $options->get( 'ns_orders_follow_up' ),
            'description'   =>  __( 'Penawaran harga akan dihapus setelah mencapai waktu yang ditentukan.' ),
            'options'   =>  Helper::kvToJsOptions([
                'yes'   =>  __( 'Ya' ),
                'no'    =>  __( 'Tidak' )
            ])
        ]
    ]
];
