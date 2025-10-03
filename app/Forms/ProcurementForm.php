<?php

namespace App\Forms;

use App\Classes\Hook;
use App\Models\Procurement;
use App\Models\Product;
use App\Models\Unit;
use App\Models\UnitGroup;
use App\Services\Helper;
use App\Services\SettingsPage;
use App\Services\UserOptions;

class ProcurementForm extends SettingsPage
{
    public function __construct()
    {        
        if ( ! empty( request()->route( 'identifier' ) ) ) {
            $procurement    =   Procurement::with( 'products' )
                ->with( 'provider' )
                ->find( request()->route( 'identifier' ) );
        }

        $this->form    =   [
            'main'          =>  [
                'name'      =>  'name',
                'type'      =>  'text',
                'value'     =>  $procurement->name ?? '',
                'label'     =>  __( 'Nama Pengadaan' ),
                'description'   =>  __( 'Berikan nama yang membantu mengidentifikasi pengadaan.' ),
                'validation'    =>  'required',
            ],
            'columns'   =>  Hook::filter( 'ns-procurement-columns', [
                'name'          =>  [
                    'label' =>  __( 'Nama' ),
                    'type'  =>  'name',
                ],
                'purchase_price_edit'    =>  [
                    'label' =>  __( 'Harga Satuan' ),
                    'type'  =>  'text'
                ],
                'tax_group_id'  =>  [
                    'label' =>  __( 'Pajak' ),
                    'type'  =>  'tax_group_id'
                ],
                'tax_value'       =>  [
                    'label' =>  __( 'Nilai Pajak' ),
                    'type'  =>  'currency'
                ],
                'unit_quantities'       =>  [
                    'label' =>  __( 'Satuan' ),
                    'type'  =>  'unit_quantities'
                ],
                'quantity'      =>  [
                    'label' =>  __( 'Jumlah' ),
                    'type'  =>  'text'
                ],
                'total_purchase_price'   =>  [
                    'label' =>  __( 'Total Harga' ),
                    'type'  =>  'currency'
                ],
            ]),
            'products'          =>  isset( $procurement ) ? $procurement->products->map( function( $_product ) {
                $product                     =   Product::findOrFail( $_product->product_id );
                $product->load( 'unit_quantities.unit' )->get();
                
                $_product->procurement       =   array_merge( $_product->toArray(), [
                    '$invalid'              =>  false,
                    'purchase_price_edit'   =>  $_product->purchase_price
                ]);

                $_product->unit_quantities  =   $product->unit_quantities;

                return $_product;
            }) : [],
            'tabs'               =>  [
                'general'       =>  include( dirname( __FILE__ ) . '/procurement/general.php' ),
            ]
        ];
    }
}
