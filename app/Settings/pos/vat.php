<?php

use App\Models\TaxGroup;
use App\Services\Helper;

$fields     =   [
    [
        'label'         =>  __( 'Jenis PPN' ),
        'name'          =>  'ns_pos_vat',
        'type'          =>  'select',
        'value'         =>  $options->get( 'ns_pos_vat' ),
        'description'   =>  __( 'Tentukan jenis PPN yang akan digunakan.' ),
        'options'       =>  Helper::kvToJsOptions([
            'disabled'                  =>  __( 'Dinonaktifkan' ),
            'flat_vat'                  =>  __( 'Tarif Tetap' ),
            'variable_vat'              =>  __( 'Tarif Fleksibel' ),
            'products_vat'              =>  __( 'PPN Produk' ),
            'products_flat_vat'         =>  __( 'Produk & Tarif Tetap' ),
            'products_variable_vat'     =>  __( 'Produk & Tarif Fleksibel' ),
        ])
    ]
];

if ( in_array( $options->get( 'ns_pos_vat' ), [ 'flat_vat', 'products_flat_vat' ] ) ) {
    $fields[]       =   [
        'type'      =>  'select',
        'name'      =>  'ns_pos_tax_group',
        'value'     =>  $options->get( 'ns_pos_tax_group' ),
        'options'   =>  Helper::toJsOptions( TaxGroup::get(), [ 'id', 'name' ] ),
        'label'     =>  __( 'Grup Pajak' ),
        'description'   =>  __( 'Tentukan grup pajak yang berlaku untuk penjualan.' )
    ];

    $fields[]       =   [
        'type'      =>  'select',
        'name'      =>  'ns_pos_tax_type',
        'value'     =>  $options->get( 'ns_pos_tax_type' ),
        'options'   =>  Helper::kvToJsOptions([
            'inclusive'     =>      __( 'Termasuk' ),
            'exclusive'     =>      __( 'Tidak Termasuk' )
        ]),
        'label'     =>  __( 'Tipe Pajak' ),
        'description'   =>  __( 'Tentukan bagaimana pajak dihitung pada penjualan.' )
    ];
}

return [
    'label'     =>  __( 'Pengaturan PPN' ),
    'fields'    =>  $fields
];
