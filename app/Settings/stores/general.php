<?php

use App\Services\Helper;

return [
    'label' =>  __( 'Umum' ),
    'fields'    =>  [
        [
            'type'  =>  'switch',
            'options'       =>  Helper::kvToJsOptions([
                'yes'       =>  __( 'Ya' ),
                'no'        =>  __( 'Tidak' )
            ]),
            'label'         =>  __( 'Aktifkan Mode Multistore' ),
            'value'         =>  $options->get( 'ns_store_multistore_enabled', 'no' ),
            'name'          =>  'ns_store_multistore_enabled',
            'description'   =>  __( 'Mengaktifkan mode multistore.' )
        ]
    ]
];
