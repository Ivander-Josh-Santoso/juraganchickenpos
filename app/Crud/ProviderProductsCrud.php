<?php
namespace App\Crud;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use App\Exceptions\NotAllowedException;
use App\Models\ProcurementProduct;
use App\Models\User;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\ProviderProduct;

class ProviderProductsCrud extends CrudService
{
    protected $table      =   'nexopos_procurements_products';
    protected $slug   =   '/dashboard/providers';
    protected $namespace  =   'ns.providers-products';
    protected $model      =   ProcurementProduct::class;
    protected $permissions  =   [
        'create'    =>  false,
        'read'      =>  true,
        'update'    =>  false,
        'delete'    =>  false,
    ];
    public $relations   =  [
        [ 'nexopos_taxes_groups as tax_group', 'nexopos_procurements_products.tax_group_id', '=', 'tax_group.id' ]
    ];
    protected $tabsRelations    =   [];
    public $pick        =   [
        'tax_group'     =>  [ 'name' ]
    ];
    protected $listWhere    =   [];
    protected $whereIn      =   [];
    public $fillable    =   [];

    public function __construct()
    {
        parent::__construct();
        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
    }

    public function getLabels()
    {
        return [
            'list_title'            =>  __( 'Daftar Produk Penyedia' ),
            'list_description'      =>  __( 'Menampilkan semua Produk Penyedia.' ),
            'no_entry'              =>  __( 'Belum ada Produk Penyedia yang terdaftar' ),
            'create_new'            =>  __( 'Tambah Produk Penyedia baru' ),
            'create_title'          =>  __( 'Buat Produk Penyedia baru' ),
            'create_description'    =>  __( 'Daftarkan Produk Penyedia baru dan simpan.' ),
            'edit_title'            =>  __( 'Edit Produk Penyedia' ),
            'edit_description'      =>  __( 'Ubah Produk Penyedia.' ),
            'back_to_list'          =>  __( 'Kembali ke Daftar Produk Penyedia' ),
        ];
    }

    public function isEnabled( $feature )
    {
        return false;
    }

    public function getForm( $entry = null ) 
    {
        return [
            'main' =>  [
                'label'         =>  __( 'Nama' ),
                'description'   =>  __( 'Berikan nama untuk sumber daya.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'fields'    =>  [
                        // ...
                    ]
                ]
            ]
        ];
    }

    public function filterPostInputs( $inputs )
    {
        return $inputs;
    }

    public function filterPutInputs( $inputs, ProcurementProduct $entry )
    {
        return $inputs;
    }

    public function beforePost( $request )
    {
        if ( $this->permissions[ 'create' ] !== false ) {
            ns()->restrict( $this->permissions[ 'create' ] );
        } else {
            throw new NotAllowedException;
        }
        return $request;
    }

    public function afterPost( $request, ProcurementProduct $entry )
    {
        return $request;
    }

    public function get( $param )
    {
        switch( $param ) {
            case 'model' : return $this->model ; break;
        }
    }

    public function beforePut( $request, $entry )
    {
        if ( $this->permissions[ 'update' ] !== false ) {
            ns()->restrict( $this->permissions[ 'update' ] );
        } else {
            throw new NotAllowedException;
        }
        return $request;
    }

    public function afterPut( $request, $entry )
    {
        return $request;
    }

    public function beforeDelete( $namespace, $id, $model ) {
        if ( $namespace == 'ns.providers-products' ) {
            if ( $this->permissions[ 'delete' ] !== false ) {
                ns()->restrict( $this->permissions[ 'delete' ] );
            } else {
                throw new NotAllowedException;
            }
        }
    }

    public function getColumns() {
        return [
            'name'  =>  [
                'label'  =>  __( 'Nama' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'purchase_price'  =>  [
                'label'  =>  __( 'Harga Beli' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
            'quantity'  =>  [
                'label'  =>  __( 'Jumlah' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
            'tax_group_name'  =>  [
                'label'  =>  __( 'Grup Pajak' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
            'barcode'  =>  [
                'label'  =>  __( 'Barcode' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
            'expiration_date'  =>  [
                'label'  =>  __( 'Tanggal Kedaluwarsa' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
            'tax_type'  =>  [
                'label'  =>  __( 'Tipe Pajak' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
            'tax_value'  =>  [
                'label'  =>  __( 'Nilai Pajak' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
            'total_purchase_price'  =>  [
                'label'  =>  __( 'Total Harga' ),
                '$direction'    =>  '',
                'width'         =>  '100px',
                '$sort'         =>  false
            ],
        ];
    }

    public function setActions( $entry, $namespace )
    {
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;
        
        $entry->purchase_price          =   ns()->currency->define( $entry->purchase_price )->format();
        $entry->tax_value               =   ns()->currency->define( $entry->tax_value )->format();
        $entry->total_purchase_price    =   ns()->currency->define( $entry->total_purchase_price )->format();
        $entry->expiration_date         =   $entry->expiration_date ?: __( 'Tidak Ada' );

        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Edit' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'url'           =>      ns()->url( '/dashboard/' . $this->slug . '/edit/' . $entry->id )
            ], [
                'label'     =>  __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       =>  ns()->url( '/api/nexopos/v4/crud/ns.providers-products/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Apakah Anda ingin menghapus ini?' ),
                ]
            ]
        ];

        return $entry;
    }

    public function hook( $query )
    {
        $query->whereIn( 'procurement_id', explode( ',', request()->query( 'procurements' ) ) );
    }

    public function bulkAction( Request $request ) 
    {
        if ( $request->input( 'action' ) == 'delete_selected' ) {
            if ( $this->permissions[ 'delete' ] !== false ) {
                ns()->restrict( $this->permissions[ 'delete' ] );
            } else {
                throw new NotAllowedException;
            }

            $status     =   [
                'success'   =>  0,
                'failed'    =>  0
            ];

            foreach ( $request->input( 'entries' ) as $id ) {
                $entity     =   $this->model::find( $id );
                if ( $entity instanceof ProviderProduct ) {
                    $entity->delete();
                    $status[ 'success' ]++;
                } else {
                    $status[ 'failed' ]++;
                }
            }
            return $status;
        }

        return Hook::filter( $this->namespace . '-catch-action', false, $request );
    }

    public function getLinks()
    {
        return  [
            'list'      =>  ns()->url( 'dashboard/' . '/dashboard/providers' ),
            'create'    =>  ns()->url( 'dashboard/' . '/dashboard/providers/create' ),
            'edit'      =>  ns()->url( 'dashboard/' . '/dashboard/providers/edit/' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.providers-products' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.providers-products/{id}' . '' ),
        ];
    }

    public function getBulkActions()
    {
        return Hook::filter( $this->namespace . '-bulk', [
            [
                'label'         =>  __( 'Hapus Grup yang Dipilih' ),
                'identifier'    =>  'delete_selected',
                'url'           =>  ns()->route( 'ns.api.crud-bulk-actions', [
                    'namespace' =>  $this->namespace
                ])
            ]
        ]);
    }

    public function getExports()
    {
        return [];
    }
}