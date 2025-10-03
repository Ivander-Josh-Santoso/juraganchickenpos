<?php
use App\Services\Helper;

return [
    'label'     =>  __( 'SMS' ),
    'fields'    =>  [
        [
            'label'         =>  __( 'Penyedia SMS' ),
            'name'          =>  'ns_providers_sms',
            'value'         =>  $options->get( 'ns_providers_sms' ),
            'type'          =>  'select',
            'options'       =>  Helper::kvToJsOptions([
                'twilio'    =>  __( 'Twilio' ),
            ]),
            'description'   =>  __( 'Pilih penyedia SMS yang digunakan pada sistem.' )
        ]
    ]
];
