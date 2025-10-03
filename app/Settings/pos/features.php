<?php

use App\Services\Helper;

return [
    'label' =>  __( 'Fitur' ),
    'fields'    =>  [
        [
            'name'              =>  'ns_pos_sound_enabled',
            'value'             =>  $options->get( 'ns_pos_sound_enabled' ),
            'label'             =>  __( 'Efek Suara' ), 
            'type'              =>  'switch',
            'options'           =>  Helper::kvToJsOptions([
                'yes'           =>  __( 'Ya' ),
                'no'            =>  __( 'Tidak' )
            ]),
            'description'       =>  __( 'Aktifkan efek suara di POS.' ),
        ], [
            'name'              =>  'ns_pos_show_quantity',
            'value'             =>  $options->get( 'ns_pos_show_quantity' ),
            'label'             =>  __( 'Tampilkan Kuantitas' ), 
            'type'              =>  'switch',
            'options'           =>  Helper::kvToJsOptions([
                'yes'           =>  __( 'Ya' ),
                'no'            =>  __( 'Tidak' )
            ]),
            'description'       =>  __( 'Menampilkan pemilih jumlah saat memilih produk. Jika tidak, jumlah default ditetapkan ke 1.' ),
        ], [
            'name'              =>  'ns_pos_customers_creation_enabled',
            'value'             =>  $options->get( 'ns_pos_customers_creation_enabled' ),
            'label'             =>  __( 'Izinkan Pembuatan Pelanggan' ), 
            'type'              =>  'switch',
            'options'           =>  Helper::kvToJsOptions([
                'yes'           =>  __( 'Ya' ),
                'no'            =>  __( 'Tidak' )
            ]),
            'description'       =>  __( 'Izinkan pelanggan dibuat pada POS.' ),
        ], [
            'name'              =>  'ns_pos_quick_product',
            'value'             =>  $options->get( 'ns_pos_quick_product' ),
            'label'             =>  __( 'Produk Cepat' ), 
            'type'              =>  'switch',
            'options'           =>  Helper::kvToJsOptions([
                'yes'           =>  __( 'Ya' ),
                'no'            =>  __( 'Tidak' )
            ]),
            'description'       =>  __( 'Izinkan produk cepat dibuat dari POS.' ),
        ], [
            'name'              =>  'ns_pos_order_sms',
            'value'             =>  $options->get( 'ns_pos_order_sms' ),
            'label'             =>  __( 'Konfirmasi Pesanan via SMS' ), 
            'type'              =>  'switch',
            'options'           =>  Helper::kvToJsOptions([
                'yes'           =>  __( 'Ya' ),
                'no'            =>  __( 'Tidak' )
            ]),
            'description'       =>  __( 'Mengirim SMS ke pelanggan setelah pesanan dibuat.' ),
        ], [
            'name'              =>  'ns_pos_unit_price_ediable',
            'value'             =>  $options->get( 'ns_pos_unit_price_ediable' ),
            'label'             =>  __( 'Harga Satuan Dapat Diedit' ), 
            'type'              =>  'switch',
            'options'           =>  Helper::kvToJsOptions([
                'yes'           =>  __( 'Ya' ),
                'no'            =>  __( 'Tidak' )
            ]),
            'description'       =>  __( 'Izinkan harga satuan produk untuk diedit.' ),
        ], [
            'name'              =>  'ns_pos_gross_price_used',
            'value'             =>  $options->get( 'ns_pos_gross_price_used' ),
            'label'             =>  __( 'Gunakan Harga Kotor' ), 
            'type'              =>  'switch',
            'options'           =>  Helper::kvToJsOptions([
                'yes'           =>  __( 'Ya' ),
                'no'            =>  __( 'Tidak' )
            ]),
            'description'       =>  __( 'Menggunakan harga kotor untuk setiap produk.' ),
        ], [
            'name'              =>  'ns_pos_order_types',
            'value'             =>  $options->get( 'ns_pos_order_types' ),
            'label'             =>  __( 'Tipe Pesanan' ), 
            'type'              =>  'multiselect',
            'options'           =>  Helper::kvToJsOptions([
                'delivery'      =>  __( 'Pengantaran' ),
                'take_away'     =>  __( 'Bawa Pulang' )
            ]),
            'description'       =>  __( 'Kontrol tipe pesanan yang diaktifkan.' ),
        ],
    ]
];
