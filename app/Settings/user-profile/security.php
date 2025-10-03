<?php
return [
    'label'     =>      __( 'Keamanan' ),
    'fields'    =>      [
        [
            'label'         =>  __( 'Kata Sandi Lama' ),
            'name'          =>  'old_password',
            'type'          =>  'password',
            'description'   =>  __( 'Masukkan kata sandi lama.' ),
        ], [
            'label'         =>  __( 'Kata Sandi' ),
            'name'          =>  'password',
            'type'          =>  'password',
            'description'   =>  __( 'Ganti kata sandi Anda dengan kata sandi yang lebih kuat.' ),
            'validation'    =>  'min:6',
        ], [
            'label'         =>  __( 'Konfirmasi Kata Sandi' ),
            'name'          =>  'password_confirm',
            'type'          =>  'password',
            'description'   =>  __( 'Ulangi kata sandi baru Anda untuk konfirmasi.' ),
            'validation'    =>  'min:6|same:security.password',
        ], 
    ]
];
