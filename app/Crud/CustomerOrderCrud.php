<?php
namespace App\Crud;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use App\Exceptions\NotAllowedException;
use App\Models\User;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\Order;

class CustomerOrderCrud extends OrderCrud
{
    protected $identifier   =   'dashboard/customers/orders';
    protected $namespace    =   'ns.customers-orders';
    protected $model        =   Order::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Label CRUD
     */
    public function getLabels()
    {
        return [
            'list_title'         =>  __( 'Daftar Pesanan Pelanggan' ),
            'list_description'   =>  __( 'Menampilkan semua pesanan pelanggan.' ),
            'no_entry'           =>  __( 'Belum ada pesanan pelanggan yang terdaftar.' ),
            'create_new'         =>  __( 'Tambah Pesanan Pelanggan Baru' ),
            'create_title'       =>  __( 'Buat Pesanan Pelanggan Baru' ),
            'create_description' =>  __( 'Daftarkan pesanan pelanggan baru dan simpan.' ),
            'edit_title'         =>  __( 'Ubah Pesanan Pelanggan' ),
            'edit_description'   =>  __( 'Modifikasi data pesanan pelanggan.' ),
            'back_to_list'       =>  __( 'Kembali ke Daftar Pesanan Pelanggan' ),
        ];
    }

    public function isEnabled( $feature )
    {
        return false;
    }

    public function hook( $query )
    {
        if ( empty( request()->query( 'direction' ) ) ) {
            $query->orderBy( 'id', 'desc' );
        }

        if ( ! empty( request()->query( 'customer_id' ) ) ) {
            $query->where( 'customer_id', request()->query( 'customer_id' ) );
        }
    }

    /**
     * Form Field
     */
    public function getForm( $entry = null ) 
    {
        return [
            'main' =>  [
                'label'         =>  __( 'Nama' ),
                'description'   =>  __( 'Berikan nama untuk data ini.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'fields'    =>  [
                        [
                            'type'  =>  'text',
                            'name'  =>  'author',
                            'label' =>  __( 'Pembuat' ),
                            'value' =>  $entry->author ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'change',
                            'label' =>  __( 'Perubahan' ),
                            'value' =>  $entry->change ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'code',
                            'label' =>  __( 'Kode' ),
                            'value' =>  $entry->code ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'created_at',
                            'label' =>  __( 'Dibuat Pada' ),
                            'value' =>  $entry->created_at ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'customer_id',
                            'label' =>  __( 'ID Pelanggan' ),
                            'value' =>  $entry->customer_id ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'delivery_status',
                            'label' =>  __( 'Status Pengiriman' ),
                            'value' =>  $entry->delivery_status ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'description',
                            'label' =>  __( 'Deskripsi' ),
                            'value' =>  $entry->description ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'final_payment_date',
                            'label' =>  __( 'Tanggal Pembayaran Terakhir' ),
                            'value' =>  $entry->final_payment_date ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'gross_total',
                            'label' =>  __( 'Total Kotor' ),
                            'value' =>  $entry->gross_total ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'id',
                            'label' =>  __( 'ID' ),
                            'value' =>  $entry->id ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'net_total',
                            'label' =>  __( 'Total Bersih' ),
                            'value' =>  $entry->net_total ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'payment_status',
                            'label' =>  __( 'Status Pembayaran' ),
                            'value' =>  $entry->payment_status ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'process_status',
                            'label' =>  __( 'Status Proses' ),
                            'value' =>  $entry->process_status ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'shipping',
                            'label' =>  __( 'Ongkir' ),
                            'value' =>  $entry->shipping ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'shipping_rate',
                            'label' =>  __( 'Tarif Pengiriman' ),
                            'value' =>  $entry->shipping_rate ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'shipping_type',
                            'label' =>  __( 'Tipe Pengiriman' ),
                            'value' =>  $entry->shipping_type ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'subtotal',
                            'label' =>  __( 'Sub Total' ),
                            'value' =>  $entry->subtotal ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'tax_value',
                            'label' =>  __( 'Pajak' ),
                            'value' =>  $entry->tax_value ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'tendered',
                            'label' =>  __( 'Dibayarkan' ),
                            'value' =>  $entry->tendered ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'title',
                            'label' =>  __( 'Judul' ),
                            'value' =>  $entry->title ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'total',
                            'label' =>  __( 'Total' ),
                            'value' =>  $entry->total ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'total_instalments',
                            'label' =>  __( 'Total Cicilan' ),
                            'value' =>  $entry->total_instalments ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'type',
                            'label' =>  __( 'Tipe' ),
                            'value' =>  $entry->type ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'updated_at',
                            'label' =>  __( 'Diperbarui Pada' ),
                            'value' =>  $entry->updated_at ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'uuid',
                            'label' =>  __( 'UUID' ),
                            'value' =>  $entry->uuid ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'voidance_reason',
                            'label' =>  __( 'Alasan Pembatalan' ),
                            'value' =>  $entry->voidance_reason ?? '',
                        ],
                    ]
                ]
            ]
        ];
    }

    public function filterPostInputs( $inputs )
    {
        return $inputs;
    }

    public function filterPutInputs( $inputs, Order $entry )
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

    public function afterPost( $request, Order $entry )
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
        if ( $namespace == 'ns.customers.orders' ) {
            if ( $this->permissions[ 'delete' ] !== false ) {
                ns()->restrict( $this->permissions[ 'delete' ] );
            } else {
                throw new NotAllowedException;
            }
        }
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
                if ( $entity instanceof Order ) {
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
            'list'      =>  ns()->url( 'dashboard/' . 'dashboard/customers/orders' ),
            'create'    =>  ns()->url( 'dashboard/' . 'dashboard/customers/orders/create' ),
            'edit'      =>  ns()->url( 'dashboard/' . 'dashboard/customers/orders/edit/' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.customers.orders' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.customers.orders/{id}' . '' ),
        ];
    }

    public function getBulkActions()
    {
        return Hook::filter( $this->namespace . '-bulk', [
            [
                'label'         =>  __( 'Hapus Grup Terpilih' ),
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
