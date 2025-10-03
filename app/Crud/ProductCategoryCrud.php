<?php
namespace App\Crud;

use App\Events\ProductCategoryAfterCreatedEvent;
use App\Events\ProductCategoryAfterUpdatedEvent;
use App\Events\ProductCategoryBeforeDeletedEvent;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\ProductCategory;
use App\Services\Helper;

class ProductCategoryCrud extends CrudService
{
    /**
     * define the base table
     */
    protected $table       =   'nexopos_products_categories';

    /**
     * base route name
     */
    protected $mainRoute       =   'ns.products-categories';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.products-categories';

    /**
     * Model Used
     */
    protected $model       =   ProductCategory::class;

    /**
     * Adding relation
     */
    public $relations   =  [
        [ 'nexopos_users as user', 'nexopos_products_categories.author', '=', 'user.id' ],
        'leftJoin'  =>  [
            [ 'nexopos_products_categories as parent', 'nexopos_products_categories.parent_id', '=', 'parent.id' ]
        ],
    ];

    protected $pick     =   [
        'user'      =>  [ 'username' ],
        'parent'    =>  [ 'name' ],
    ];

    protected $permissions  =   [
        'create'    =>  'nexopos.create.categories',
        'read'      =>  'nexopos.read.categories',
        'update'    =>  'nexopos.update.categories',
        'delete'    =>  'nexopos.delete.categories',
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
            'list_title'             =>  __( 'Daftar Kategori Produk' ),
            'list_description'       =>  __( 'Tampilkan semua kategori produk.' ),
            'no_entry'               =>  __( 'Belum ada kategori produk yang terdaftar' ),
            'create_new'             =>  __( 'Tambah kategori produk baru' ),
            'create_title'           =>  __( 'Buat kategori produk baru' ),
            'create_description'     =>  __( 'Daftarkan kategori produk baru dan simpan.' ),
            'edit_title'             =>  __( 'Edit kategori produk' ),
            'edit_description'       =>  __( 'Modifikasi Kategori Produk.' ),
            'back_to_list'           =>  __( 'Kembali ke Kategori Produk' ),
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
        $parents    =   ProductCategory::where( 'id', '<>', $entry->id ?? 0 )->get();
        $parents->prepend( ( object ) [
            'id'    =>    0,
            'name'  =>  __( 'Tanpa Induk' ),
        ]);

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
                            'type'          =>  'media',
                            'label'         =>  __( 'Pratinjau' ),
                            'name'          =>  'preview_url',
                            'description'   =>  __( 'Berikan url pratinjau pada kategori.' ),
                            'value'         =>  $entry->preview_url ?? '',
                        ], [
                            'type'          =>  'switch',
                            'label'         =>  __( 'Tampil di POS' ),
                            'name'          =>  'displays_on_pos',
                            'description'   =>  __( 'Jika dipilih "Tidak", semua produk yang terkait kategori ini atau sub-kategori, tidak akan muncul di POS.' ),
                            'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
                            'validation'    =>  'required',
                            'value'         =>  $entry->displays_on_pos ?? 1,
                        ], [
                            'type'          =>  'select',
                            'options'       =>  Helper::toJsOptions( $parents, [ 'id', 'name' ]),
                            'name'          =>  'parent_id',
                            'label'         =>  __( 'Induk Kategori' ),
                            'description'   =>  __( 'Jika kategori ini adalah subkategori dari kategori yang sudah ada.' ),
                            'value'         =>  $entry->parent_id ?? '',
                        ], [
                            'type'  =>  'ckeditor',
                            'name'  =>  'description',
                            'label' =>  __( 'Deskripsi' ),
                            'value' =>  $entry->description ?? '',
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
    public function filterPutInputs( $inputs, ProductCategory $entry )
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
     * @param  ProductCategory $entry
     * @return  void
     */
    public function afterPost( $request, ProductCategory $entry )
    {
        ProductCategoryAfterCreatedEvent::dispatch( $entry );

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
        $this->allowedTo( 'delete' );
        return $request;
    }

    /**
     * After updating a record
     * @param  Request $request
     * @param  object entry
     * @return  void
     */
    public function afterPut( $request, ProductCategory $entry )
    {
        /**
         * Jika kategori tidak tampil di POS
         * produk tidak bisa dicari.
         */
        if ( ! (bool) $entry->displays_on_pos ) {
            Product::where( 'category_id', $entry->id )->update([
                'searchable'    =>  false
            ]);
        } else {
            Product::where( 'category_id', $entry->id )->update([
                'searchable'    =>  true
            ]);
        }

        ProductCategoryAfterUpdatedEvent::dispatch( $entry );

        return $request;
    }
    
    /**
     * Protect an access to a specific crud UI
     * @param  array { namespace, id, type }
     * @return  array | throw Exception
     **/
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
        if ( $namespace == 'ns.products-categories' ) {
            $this->allowedTo( 'delete' );

            ProductCategoryBeforeDeletedEvent::dispatch( $model );
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
            'parent_name'  =>  [
                'label'  =>  __( 'Induk Kategori' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'total_items'  =>  [
                'label'  =>  __( 'Total Produk' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'displays_on_pos'  =>  [
                'label'         =>  __( 'Tampil di POS' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'user_username'  =>  [
                'label'  =>  __( 'Pembuat' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'  =>  [
                'label'  =>  __( 'Tanggal Dibuat' ),
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

        $entry->parent_name     =   $entry->parent_name === null ? __( 'Tanpa Induk' ) : $entry->parent_name;
        $entry->displays_on_pos =   (int) $entry->displays_on_pos === 1 ? __( 'Ya' ) : __( 'Tidak' );
        
        // Anda dapat mengubah di sini
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Ubah' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'products/categories' . '/edit/' . $entry->id )
            ], [
                'label'         =>      __( 'Hitung Produk' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'products/categories' . '/compute-products/' . $entry->id )
            ], [
                'label'     =>  __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       => ns()->url( '/api/nexopos/v4/crud/ns.products-categories/' . $entry->id ),
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
                if ( $entity instanceof ProductCategory ) {
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
            'list'      =>  ns()->url( 'dashboard/' . 'products/categories' ),
            'create'    =>  ns()->url( 'dashboard/' . 'products/categories/create' ),
            'edit'      =>  ns()->url( 'dashboard/' . 'products/categories/edit/' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.products-categories' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.products-categories/{id}' . '' ),
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
     **/
    public function getExports()
    {
        return [];
    }
}
