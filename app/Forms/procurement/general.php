<?php

use App\Models\Provider;
use App\Services\Helper;

return [
    'label' =>  __( 'Pengadaan' ),
    'fields'    =>  [
        [
            'type'  =>  'text',
            'name'  =>  'invoice_reference',
            'value' =>  $procurement->invoice_reference ?? '',
            'label' =>  __( 'Nomor Faktur' ),
            'description'   =>  __( 'Jika pengadaan dilakukan di luar Juragan Chicken POS, berikan referensi unik di sini.' )
        ], [
            'type'  =>  'date',
            'name'  =>  'delivery_time',
            'value' =>  $procurement->delivery_time ?? '',
            'label' =>  __( 'Waktu Pengiriman' ),
            'description'   =>  __( 'Jika pengiriman harus dilakukan pada waktu tertentu, tentukan waktu tersebut disini.' )
        ], [
            'type'          =>  'switch',
            'name'          =>  'automatic_approval',
            'value' =>  $procurement->automatic_approval ?? '',
            'options'       =>  Helper::kvToJsOptions([
                0   =>  __( 'Tidak' ),
                1   =>  __( 'Ya' ),
            ]),
            'label'         =>  __( 'Persetujuan Otomatis' ),
            'description'   =>  __( 'Tentukan apakah pengadaan harus otomatis dianggap disetujui saat Waktu Pengiriman tiba.' )
        ], [
            'type'          =>  'select',
            'name'          =>  'delivery_status',
            'value' =>  $procurement->delivery_status ?? '',
            'validation'    =>  'required',
            'options'           =>  Helper::kvToJsOptions([
                'pending'       =>  __( 'Menunggu' ),
                'delivered'     =>  __( 'Terkirim' ),
            ]),
            'label'         =>  __( 'Status Pengiriman' ),
            'description'   =>  __( 'Tentukan status pengadaan yang sebenarnya. Setelah "Terkirim", status tidak bisa diubah dan stok akan diperbarui.' )
        ], [
            'type'          =>  'select',
            'name'          =>  'payment_status',
            'value'         =>  $procurement->payment_status ?? '',
            'validation'    =>  'required',
            'options'           =>  Helper::kvToJsOptions([
                'unpaid'    =>  __( 'Belum Dibayar' ),
                'paid'      =>  __( 'Sudah Dibayar' ),
            ]),
            'label'         =>  __( 'Status Pembayaran' ),
            'description'   =>  __( 'Tentukan status pembayaran pengadaan saat ini.' )
        ], [
            'type'          =>  'select',
            'name'          =>  'provider_id',
            'value' =>  $procurement->provider_id ?? '',
            'validation'    =>  'required',
            'options'       =>  Helper::toJsOptions( Provider::get(), [ 'id', 'name' ]),
            'label'         =>  __( 'Penyedia' ),
            'description'   =>  __( 'Tentukan penyedia pengadaan saat ini.' )
        ]
    ]
];
