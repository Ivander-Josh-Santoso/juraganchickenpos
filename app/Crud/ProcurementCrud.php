<?php
namespace App\Crud;

use App\Exceptions\NotAllowedException;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\Procurement;
use App\Services\ProviderService;

class ProcurementCrud extends CrudService
{
    /**
     * define the base table
     */
    protected $table       =   'nexopos_procurements';

    /**
     * base route name
     */
    protected $identifier   =   '/procurements';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.procurements';

    /**
     * Model Used
     */
    protected $model       =   Procurement::class;

    /**
     * Adding relation
     */
    public $relations   =  [
        [ 'nexopos_users as users', 'nexopos_procurements.author', '=', 'users.id' ],
        [ 'nexopos_providers as providers', 'nexopos_procurements.provider_id', '=', 'providers.id' ]
    ];

    /**
     * Define where statement
     * @var  array
     **/
    protected $listWhere    =   [];

    /**
     * Define where in statement
     * @var  array
     */
    protected $whereIn      =   [];

    /**
     * Fields which will be filled during post/put
     */
    public $fillable    =   [];

    /**
     * define permission
     */
    public $permissions     =   [
        'create'    =>  'nexopos.create.procurements',
        'read'      =>  'nexopos.read.procurements',
        'update'    =>  false,
        'delete'    =>  'nexopos.delete.procurements',
    ];

    /**
     * @var ProviderService
     */
    protected $providerService;

    /**
     * Define Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->providerService  =   app()->make( ProviderService::class );

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
    }

    /**
     * Return the label used for the crud 
     * instance
     * @return  array
     */
    public function getLabels()
    {
        return [
            'list_title'             =>  __( 'Daftar Pengadaan' ),
            'list_description'       =>  __( 'Tampilkan semua pengadaan.' ),
            'no_entry'               =>  __( 'Belum ada pengadaan yang terdaftar' ),
            'create_new'             =>  __( 'Tambah pengadaan baru' ),
            'create_title'           =>  __( 'Buat pengadaan baru' ),
            'create_description'     =>  __( 'Daftarkan pengadaan baru dan simpan.' ),
            'edit_title'             =>  __( 'Ubah pengadaan' ),
            'edit_description'       =>  __( 'Modifikasi Pengadaan.' ),
            'back_to_list'           =>  __( 'Kembali ke Pengadaan' ),
        ];
    }

    public function hook( $query )
    {
        /**
         * pastikan tidak mengganggu sorting default crud
         */
        if ( ! request()->query( 'active' ) && ! request()->query( 'direction' ) ) {
            $query->orderBy( 'created_at', 'desc' );
        }
    }

    /**
     * Check whether a feature is enabled
     * @return  boolean
     */
    public function isEnabled( $feature )
    {
        return false; // by default
    }

