<?php
namespace App\Fields;

use App\Classes\Hook;
use App\Services\FieldsService;

class AuthRegisterFields extends FieldsService
{
    public function get()
    {
        $fields     =   Hook::filter( 'ns-register-fields', [
            [
                'label'         =>  __( 'Nama Pengguna' ),
                'description'   =>  __( 'Masukkan nama pengguna Anda.' ),
                'validation'    =>  'required|min:5',
                'name'          =>  'username',
                'type'          =>  'text',
            ], [
                'label'         =>  __( 'Email' ),
                'description'   =>  __( 'Masukkan alamat email Anda.' ),
                'validation'    =>  'required|email',
                'name'          =>  'email',
                'type'          =>  'text',
            ], [
                'label'         =>  __( 'Kata Sandi' ),
                'description'   =>  __( 'Masukkan kata sandi Anda.' ),
                'validation'    =>  'required|min:6',
                'name'          =>  'password',
                'type'          =>  'password',
            ], [
                'label'         =>  __( 'Konfirmasi Kata Sandi' ),
                'description'   =>  __( 'Harus sama dengan kata sandi.' ),
                'validation'    =>  'required|min:6',
                'name'          =>  'password_confirm',
                'type'          =>  'password',
            ]
        ]);
        
        return $fields;
    }
}
