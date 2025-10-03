<?php

use App\Services\Helper;

$audios     =   Helper::kvToJsOptions([
    ''          =>  __( 'Dinonaktifkan' ),
    url( '/audio/bubble.mp3' )  =>  __( 'Gelembung' ),
    url( '/audio/ding.mp3' )  =>  __( 'Ding' ),
    url( '/audio/pop.mp3' )  =>  __( 'Pop' ),
    url( '/audio/cash-sound.mp3' )  =>  __( 'Suara Uang' ),
]);

return [
    'label' =>  __( 'Tata Letak' ),
    'fields'    =>  [
        [
            'name'              =>  'ns_pos_layout',
            'value'             =>  $options->get( 'ns_pos_layout' ),
            'options'           =>  Helper::kvToJsOptions([
                'grocery_shop'      =>  __( 'Tata Letak Retail' ),
                'clothing_shop'     =>  __( 'Toko Pakaian' ),
            ]),
            'label'         =>  __( 'Tata Letak POS' ), 
            'type'          =>  'select',
            'description'   =>  __( 'Ubah tata letak POS.' ),
        ], [
            'name'              =>  'ns_pos_complete_sale_audio',
            'value'             =>  $options->get( 'ns_pos_complete_sale_audio' ),
            'options'           =>  $audios,
            'label'         =>  __( 'Suara Penyelesaian Penjualan' ), 
            'type'          =>  'select-audio',
            'description'   =>  __( 'Ubah suara saat penjualan selesai.' ),
        ], [
            'name'              =>  'ns_pos_new_item_audio',
            'value'             =>  $options->get( 'ns_pos_new_item_audio' ),
            'options'           =>  $audios,
            'label'         =>  __( 'Audio Item Baru' ), 
            'type'          =>  'select-audio',
            'description'   =>  __( 'Suara yang diputar saat item ditambahkan ke keranjang.' ),
        ], 
    ]
];
