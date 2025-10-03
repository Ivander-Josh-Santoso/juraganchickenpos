<?php
namespace App\Crud;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use App\Models\User;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\UnitGroup;

class UnitGroupCrud extends CrudService
{
    /**
     * define the base table
     */
    protected $table       =   'nexopos_units_groups';

    /**
     * base route name
     */
    protected $mainRoute       =   'ns.units-groups';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.units-groups';

    /**
     * Model Used
     */
    protected $model       =   UnitGroup::class;

    /**
     * Adding relation
     */
    public $relations   =  [
        [ 'nexopos_users as user', 'nexopos_units_groups.author', '=', 'user.id' ],
    ];

    public $pick        =   [
        'user'  =>  [ 'username' ],
    ];

    public $permissions     =   [
        'create'    =>  'nexopos.create.products-units',
        'read'      =>  'nexopos.read.products-units',
        'update'    =>  'nexopos.update.products-units',
        'delete'    =>  'nexopos.delete.products-units',
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
     */
    public function __construct()
    {
        parent::__construct();

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
            'list_title'             =>  __( 'Daftar Grup Unit' ),
            'list_description'       =>  __( 'Tampilkan semua grup unit.' ),
            'no_entry'               =>  __( 'Belum ada grup unit yang terdaftar' ),
            'create_new'             =>  __( 'Tambah grup unit baru' ),
            'create_title'           =>  __( 'Buat grup unit baru' ),
            'create_description'     =>  __( 'Daftarkan grup unit baru dan simpan.' ),
            'edit_title'             =>  __( 'Ubah grup unit' ),
            'edit_description'       =>  __( 'Modifikasi Grup Unit.' ),
            'back_to_list'           =>  __( 'Kembali ke Grup Unit' ),
        ];
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
     * @return  array of fields
     */
    public function getForm( $entry = null ) 
    {
        return [
            'main' =>  [
                'label'         =>  __( 'Nama' ),
                'name'          =>  'name',
                'value'         =>  $entry->name ?? '',
                'description'   =>  __( 'Berikan nama untuk resource ini.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'fields'    =>  [
                        [
                            'type'  =>  'textarea',
                            'name'  =>  'description',
                            'value' =>  $entry->description ?? '',
                            'label' =>  __( 'Deskripsi' ),
                        ]
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
    public function filterPutInputs( $inputs, UnitGroup $entry )
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
     * @param  UnitGroup $entry
     * @return  void
     */
    public function afterPost( $request, UnitGroup $entry )
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
        if ( $namespace == 'ns.units-groups' ) {
            $this->allowedTo( 'delete' );
        }
    }

    /**
     * Define Columns
     * @return  array of columns configuration
     */
    public function getColumns() {
        return [
            'name'              =>  [
                'label'         =>  __( 'Nama' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'user_username'  =>  [
                'label'         =>  __( 'Pembuat' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'  =>  [
                'label'         =>  __( 'Tanggal Dibuat' ),
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

        // Anda dapat mengubah di sini
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Ubah' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'units/groups' . '/edit/' . $entry->id )
            ], [
                'label'     =>  __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       =>  ns()->url( '/api/nexopos/v4/crud/ns.units-groups/' . $entry->id ),
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
        /**
         * Menghapus hanya diizinkan untuk admin
         * dan supervisor.
         */
        $user   =   app()->make( Users::class );
        if ( ! $user->is([ 'admin', 'supervisor' ]) ) {
            return response()->json([
                'status'    =>  'failed',
                'message'   =>  __( 'Anda tidak diperbolehkan melakukan operasi ini' )
            ], 403 );
        }

        if ( $request->input( 'action' ) == 'delete_selected' ) {
            $status     =   [
                'success'   =>  0,
                'failed'    =>  0
            ];

            foreach ( $request->input( 'entries' ) as $id ) {
                $entity     =   $this->model::find( $id );
                if ( $entity instanceof UnitGroup ) {
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
            'list'      =>  ns()->url( 'dashboard/' . 'units/groups' ),
            'create'    =>  ns()->url( 'dashboard/' . 'units/groups/create' ),
            'edit'      =>  ns()->url( 'dashboard/' . 'units/groups/edit/' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.units-groups' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.units-groups/{id}' . '' ),
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
