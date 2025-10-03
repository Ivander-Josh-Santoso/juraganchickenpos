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
use App\Models\OrderTax;

class HoldOrderCrud extends CrudService
{
    protected $table        =   'nexopos_orders';
    protected $identifier   =   'ns.hold-orders';
    protected $namespace    =   'ns.hold-orders';
    protected $model        =   Order::class;

    protected $permissions  =   [
        'create'    =>  true,
        'read'      =>  true,
        'update'    =>  true,
        'delete'    =>  true,
    ];

    public $relations   =  [
        [ 'nexopos_users', 'nexopos_orders.author', '=', 'nexopos_users.id' ],
        [ 'nexopos_customers', 'nexopos_customers.id', '=', 'nexopos_orders.customer_id' ],
    ];

    protected $tabsRelations = [];

    public $pick = [
        'nexopos_users'     =>  [ 'username' ],
        'nexopos_customers' =>  [ 'name' ],
    ];

    protected $listWhere = [];
    protected $whereIn   = [];
    public $fillable     = [];
    protected $bulkActions = [];

    public function __construct()
    {
        parent::__construct();

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
        $this->bulkActions  =   [];
    }

    public function hook( $query )
    {
        $query->orderBy( 'created_at', 'desc' );
        $query->where( 'payment_status', 'hold' );
    }

