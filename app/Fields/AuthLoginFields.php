<?php
namespace App\Fields;

use App\Classes\Hook;
use App\Services\FieldsService;

class AuthLoginFields extends FieldsService
{
    public function get()
    {
        $fields     =   Hook::filter( 'ns-login-fields', [
            [
                'label'         =>  __( 'Username' ),
                'description'   =>  __( 'Masukkan nama pengguna Anda.' ),
                'validation'    =>  'required',
                'name'          =>  'username',
                'type'          =>  'text',
            ], [
                'label'         =>  __( 'Password' ),
                'description'   =>  __( 'Masukkan kata sandi Anda.' ),
                'validation'    =>  'required',
                'name'          =>  'password',
                'type'          =>  'password',
            ]
        ]);
        
        return $fields;
    }
}