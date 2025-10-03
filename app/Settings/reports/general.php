<?php

use App\Services\Helper;

return [
    'label' =>  __( 'Umum' ),
    'fields'    =>  [
        [
            'type'  =>  'switch',
            'label' =>  __( 'Aktifkan Pelaporan Email' ),
            'options'   =>  Helper::kvToJsOptions([
                'yes'   =>  __( 'Ya' ),
                'no'    =>  __( 'Tidak' ),
            ]),
            'description'   =>  __( 'Tentukan apakah pelaporan diaktifkan secara global.' )
        ]
    ]
];
