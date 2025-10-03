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
use App\Models\Tax;
use App\Models\TaxGroup;
use App\Services\Helper;

class TaxCrud extends CrudService
{
    /**
     * define the base table
     * @param  string
     */
    protected $table      =   'nexopos_taxes';

    /**
     * default slug
     * @param  string
     */
    protected $slug   =   'taxes';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.taxes';

    /**
     * Model Used
     * @param  string
     */
    protected $model      =   Tax::class;

    /**
     * Define permissions
     * @param  array
     */
    protected $permissions  =   [
        'create'    =>  true,
        'read'      =>  true,
        'update'    =>  true,
        'delete'    =>  true,
    ];

    public $relations   =  [
        [ 'nexopos_users as user', 'nexopos_taxes.author', '=', 'user.id' ],
        [ 'nexopos_taxes_groups as parent', 'nexopos_taxes.tax_group_id', '=', 'parent.id' ]
    ];

    /**
     * Pick
     * Restrict columns you retreive from relation.
     * Should be an array of associative keys, where 
     * keys are either the related table or alias name.
     * Example : [
     *      'user'  =>  [ 'username' ], // here the relation on the table nexopos_users is using "user" as an alias
     * ]
     */
    protected $pick     =   [
        'user'      =>  [ 'username' ],
        'parent'    =>  [ 'name' ],
    ];

    /**
     * all tabs mentionned on the tabs relations
     * are ignored on the parent model.
     */
    protected $tabsRelations    =   [
        // 'tab_name'      =>      [ YourRelatedModel::class, 'localkey_on_relatedmodel', 'foreignkey_on_crud_model' ],
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

    /**
     * Return the label used for the crud 
     * instance
     * @return  array
    **/
    public function getLabels()
    {
        return [
            'list_title'            =>  __( 'Daftar Pajak' ),
            'list_description'      =>  __( 'Menampilkan semua pajak.' ),
            'no_entry'              =>  __( 'Belum ada pajak yang terdaftar' ),
            'create_new'            =>  __( 'Tambah Pajak Baru' ),
            'create_title'          =>  __( 'Buat Pajak Baru' ),
            'create_description'    =>  __( 'Daftarkan pajak baru dan simpan.' ),
            'edit_title'            =>  __( 'Ubah Pajak' ),
            'edit_description'      =>  __( 'Modifikasi pajak.' ),
            'back_to_list'          =>  __( 'Kembali ke Daftar Pajak' ),

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
                'label'         =>  __( 'Name' ),
                'name'          =>  'name',
                'value'         =>  $entry->name ?? '',
                'description'   =>  __( 'Provide a name to the tax.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'General' ),
                    'fields'    =>  [
                        [
                            'type'        =>  'select',
                            'options'     =>  Helper::toJsOptions( TaxGroup::get(), [ 'id', 'name' ] ),
                            'name'        =>  'tax_group_id',
                            'label'       =>  __( 'Induk' ),
                            'description' =>  __( 'Tetapkan pajak ini ke dalam grup pajak.' ),
                            'value'       =>  $entry->tax_group_id ?? '',
                        ], [
                            'type'        =>  'text',
                            'name'        =>  'rate',
                            'label'       =>  __( 'Tarif' ),
                            'description' =>  __( 'Tentukan nilai tarif untuk pajak.' ),
                            'value'       =>  $entry->rate ?? '',
                        ], [
                            'type'        =>  'textarea',
                            'name'        =>  'description',
                            'label'       =>  __( 'Deskripsi' ),
                            'description' =>  __( 'Berikan deskripsi untuk pajak ini.' ),
                            'value'       =>  $entry->description ?? '',
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
    public function filterPutInputs( $inputs, Tax $entry )
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
        if ( $this->permissions[ 'create' ] !== false ) {
            ns()->restrict( $this->permissions[ 'create' ] );
        } else {
            throw new NotAllowedException;
        }

        return $request;
    }

    /**
     * After saving a record
     * @param  Request $request
     * @param  Tax $entry
     * @return  void
     */
    public function afterPost( $request, Tax $entry )
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
        if ( $this->permissions[ 'update' ] !== false ) {
            ns()->restrict( $this->permissions[ 'update' ] );
        } else {
            throw new NotAllowedException;
        }

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
     * Before Delete
     * @return  void
     */
    public function beforeDelete( $namespace, $id, $model ) {
        if ( $namespace == 'ns.taxes' ) {
            /**
             *  Perform an action before deleting an entry
             *  In case something wrong, this response can be returned
             *
             *  return response([
             *      'status'    =>  'danger',
             *      'message'   =>  __( 'You\re not allowed to do that.' )
             *  ], 403 );
            **/
            if ( $this->permissions[ 'delete' ] !== false ) {
                ns()->restrict( $this->permissions[ 'delete' ] );
            } else {
                throw new NotAllowedException;
            }
        }
    }

    /**
     * Define Columns
     * @return  array of columns configuration
     */
    public function getColumns() {
        return [
            'name'  =>  [
            'label'      =>  __( 'Nama' ),
            '$direction' =>  '',
            '$sort'      =>  false
        ],
        'parent_name'  =>  [
            'label'      =>  __( 'Induk' ),
            '$direction' =>  '',
            '$sort'      =>  false
        ],
        'rate'  =>  [
            'label'      =>  __( 'Persentase' ),
            '$direction' =>  '',
            '$sort'      =>  false
        ],
        'user_username'  =>  [
            'label'      =>  __( 'Pembuat' ),
            '$direction' =>  '',
            '$sort'      =>  false
        ],
        'created_at'  =>  [
            'label'      =>  __( 'Dibuat Pada' ),
            '$direction' =>  '',
            '$sort'      =>  false
        ],

        ];
    }

    /**
     * Define actions
     */
    public function setActions( $entry, $namespace )
    {
        // Don't overwrite
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        $entry->rate            =   sprintf( '%s%%', $entry->rate );

        // you can make changes here
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Edit' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'url'           =>      ns()->url( '/dashboard/' . 'taxes' . '/edit/' . $entry->id )
            ], [
                'label'     =>  __( 'Delete' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       =>  ns()->url( '/api/nexopos/v4/crud/ns.taxes/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Would you like to delete this ?' ),
                ]
            ]
        ];

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
         * Deleting licence is only allowed for admin
         * and supervisor.
         */

        if ( $request->input( 'action' ) == 'delete_selected' ) {

            /**
             * Will control if the user has the permissoin to do that.
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
                if ( $entity instanceof Tax ) {
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
            'list'      =>  ns()->url( 'dashboard/' . 'taxes' ),
            'create'    =>  ns()->url( 'dashboard/' . 'taxes/create' ),
            'edit'      =>  ns()->url( 'dashboard/' . 'taxes/edit/' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.taxes' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.taxes/{id}' . '' ),
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