    /**
     * Fields
     * @param  object/null
     * @return  array of field
     */
    public function getForm( $entry = null ) 
    {
        return [
            'main' =>  [
                'label'         =>  __( 'Nama' ),
                'name'          =>  'name',
                'value'         =>  $entry->name ?? '',
                'description'   =>  __( 'Berikan nama pada resource.' )
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
                            'name'  =>  'created_at',
                            'label' =>  __( 'Tanggal Dibuat' ),
                            'value' =>  $entry->created_at ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'description',
                            'label' =>  __( 'Deskripsi' ),
                            'value' =>  $entry->description ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'id',
                            'label' =>  __( 'ID' ),
                            'value' =>  $entry->id ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'name',
                            'label' =>  __( 'Nama' ),
                            'value' =>  $entry->name ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'provider_id',
                            'label' =>  __( 'ID Penyedia' ),
                            'value' =>  $entry->provider_id ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'status',
                            'label' =>  __( 'Status' ),
                            'value' =>  $entry->status ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'total_items',
                            'label' =>  __( 'Total Item' ),
                            'value' =>  $entry->total_items ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'updated_at',
                            'label' =>  __( 'Terakhir Diperbarui' ),
                            'value' =>  $entry->updated_at ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'uuid',
                            'label' =>  __( 'UUID' ),
                            'value' =>  $entry->uuid ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'value',
                            'label' =>  __( 'Nilai' ),
                            'value' =>  $entry->value ?? '',
                        ],             
                    ]
                ]
            ]
        ];
    }

    /**
     * Filter POST input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPostInputs( $inputs )
    {
        return $inputs;
    }

    /**
     * Filter PUT input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPutInputs( $inputs, Procurement $entry )
    {
        return $inputs;
    }

    /**
     * Before saving a record
     * @param  Request $request
     * @return  void
     */
    public function beforePost( $request )
    {
        $this->allowedTo( 'create' );

        return $request;
    }

    /**
     * After saving a record
     * @param  Request $request
     * @param  Procurement $entry
     * @return  void
     */
    public function afterPost( $request, Procurement $entry )
    {
        return $request;
    }

    /**
     * get
     * @param  string
     * @return  mixed
     */
    public function get( $param )
    {
        switch( $param ) {
            case 'model' : return $this->model ; break;
        }
    }

    /**
     * Before updating a record
     * @param  Request $request
     * @param  object entry
     * @return  void
     */
    public function beforePut( $request, $entry )
    {
        $this->allowedTo( 'update' );

        return $request;
    }

    /**
     * After updating a record
     * @param  Request $request
     * @param  object entry
     * @return  void
     */
    public function afterPut( $request, $entry )
    {
        return $request;
    }

    /**
     * Protect an access to a specific crud UI
     * @param  array { namespace, id, type }
     * @return  array | throw Exception
     */
    public function canAccess( $fields )
    {
        $users       =   app()->make( Users::class );
        
        if ( $users->is([ 'admin' ]) ) {
            return [
                'status'    =>  'success',
                'message'   =>  __( 'Akses diberikan.' )
            ];
        }

        throw new Exception( __( 'Anda tidak memiliki akses ke resource ini' ) );
    }

    /**
     * Before Delete
     * @return  void
     */
    public function beforeDelete( $namespace, $id, $model ) {
        if ( $namespace == 'ns.procurements' ) {
            $this->allowedTo( 'delete' );
        }
    }

    /**
     * Define Columns
     * @return  array of columns configuration
     */
    public function getColumns() {
        return [
            'name'  =>  [
                'label'  =>  __( 'Nama' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'providers_name'  =>  [
                'label'  =>  __( 'Penyedia' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'delivery_status'  =>  [
                'label'  =>  __( 'Status Pengiriman' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'payment_status'  =>  [
                'label'  =>  __( 'Status Pembayaran' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'value'  =>  [
                'label'         =>  __( 'Nilai' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'tax_value'  =>  [
                'label'         =>  __( 'Pajak' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'users_username'    =>  [
                'label'         =>  __( 'Pembuat' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'  =>  [
                'label'  =>  __( 'Tanggal' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
        ];
    }

    /**
     * Define actions
     */
    public function setActions( $entry, $namespace )
    {
        // Jangan timpa
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        $entry->delivery_status     =   $this->providerService->getDeliveryStatusLabel( $entry->delivery_status );
        $entry->payment_status      =   $this->providerService->getPaymentStatusLabel( $entry->payment_status );

        $entry->value       =   ns()
            ->currency
            ->define( $entry->value )
            ->format();

        $entry->tax_value   =   ns()
            ->currency
            ->define( $entry->tax_value )
            ->format();

        // Anda dapat mengubah di sini
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Ubah' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'procurements' . '/edit/' . $entry->id )
            ], [
                'label'         =>      __( 'Faktur' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'procurements' . '/edit/' . $entry->id . '/invoice' )
            ], [
                'label'     =>  __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       => ns()->url( '/api/nexopos/v4/crud/ns.procurements/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Apakah Anda ingin menghapus ini?' ),
                ]
            ]
        ];

        return $entry;
    }

    /**
     * Bulk Delete Action
     * @param    object Request with object
     * @return   false/array
     */
    public function bulkAction( Request $request ) 
    {
        if ( $request->input( 'action' ) == 'delete_selected' ) {

            /**
             * Cek apakah user punya izin melakukan aksi ini.
             */
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
                if ( $entity instanceof Procurement ) {
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

    /**
     * get Links
     * @return  array of links
     */
    public function getLinks()
    {
        return  [
            'list'      =>  'procurements',
            'create'    =>  'procurements/create',
            'edit'      =>  'procurements/edit'
        ];
    }

    /**
     * Get Bulk actions
     * @return  array of actions
     */
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

    /**
     * get exports
     * @return  array of export formats
     */
    public function getExports()
    {
        return [];
    }
}
