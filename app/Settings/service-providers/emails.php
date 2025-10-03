<?php
use App\Services\Helper;

return [
    'label'     =>  __( 'Email' ),
    'fields'    =>  [
        [
            'label'         =>  __( 'Penyedia Email' ),
            'type'          =>  'select',
            'name'          =>  'ns_providers_email',
            'options'       =>  Helper::kvToJsOptions([
                'mailgun'   =>  __( 'Mailgun' ),
            ]),
            'description'   =>  __( 'Pilih penyedia email yang digunakan pada sistem.' )
        ]
    ]
];
