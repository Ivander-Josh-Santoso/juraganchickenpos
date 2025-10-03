<?php

use App\Services\Helper;

return [
    'label' =>  __( 'Umum' ),
    'fields'    =>  [
        [
            'type'          =>  'switch',
            'label'         =>  __( 'Aktifkan Pekerja' ),
            'description'   =>  __( 'Aktifkan layanan latar belakang untuk Juragan Chicken POS. Segarkan untuk memeriksa apakah opsi sudah berubah menjadi "Ya".' ),
            'name'          =>  'ns_workers_enabled',
            'value'         =>  $options->get( 'ns_workers_enabled', 'no' ),
            'options'       =>  collect( Helper::kvToJsOptions([ 
                'no'            =>  __( 'Tidak' ), 
                'await_confirm' =>  __( 'Uji' ),
                'yes'           =>  __( 'Ya' )
            ]) )->map( function( $option ) {
                $option[ 'disabled' ] = false;
                if ( $option[ 'value' ] === 'yes' ) {
                    $option[ 'disabled' ]   =   true;
                }
                return $option;
            })
        ]
    ]
];
