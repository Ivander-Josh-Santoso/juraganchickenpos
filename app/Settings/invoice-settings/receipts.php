<?php

use App\Classes\Hook;
use App\Services\Helper;

return [
    'label'     =>      __( 'Struk' ),
    'fields'    =>      [
        [
            'label'     =>  __( 'Template Struk' ),
            'type'      =>  'select',
            'options'   =>  Helper::kvToJsOptions([
                'default'   =>  __( 'Default' )
            ]),
            'name'      =>  'ns_invoice_receipt_template',
            'value'     =>  $options->get( 'ns_invoice_receipt_template' ),
            'description'   =>  __( 'Pilih template yang diterapkan pada struk' )
        ], [
            'label'     =>  __( 'Logo Struk' ),
            'type'      =>  'media',
            'name'      =>  'ns_invoice_receipt_logo',
            'value'     =>  $options->get( 'ns_invoice_receipt_logo' ),
            'description'   =>  __( 'Berikan URL untuk logo.' )
        ], [
            'label'     =>  __( 'Gabungkan Produk pada Struk/Faktur' ),
            'type'      =>  'switch',
            'options'   =>  Helper::kvToJsOptions([ 
                'no'    =>  __( 'Tidak' ), 
                'yes'   =>  __( 'Ya' ) 
            ]),
            'name'      =>  'ns_invoice_merge_similar_products',
            'value'     =>  $options->get( 'ns_invoice_merge_similar_products' ),
            'description'   =>  __( 'Semua produk serupa akan digabung untuk menghindari pemborosan kertas pada struk/faktur.' )
        ], [
            'label'     =>  __( 'Footer Struk' ),
            'type'      =>  'textarea',
            'name'      =>  'ns_invoice_receipt_footer',
            'value'     =>  $options->get( 'ns_invoice_receipt_footer' ),
            'description'   =>  __( 'Jika Anda ingin menambahkan informasi di bagian bawah struk.' )
        ], [
            'label'         =>  __( 'Kolom A' ),
            'type'          =>  'textarea',
            'name'          =>  'ns_invoice_receipt_column_a',
            'value'         =>  $options->get( 'ns_invoice_receipt_column_a' ),
            'description'   =>  
            Hook::filter( 'ns-receipts-settings-tags', [
                __( 'Tag yang tersedia : ' ) . '<br>' .
                __( '{store_name}: menampilkan nama toko.' ) . "<br>",
                __( '{store_email}: menampilkan email toko.' ) . "<br>",
                __( '{store_phone}: menampilkan nomor telepon toko.' ) . "<br>",
                __( '{cashier_name}: menampilkan nama kasir.' ) . "<br>",
                __( '{cashier_id}: menampilkan ID kasir.' ) . "<br>",
                __( '{order_code}: menampilkan kode pesanan.' ) . "<br>",
                __( '{order_date}: menampilkan tanggal pesanan.' ) . "<br>",
                __( '{customer_name}: menampilkan nama pelanggan.' ) . "<br>",
                __( '{customer_email}: menampilkan email pelanggan.' ) . "<br>",
                __( '{shipping_name}: menampilkan nama penerima pengiriman.' ) . "<br>",
                __( '{shipping_surname}: menampilkan nama belakang penerima pengiriman.' ) . "<br>",
                __( '{shipping_phone}: menampilkan nomor telepon penerima pengiriman.' ) . "<br>",
                __( '{shipping_address_1}: menampilkan alamat pengiriman baris 1.' ) . "<br>",
                __( '{shipping_address_2}: menampilkan alamat pengiriman baris 2.' ) . "<br>",
                __( '{shipping_country}: menampilkan negara pengiriman.' ) . "<br>",
                __( '{shipping_city}: menampilkan kota pengiriman.' ) . "<br>",
                __( '{shipping_pobox}: menampilkan kode pos pengiriman.' ) . "<br>",
                __( '{shipping_company}: menampilkan nama perusahaan pengiriman.' ) . "<br>",
                __( '{shipping_email}: menampilkan email pengiriman.' ) . "<br>",
                __( '{billing_name}: menampilkan nama penagihan.' ) . "<br>",
                __( '{billing_surname}: menampilkan nama belakang penagihan.' ) . "<br>",
                __( '{billing_phone}: menampilkan nomor telepon penagihan.' ) . "<br>",
                __( '{billing_address_1}: menampilkan alamat penagihan baris 1.' ) . "<br>",
                __( '{billing_address_2}: menampilkan alamat penagihan baris 2.' ) . "<br>",
                __( '{billing_country}: menampilkan negara penagihan.' ) . "<br>",
                __( '{billing_city}: menampilkan kota penagihan.' ) . "<br>",
                __( '{billing_pobox}: menampilkan kode pos penagihan.' ) . "<br>",
                __( '{billing_company}: menampilkan nama perusahaan penagihan.' ) . "<br>",
                __( '{billing_email}: menampilkan email penagihan.' ) . "<br>"
            ])
        ], [
            'label'         =>  __( 'Kolom B' ),
            'type'          =>  'textarea',
            'name'          =>  'ns_invoice_receipt_column_b',
            'value'         =>  $options->get( 'ns_invoice_receipt_column_b' ),
            'description'   =>  
            Hook::filter( 'ns-receipts-settings-tags', [
                __( 'Tag yang tersedia :' ) . '<br>',
                __( '{store_name}: menampilkan nama toko.' ) . "<br>",
                __( '{store_email}: menampilkan email toko.' ) . "<br>",
                __( '{store_phone}: menampilkan nomor telepon toko.' ) . "<br>",
                __( '{cashier_name}: menampilkan nama kasir.' ) . "<br>",
                __( '{cashier_id}: menampilkan ID kasir.' ) . "<br>",
                __( '{order_code}: menampilkan kode pesanan.' ) . "<br>",
                __( '{order_date}: menampilkan tanggal pesanan.' ) . "<br>",
                __( '{customer_name}: menampilkan nama pelanggan.' ) . "<br>",
                __( '{customer_email}: menampilkan email pelanggan.' ) . "<br>",
                __( '{shipping_name}: menampilkan nama penerima pengiriman.' ) . "<br>",
                __( '{shipping_surname}: menampilkan nama belakang penerima pengiriman.' ) . "<br>",
                __( '{shipping_phone}: menampilkan nomor telepon penerima pengiriman.' ) . "<br>",
                __( '{shipping_address_1}: menampilkan alamat pengiriman baris 1.' ) . "<br>",
                __( '{shipping_address_2}: menampilkan alamat pengiriman baris 2.' ) . "<br>",
                __( '{shipping_country}: menampilkan negara pengiriman.' ) . "<br>",
                __( '{shipping_city}: menampilkan kota pengiriman.' ) . "<br>",
                __( '{shipping_pobox}: menampilkan kode pos pengiriman.' ) . "<br>",
                __( '{shipping_company}: menampilkan nama perusahaan pengiriman.' ) . "<br>",
                __( '{shipping_email}: menampilkan email pengiriman.' ) . "<br>",
                __( '{billing_name}: menampilkan nama penagihan.' ) . "<br>",
                __( '{billing_surname}: menampilkan nama belakang penagihan.' ) . "<br>",
                __( '{billing_phone}: menampilkan nomor telepon penagihan.' ) . "<br>",
                __( '{billing_address_1}: menampilkan alamat penagihan baris 1.' ) . "<br>",
                __( '{billing_address_2}: menampilkan alamat penagihan baris 2.' ) . "<br>",
                __( '{billing_country}: menampilkan negara penagihan.' ) . "<br>",
                __( '{billing_city}: menampilkan kota penagihan.' ) . "<br>",
                __( '{billing_pobox}: menampilkan kode pos penagihan.' ) . "<br>",
                __( '{billing_company}: menampilkan nama perusahaan penagihan.' ) . "<br>",
                __( '{billing_email}: menampilkan email penagihan.' ) . "<br>"
            ])
        ]
    ]
];
