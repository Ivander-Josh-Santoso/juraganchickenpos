<?php

use App\Services\Helper;

return [
    'label' =>  __( 'Shortcut Keyboard' ),
    'fields'    =>  [
        [
            'name'              =>  'ns_pos_keyboard_cancel_order',
            'value'             =>  $options->get( 'ns_pos_keyboard_cancel_order' ),
            'label'             =>  __( 'Batalkan Pesanan' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk membatalkan pesanan saat ini.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_hold_order',
            'value'             =>  $options->get( 'ns_pos_keyboard_hold_order' ),
            'label'             =>  __( 'Tahan Pesanan' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk menahan pesanan saat ini.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_create_customer',
            'value'             =>  $options->get( 'ns_pos_keyboard_create_customer' ),
            'label'             =>  __( 'Buat Pelanggan' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk membuat pelanggan.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_payment',
            'value'             =>  $options->get( 'ns_pos_keyboard_payment' ),
            'label'             =>  __( 'Lanjutkan Pembayaran' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk melanjutkan ke tahap pembayaran.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_shipping',
            'value'             =>  $options->get( 'ns_pos_keyboard_shipping' ),
            'label'             =>  __( 'Buka Pengiriman' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk mengatur detail pengiriman.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_note',
            'value'             =>  $options->get( 'ns_pos_keyboard_note' ),
            'label'             =>  __( 'Buka Catatan' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk membuka catatan.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_calculator',
            'value'             =>  $options->get( 'ns_pos_keyboard_calculator' ),
            'label'             =>  __( 'Buka Kalkulator' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk membuka kalkulator.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_category_explorer',
            'value'             =>  $options->get( 'ns_pos_keyboard_category_explorer' ),
            'label'             =>  __( 'Buka Penjelajah Kategori' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk membuka penjelajah kategori.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_order_type',
            'value'             =>  $options->get( 'ns_pos_keyboard_order_type' ),
            'label'             =>  __( 'Pemilih Tipe Pesanan' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk membuka pemilih tipe pesanan.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_fullscreen',
            'value'             =>  $options->get( 'ns_pos_keyboard_fullscreen' ),
            'label'             =>  __( 'Alihkan Layar Penuh' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk mengalihkan mode layar penuh.' ),
        ], [
            'name'              =>  'ns_pos_keyboard_quick_search',
            'value'             =>  $options->get( 'ns_pos_keyboard_quick_search' ),
            'label'             =>  __( 'Pencarian Cepat' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut keyboard untuk membuka popup pencarian cepat.' ),
        ], [
            'name'              =>  'ns_pos_amount_shortcut',
            'value'             =>  $options->get( 'ns_pos_amount_shortcut' ),
            'label'             =>  __( 'Shortcut Nominal' ), 
            'type'              =>  'text',
            'description'       =>  __( 'Shortcut angka nominal dipisah dengan tanda "|".' ),
        ], 
    ]
];
