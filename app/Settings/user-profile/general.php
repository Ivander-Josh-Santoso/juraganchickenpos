<?php
return [
    'label'     =>  __( 'Umum' ),
    'fields'    =>  [
        [
            'label'         =>  __( 'Nama Publik' ),
            'name'          =>  'public_name',
            'value'         =>  $options->get( 'public_name' ),
            'type'          =>  'text',
            'description'   =>  __( 'Tentukan nama publik pengguna. Jika tidak diisi, nama pengguna akan digunakan sebagai gantinya.' ),
        ], 
    ]
];