    public function getLabels()
    {
        return [
            'list_title'         =>  __( 'Daftar Pesanan Ditahan' ),
            'list_description'   =>  __( 'Tampilkan semua pesanan yang ditahan.' ),
            'no_entry'           =>  __( 'Belum ada pesanan yang ditahan.' ),
            'create_new'         =>  __( 'Tambah pesanan ditahan baru' ),
            'create_title'       =>  __( 'Buat pesanan ditahan baru' ),
            'create_description' =>  __( 'Daftarkan pesanan ditahan baru dan simpan.' ),
            'edit_title'         =>  __( 'Ubah pesanan ditahan' ),
            'edit_description'   =>  __( 'Modifikasi pesanan yang ditahan.' ),
            'back_to_list'       =>  __( 'Kembali ke daftar pesanan ditahan' ),
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
                'description'   =>  __( 'Berikan nama untuk sumber daya ini.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'fields'    =>  [
                        [ 'type'=>'text','name'=>'author','label'=>__( 'Pembuat' ),'value'=>$entry->author ?? '' ],
                        [ 'type'=>'text','name'=>'change','label'=>__( 'Kembalian' ),'value'=>$entry->change ?? '' ],
                        [ 'type'=>'text','name'=>'code','label'=>__( 'Kode' ),'value'=>$entry->code ?? '' ],
                        [ 'type'=>'text','name'=>'created_at','label'=>__( 'Dibuat Pada' ),'value'=>$entry->created_at ?? '' ],
                        [ 'type'=>'text','name'=>'customer_id','label'=>__( 'ID Pelanggan' ),'value'=>$entry->customer_id ?? '' ],
                        [ 'type'=>'text','name'=>'delivery_status','label'=>__( 'Status Pengiriman' ),'value'=>$entry->delivery_status ?? '' ],
                        [ 'type'=>'text','name'=>'description','label'=>__( 'Deskripsi' ),'value'=>$entry->description ?? '' ],
                        [ 'type'=>'text','name'=>'discount','label'=>__( 'Diskon' ),'value'=>$entry->discount ?? '' ],
                        [ 'type'=>'text','name'=>'discount_percentage','label'=>__( 'Persentase Diskon' ),'value'=>$entry->discount_percentage ?? '' ],
                        [ 'type'=>'text','name'=>'discount_type','label'=>__( 'Jenis Diskon' ),'value'=>$entry->discount_type ?? '' ],
                        [ 'type'=>'text','name'=>'gross_total','label'=>__( 'Total Kotor' ),'value'=>$entry->gross_total ?? '' ],
                        [ 'type'=>'text','name'=>'id','label'=>__( 'ID' ),'value'=>$entry->id ?? '' ],
                        [ 'type'=>'text','name'=>'net_total','label'=>__( 'Total Bersih' ),'value'=>$entry->net_total ?? '' ],
                        [ 'type'=>'text','name'=>'payment_status','label'=>__( 'Status Pembayaran' ),'value'=>$entry->payment_status ?? '' ],
                        [ 'type'=>'text','name'=>'process_status','label'=>__( 'Status Proses' ),'value'=>$entry->process_status ?? '' ],
                        [ 'type'=>'text','name'=>'shipping','label'=>__( 'Pengiriman' ),'value'=>$entry->shipping ?? '' ],
                        [ 'type'=>'text','name'=>'shipping_rate','label'=>__( 'Biaya Pengiriman' ),'value'=>$entry->shipping_rate ?? '' ],
                        [ 'type'=>'text','name'=>'shipping_type','label'=>__( 'Jenis Pengiriman' ),'value'=>$entry->shipping_type ?? '' ],
                        [ 'type'=>'text','name'=>'subtotal','label'=>__( 'Sub Total' ),'value'=>$entry->subtotal ?? '' ],
                        [ 'type'=>'text','name'=>'tax_value','label'=>__( 'Pajak' ),'value'=>$entry->tax_value ?? '' ],
                        [ 'type'=>'text','name'=>'tendered','label'=>__( 'Dibayarkan' ),'value'=>$entry->tendered ?? '' ],
                        [ 'type'=>'text','name'=>'title','label'=>__( 'Judul' ),'value'=>$entry->title ?? '' ],
                        [ 'type'=>'text','name'=>'total','label'=>__( 'Total' ),'value'=>$entry->total ?? '' ],
                        [ 'type'=>'text','name'=>'type','label'=>__( 'Tipe' ),'value'=>$entry->type ?? '' ],
                        [ 'type'=>'text','name'=>'updated_at','label'=>__( 'Diperbarui Pada' ),'value'=>$entry->updated_at ?? '' ],
                        [ 'type'=>'text','name'=>'uuid','label'=>__( 'UUID' ),'value'=>$entry->uuid ?? '' ],
                    ]
                ]
            ]
        ];
    }

    public function filterPostInputs( $inputs ){ return $inputs; }
    public function filterPutInputs( $inputs, Order $entry ){ return $inputs; }

    public function beforePost( $request )
    {
        if ( $this->permissions['create'] !== false ) {
            ns()->restrict( $this->permissions['create'] );
        } else throw new NotAllowedException;
        return $request;
    }

    public function afterPost( $request, Order $entry ){ return $request; }

    public function get( $param )
    {
        switch( $param ) {
            case 'model' : return $this->model; break;
        }
    }

    public function beforePut( $request, $entry )
    {
        if ( $this->permissions['update'] !== false ) {
            ns()->restrict( $this->permissions['update'] );
        } else throw new NotAllowedException;
        return $request;
    }

    public function afterPut( $request, $entry ){ return $request; }

    public function beforeDelete( $namespace, $id, $model )
    {
        if ( $namespace == 'ns.hold-orders' ) {
            if ( $this->permissions['delete'] !== false ) {
                ns()->restrict( $this->permissions['delete'] );
            } else throw new NotAllowedException;
        }
    }

    public function getColumns() {
        return [
            'code'  =>  [
                'label' =>  __( 'Kode' ),
                '$direction' => '',
                'width' => '120px',
                '$sort' => false
            ],
            'nexopos_customers_name'  =>  [
                'label' =>  __( 'Pelanggan' ),
                '$direction' => '',
                '$sort' => false
            ],
            'total'  =>  [
                'label' =>  __( 'Total' ),
                '$direction' => '',
                '$sort' => false
            ],
            'created_at'  =>  [
                'label' =>  __( 'Dibuat Pada' ),
                '$direction' => '',
                '$sort' => false
            ],
        ];
    }

    public function setActions( $entry, $namespace )
    {
        $entry->{'$checked'} = false;
        $entry->{'$toggled'} = false;
        $entry->{'$id'}      = $entry->id;

        $entry->{'$actions'} = [
            [
                'label'     => __( 'Lanjutkan' ),
                'namespace' => 'ns.open',
                'type'      => 'POPUP',
            ]
        ];

        return $entry;
    }

    public function bulkAction( Request $request ) 
    {
        if ( $request->input( 'action' ) == 'delete_selected' ) {
            if ( $this->permissions['delete'] !== false ) {
                ns()->restrict( $this->permissions['delete'] );
            } else throw new NotAllowedException;

            $status = [ 'success'=>0, 'failed'=>0 ];
            foreach ( $request->input( 'entries' ) as $id ) {
                $entity = $this->model::find( $id );
                if ( $entity instanceof Order ) {
                    $entity->delete();
                    $status['success']++;
                } else {
                    $status['failed']++;
                }
            }
            return $status;
        }
        return Hook::filter( $this->namespace . '-catch-action', false, $request );
    }

    public function getLinks()
    {
        return [
            'list'   => ns()->url( 'dashboard/ns.hold-orders' ),
            'create' => ns()->url( 'dashboard/ns.hold-orders/create' ),
            'edit'   => ns()->url( 'dashboard/ns.hold-orders/edit/' ),
            'post'   => ns()->url( 'dashboard/ns.hold-orders' ),
            'put'    => ns()->url( 'dashboard/ns.hold-orders/' ),
        ];
    }

    public function getBulkActions(){ return []; }
    public function getExports(){ return []; }
}
