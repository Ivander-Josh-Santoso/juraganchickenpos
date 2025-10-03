<?php
namespace App\Fields;

use App\Models\Order;
use App\Services\FieldsService;
use App\Services\Helper;

class LayawayFields extends FieldsService
{
    public function get()
    {
        $fields     =   [
            [
                'label'         =>  __( 'Cicilan' ),
                'description'   =>  __( 'Tentukan jumlah cicilan untuk pesanan ini.' ),
                'name'          =>  'total_instalments',
                'type'          =>  'number',
            ]
        ];
        
        return $fields;
    }
}
