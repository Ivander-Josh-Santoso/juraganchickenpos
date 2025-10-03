<?php
namespace App\Fields;

use App\Services\FieldsService;
use App\Services\Helper;

class PosOrderSettingsFields extends FieldsService
{
    public function get()
    {
        $fields     =   [
            [
                'label'         =>  __( 'Nama pesanan' ),
                'description'   =>  __( 'Tentukan nama pesanan' ),
                'validation'    =>  'required',
                'name'          =>  'title',
                'type'          =>  'text',
            ], [
                'label'         =>  __( 'Dibuat untuk tanggal' ),
                'description'   =>  __( 'Tentukan tanggal pembuatan pesanan' ),
                'name'          =>  'created_at',
                'type'          =>  'date',
            ], 
        ];
        
        return $fields;
    }
}