<?php
namespace App\Crud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Helper;
use App\Models\User;
use TorMorten\Eventy\Facades\Events as Hook;
use App\Models\CustomerGroup;
use App\Models\RewardSystem;
use Exception;

class CustomerGroupCrud extends CrudService
{
    /**
     * define the base table
     */
    protected $table      =   'nexopos_customers_groups';

    /**
     * base route name
     */
    protected $mainRoute      =   '/dashboard/customers/groups';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.customers-groups';

    /**
     * Model Used
     */
    protected $model      =   CustomerGroup::class;

    /**
     * Adding relation
     */
    public $relations   =  [
        [ 'nexopos_users', 'nexopos_customers_groups.author', '=', 'nexopos_users.id' ],
        'leftJoin'  =>  [
            [ 'nexopos_rewards_system as reward', 'reward.id', '=', 'nexopos_customers_groups.reward_system_id' ]
        ]
    ];

    public $pick    =   [
        'nexopos_users'     =>  [ 'username' ],
        'reward'            =>  [ 'name' ]
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
     * Define Constructor
     * @param  
     */
    public function __construct()
    {
        parent::__construct();

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
    }

    protected $permissions = [
        'create' => 'nexopos.create.customers-groups',
        'read' => 'nexopos.read.customers-groups',
        'update' => 'nexopos.update.customers-groups',
        'delete' => 'nexopos.delete.customers-groups',
    ];

    /**
     * Return the label used for the crud 
     * instance
     * @return  array
    **/
    public function getLabels()
    {
        return [
            'list_title'            =>  __( 'Daftar Grup Pelanggan' ),
            'list_description'      =>  __( 'Menampilkan semua grup pelanggan.' ),
            'no_entry'              =>  __( 'Belum ada grup pelanggan yang terdaftar' ),
            'create_new'            =>  __( 'Tambah Grup Pelanggan Baru' ),
            'create_title'          =>  __( 'Buat Grup Pelanggan Baru' ),
            'create_description'    =>  __( 'Daftarkan grup pelanggan baru dan simpan.' ),
            'edit_title'            =>  __( 'Ubah Grup Pelanggan' ),
            'edit_description'      =>  __( 'Modifikasi grup pelanggan.' ),
            'back_to_list'          =>  __( 'Kembali ke Daftar Grup Pelanggan' ),
        ];
    }

    /**
     * Check whether a feature is enabled
     * @return  boolean
    **/
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
                'description'   =>  __( 'Berikan nama untuk sumber daya ini.' ),
                'validation'    =>  'required'
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'fields'    =>  [
                        // [
                        //     'type'          =>  'select',
                        //     'name'          =>  'reward_system_id',
                        //     'label'         =>  __( 'Sistem Poin Hadiah' ),
                        //     'options'       =>  Helper::toJsOptions(
                        //         RewardSystem::get(), [ 'id', 'name' ]
                        //     ),
                        //     'value'         =>  $entry->reward_system_id ?? '',
                        //     'description'   =>  __( 'Pilih sistem poin hadiah yang berlaku untuk grup ini' )
                        // ], 
                        // [
                        //     'type'          =>  'number',
                        //     'name'          =>  'minimal_credit_payment',
                        //     'label'         =>  __( 'Jumlah Minimum Kredit' ),
                        //     'value'         =>  $entry->minimal_credit_payment ?? '',
                        //     'description'   =>  __( 'Tentukan dalam persen, jumlah minimum pembayaran kredit pertama yang dilakukan oleh semua pelanggan dalam grup, jika menggunakan pesanan kredit. Jika diisi "0", maka tidak ada jumlah minimum kredit yang diperlukan.' )
                        // ],
                        [
                            'type'          =>  'textarea',
                            'name'          =>  'description',
                            'value'         =>  $entry->description ?? '',
                            'description'   =>  __( 'Deskripsi singkat tentang grup ini' ),
                            'label'         =>  __( 'Deskripsi' )
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
        $inputs[ 'minimal_credit_payment' ]   =   $inputs[ 'minimal_credit_payment' ] === null ? 0 : $inputs[ 'minimal_credit_payment' ];
        return $inputs;
    }

    /**
     * Filter PUT input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPutInputs( $inputs, CustomerGroup $entry )
    {
        $inputs[ 'minimal_credit_payment' ]   =   $inputs[ 'minimal_credit_payment' ] === null ? 0 : $inputs[ 'minimal_credit_payment' ];
        return $inputs;
    }

    /**
     * After Crud POST
     * @param  object entry
     * @return  void
     */
    public function afterPost( $inputs )
    {
        return $inputs;
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
     * After Crud PUT
     * @param  object entry
     * @return  void
     */
    public function afterPut( $inputs )
    {
        return $inputs;
    }
    
    /**
     * Protect an access to a specific crud UI
     * @param  array { namespace, id, type }
     * @return  array | throw AccessDeniedException
    **/
    public function canAccess( $fields )
    {
        $users      =   app()->make( Users::class );
        
        if ( $users->is([ 'admin' ]) ) {
            return [
                'status'    =>  'success',
                'message'   =>  __( 'Akses diberikan.' )
            ];
        }

        throw new Exception( __( 'Anda tidak memiliki akses ke sumber daya ini' ) );
    }

    /**
     * Before Delete
     * @return  void
     */
    public function beforeDelete( $namespace, $id ) {
        if ( $namespace == 'ns.customers-groups' ) {
            $this->allowedTo( 'delete' );
        }
    }
    /**
     * Before Delete
     * @return  void
     */
    public function beforePost( $request ) {
        $this->allowedTo( 'create' );
    }
    /**
     * Before Delete
     * @return  void
     */
    public function beforePut( $request, $id ) {
        $this->allowedTo( 'delete' );
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
                '$sort'         =>  false,
            ],
            // 'reward_name'  =>  [
            //     'label'  =>  __( 'Sistem Poin Hadiah' ),
            //     '$direction'    =>  '',
            //     '$sort'         =>  false,
            // ],
            'nexopos_users_username'  =>  [
                'label'  =>  __( 'Pembuat' ),
                '$direction'    =>  '',
                '$sort'         =>  false,
            ],
            'created_at'  =>  [
                'label'  =>  __( 'Dibuat Pada' ),
                '$direction'    =>  '',
                '$sort'         =>  false,
            ],
        ];
    }

    /**
     * Define actions
     */
    public function setActions( $entry, $namespace )
    {
        $entry->reward_system_id   =    $entry->reward_system_id === 0 ? __( 'N/A' ) : $entry->reward_system_id;
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Ubah' ),
                'namespace'     =>      'edit_customers_group',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>     ns()->url( 'dashboard/customers/groups/edit/' . $entry->id )
            ], [
                'label'     =>  __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'index'     =>  'id',
                'url'       => ns()->url( '/api/nexopos/v4/crud/ns.customers-groups/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Apakah Anda yakin ingin menghapus ini?' ),
                    'title'     =>  __( 'Hapus lisensi' )
                ]
            ]
        ];
        $entry->reward_name     =   $entry->reward_name ?: __( 'N/A' );
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        return $entry;
    }

    
    /**
     * Bulk Delete Action
     * @param    object Request with object
     * @return    false/array
     */
    public function bulkAction( Request $request ) 
    {
        /**
         * Menghapus lisensi hanya diizinkan untuk admin
         * dan supervisor.
         */
        $user   =   app()->make( 'App\Services\Users' );

        if ( ! $user->is([ 'admin', 'supervisor' ]) ) {
            return response()->json([
                'status'    =>  'failed',
                'message'   =>  __( 'Anda tidak diizinkan melakukan operasi ini' )
            ], 403 );
        }

        if ( $request->input( 'action' ) == 'delete_selected' ) {
            $status     =   [
                'success'   =>  0,
                'failed'    =>  0
            ];

            foreach ( $request->input( 'entries' ) as $id ) {
                $entity     =   $this->model::find( $id );
                if ( $entity instanceof CustomerGroup ) {
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
            'list'      =>  ns()->url(  'dashboard/customers/groups' ),
            'create'    =>  ns()->url(  'dashboard/customers/groups/create' ),
            'edit'      =>  ns()->url(  'dashboard/customers/groups/edit' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.customers-groups' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.customers-groups/{id}' . '' ),
        ];
    }

    /**
     * Get Bulk actions
     * @return  array of actions
    **/
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

    /**
     * get exports
     * @return  array of export formats
    **/
    public function getExports()
    {
        return [];
    }
}
