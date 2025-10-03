<?php
namespace App\Settings;

use App\Classes\Hook;
use App\Models\Role;
use App\Services\Helper;
use App\Services\Options;
use App\Services\SettingsPage;

class GeneralSettings extends SettingsPage
{
    public function __construct()
    {
        $options    =   app()->make( Options::class );
        
        $this->form     =   [
            'tabs'  =>  [
                'identification'   =>  [
                    'label' =>  __( 'Identifikasi' ),
                    'fields'    =>  [
                        [
                            'name'  =>  'ns_store_name',
                            'value'         =>  $options->get( 'ns_store_name' ),
                            'label' =>  __( 'Nama Toko' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Ini adalah nama toko.' ),
                            'validation'    =>  'required'                              
                        ], [
                            'name'  =>  'ns_store_address',
                            'value'         =>  $options->get( 'ns_store_address' ),
                            'label' =>  __( 'Alamat Toko' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Alamat lengkap toko.' ),
                        ], [
                            'name'  =>  'ns_store_city',
                            'value'         =>  $options->get( 'ns_store_city' ),
                            'label' =>  __( 'Kota Toko' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Kota tempat toko berada.' ),
                        ], [
                            'name'  =>  'ns_store_phone',
                            'value'         =>  $options->get( 'ns_store_phone' ),
                            'label' =>  __( 'Telepon Toko' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Nomor telepon untuk menghubungi toko.' ),
                        ], [
                            'name'  =>  'ns_store_email',
                            'value'         =>  $options->get( 'ns_store_email' ),
                            'label' =>  __( 'Email Toko' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Alamat email toko yang mungkin digunakan pada faktur atau laporan.' ),
                        ], [
                            'name'  =>  'ns_store_pobox',
                            'value'         =>  $options->get( 'ns_store_pobox' ),
                            'label' =>  __( 'Kotak Pos Toko' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Nomor kotak pos toko.' ),
                        ], [
                            'name'  =>  'ns_store_fax',
                            'value'         =>  $options->get( 'ns_store_fax' ),
                            'label' =>  __( 'Fax Toko' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Nomor fax toko.' ),     
                        ], [
                            'name'  =>  'ns_store_additional',
                            'value'         =>  $options->get( 'ns_store_additional' ),
                            'label' =>  __( 'Informasi Tambahan Toko' ), 
                            'type'          =>  'textarea',
                            'description'   =>  __( 'Informasi tambahan mengenai toko.' ),
                        ], [
                            'name'  =>  'ns_store_square_logo',
                            'value'         =>  $options->get( 'ns_store_square_logo' ),
                            'label' =>  __( 'Logo Persegi Toko' ), 
                            'type'          =>  'media',
                            'description'   =>  __( 'Pilih logo persegi toko.' ),
                        ], [
                            'name'  =>  'ns_store_rectangle_logo',
                            'value'         =>  $options->get( 'ns_store_rectangle_logo' ),
                            'label' =>  __( 'Logo Persegi Panjang Toko' ), 
                            'type'          =>  'media',
                            'description'   =>  __( 'Pilih logo persegi panjang toko.' ),
                        ], [
                            'name'          =>  'ns_store_language',
                            'value'         =>  $options->get( 'ns_store_language' ),
                            'options'         =>  Helper::kvToJsOptions( config( 'nexopos.languages' ) ),
                            'label'         =>  __( 'Bahasa' ), 
                            'type'          =>  'select',
                            'description'   =>  __( 'Tentukan bahasa default cadangan.' ),
                        ], 
                    ]
                ],
                'currency'   =>  [
                    'label' =>  __( 'Mata Uang' ),
                    'fields'    =>  [
                        [
                            'name'  =>  'ns_currency_symbol',
                            'value'         =>  $options->get( 'ns_currency_symbol' ),
                            'label' =>  __( 'Simbol Mata Uang' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Ini adalah simbol mata uang.' ),
                            'validation'    =>  'required'                              
                        ], [
                            'name'  =>  'ns_currency_iso',
                            'value'         =>  $options->get( 'ns_currency_iso' ),
                            'label' =>  __( 'Kode ISO Mata Uang' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Format internasional kode ISO mata uang.' ),
                            'validation'    =>  'required'                              
                        ], [
                            'name'  =>  'ns_currency_position',
                            'value'         =>  $options->get( 'ns_currency_position' ),
                            'label' =>  __( 'Posisi Mata Uang' ), 
                            'type'          =>  'select',
                            'options'       =>  [
                                [
                                    'label' =>  __( 'Sebelum jumlah' ),
                                    'value' =>  'before',
                                ], [
                                    'label' =>  __( 'Sesudah jumlah' ),
                                    'value' =>  'after',
                                ]
                            ],
                            'description'   =>  __( 'Tentukan posisi simbol mata uang.' ),
                        ], [
                            'name'  =>  'ns_currency_prefered',
                            'value'         =>  $options->get( 'ns_currency_prefered' ),
                            'label' =>  __( 'Preferensi Mata Uang' ), 
                            'type'          =>  'select',
                            'options'       =>  [
                                [
                                    'label' =>  __( 'Kode ISO Mata Uang' ),
                                    'value' =>  'iso',
                                ], [
                                    'label' =>  __( 'Simbol' ),
                                    'value' =>  'symbol',
                                ]
                            ],
                            'description'   =>  __( 'Tentukan indikator mata uang yang digunakan.' ),
                        ], [
                            'name'  =>  'ns_currency_thousand_separator',
                            'value'         =>  $options->get( 'ns_currency_thousand_separator' ),
                            'label' =>  __( 'Pemisah Ribuan Mata Uang' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Tentukan simbol pemisah ribuan. Default ",".' ),
                        ], [
                            'name'  =>  'ns_currency_decimal_separator',
                            'value'         =>  $options->get( 'ns_currency_decimal_separator' ),
                            'label' =>  __( 'Pemisah Desimal Mata Uang' ), 
                            'type'          =>  'text',
                            'description'   =>  __( 'Tentukan simbol pemisah desimal. Default ".".' ),
                        ], [
                            'name'  =>  'ns_currency_precision',
                            'value'         =>  $options->get( 'ns_currency_precision' ),
                            'label' =>  __( 'Presisi Mata Uang' ), 
                            'type'          =>  'select',
                            'options'       =>  collect([0,1,2,3,4,5])->map( function( $index ) {
                                return [
                                    'label' =>  sprintf( __( '%s angka di belakang koma' ), $index ),
                                    'value' =>  $index,
                                ];
                            })->toArray(),
                            'description'   =>  __( 'Tentukan jumlah angka di belakang koma.' ),
                        ],
                    ]
                ],
                'date'  =>  [
                    'label' =>  __( 'Tanggal' ),
                    'fields'    =>  [
                        [
                            'label'         =>  __( 'Format Tanggal' ),
                            'name'          =>  'ns_date_format',
                            'value'         =>  $options->get( 'ns_date_format' ),
                            'type'          =>  'text',
                            'description'   =>  __( 'Tentukan format tanggal. Default "Y-m-d".' ),
                        ], [
                            'label'         =>  __( 'Format Tanggal dan Waktu' ),
                            'name'          =>  'ns_datetime_format',
                            'value'         =>  $options->get( 'ns_datetime_format' ),
                            'type'          =>  'text',
                            'description'   =>  __( 'Tentukan format tanggal dan waktu. Default "Y-m-d H:i".' ),
                        ], [
                            'label'         =>  sprintf( __( 'Zona Waktu Tanggal (Sekarang: %s)' ), ns()->date->getNowFormatted() ),
                            'name'          =>  'ns_datetime_timezone',
                            'value'         =>  $options->get( 'ns_datetime_timezone' ),
                            'type'          =>  'select',
                            'options'       =>  Helper::kvToJsOptions( config( 'nexopos.timezones' ) ),
                            'description'   =>  __( 'Tentukan zona waktu default toko.' ),
                        ]
                    ]
                ],
                'registration'   =>  [
                    'label' =>  __( 'Registrasi' ),
                    'fields'    =>  [
                        [
                            'name'          =>  'ns_registration_enabled',
                            'value'         =>  $options->get( 'ns_registration_enabled' ),
                            'options'         =>  Helper::kvToJsOptions([
                                'yes'       =>  __( 'Ya' ),
                                'no'        =>  __( 'Tidak' )
                            ]),
                            'label' =>  __( 'Registrasi Terbuka' ), 
                            'type'          =>  'select',
                            'description'   =>  __( 'Tentukan apakah semua orang dapat mendaftar.' ),
                        ], [
                            'name'          =>  'ns_registration_role',
                            'value'         =>  $options->get( 'ns_registration_role' ),
                            'options'         =>  Helper::toJsOptions( Hook::filter( 'ns-registration-roles', Role::get() ), [ 'id', 'name' ]),
                            'label'         =>  __( 'Peran Registrasi' ), 
                            'type'          =>  'select',
                            'description'   =>  __( 'Pilih peran yang digunakan saat registrasi.' ),
                        ], [
                            'name'          =>  'ns_registration_validated',
                            'value'         =>  $options->get( 'ns_registration_validated' ),
                            'options'         =>  Helper::kvToJsOptions([
                                'yes'       =>  __( 'Ya' ),
                                'no'        =>  __( 'Tidak' )
                            ]),
                            'label' =>  __( 'Memerlukan Validasi' ), 
                            'type'          =>  'select',
                            'description'   =>  __( 'Memaksa validasi akun setelah registrasi.' ),
                        ], [
                            'name'          =>  'ns_recovery_enabled',
                            'value'         =>  $options->get( 'ns_recovery_enabled' ),
                            'options'         =>  Helper::kvToJsOptions([
                                'yes'       =>  __( 'Ya' ),
                                'no'        =>  __( 'Tidak' )
                            ]),
                            'label' =>  __( 'Izinkan Pemulihan' ), 
                            'type'          =>  'switch',
                            'description'   =>  __( 'Izinkan pengguna memulihkan akunnya.' ),
                        ], 
                    ]
                ],
            ]
        ];
    }
}
