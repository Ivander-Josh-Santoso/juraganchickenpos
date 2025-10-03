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
use App\Models\Coupon;
use App\Models\CouponCategory;
use App\Models\CouponProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\CustomerService;
use App\Services\Helper;
use Carbon\Carbon;

class CouponCrud extends CrudService
{
    /**
     * define the base table
     * @param  string
     */
    protected $table       =   'nexopos_coupons';

    /**
     * default slug
     * @param  string
     */
    protected $slug   =   'customers/coupons';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.coupons';

    /**
     * Model Used
     * @param  string
     */
    protected $model      =   Coupon::class;

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

    /**
     * Adding relation
     * Example : [ 'nexopos_users as user', 'user.id', '=', 'nexopos_orders.author' ]
     * @param  array
     */
    public $relations   =  [
        [ 'nexopos_users', 'nexopos_coupons.author', '=', 'nexopos_users.id' ],
    ];

    /**
     * all tabs mentionned on the tabs relations
     * are ignored on the parent model.
     */
    protected $tabsRelations    =   [
        // 'tab_name'      =>      [ YourRelatedModel::class, 'localkey_on_relatedmodel', 'foreignkey_on_crud_model' ],
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
    public $pick        =   [];

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
            'list_title'             =>  __( 'Daftar Kupon' ),
            'list_description'       =>  __( 'Menampilkan semua kupon.' ),
            'no_entry'               =>  __( 'Belum ada kupon yang terdaftar' ),
            'create_new'             =>  __( 'Tambah kupon baru' ),
            'create_title'           =>  __( 'Buat kupon baru' ),
            'create_description'     =>  __( 'Daftarkan kupon baru dan simpan.' ),
            'edit_title'             =>  __( 'Edit kupon' ),
            'edit_description'       =>  __( 'Ubah Kupon.' ),
            'back_to_list'           =>  __( 'Kembali ke Kupon' ),
        ];
    }

    /**
     * Check whether a feature is enabled
     * @return  boolean
     **/
    public function isEnabled( $feature )
    {
        return false; // secara default
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
                'validation'    =>  'required',
                'description'   =>  __( 'Berikan nama pada sumber daya.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'active'    =>  false,
                    'fields'    =>  [
                        [
                            'type'  =>  'text',
                            'name'  =>  'code',
                            'label' =>  __( 'Kode Kupon' ),
                            'validation'    =>  'required',
                            'description'   =>  __( 'Kode ini mungkin digunakan saat mencetak kupon.' ),
                            'value' =>  $entry->code ?? '',
                        ], [
                            'type'  =>  'select',
                            'name'  =>  'type',
                            'validation'    =>  'required',
                            'options'   =>  Helper::kvToJsOptions([
                                'percentage_discount'   =>  __( 'Diskon Persentase' ),
                                'flat_discount'         =>  __( 'Diskon Tetap' ),
                            ]),
                            'label' =>  __( 'Tipe' ),
                            'value' =>  $entry->type ?? '',
                            'description'   =>  __( 'Tentukan tipe diskon yang berlaku untuk kupon ini.' )
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'discount_value',
                            'label' =>  __( 'Nilai Diskon' ),
                            'description'   =>  __( 'Tentukan nilai persentase atau nilai tetap.' ),
                            'value' =>  $entry->discount_value ?? '',
                        ], [
                            'type'  =>  'datetime',
                            'name'  =>  'valid_until',
                            'label' =>  __( 'Berlaku Hingga' ),
                            'description'   =>  __( 'Tentukan sampai kapan kupon berlaku.' ),
                            'value' =>  $entry->valid_until ?? '',
                        ], [
                            'type'  =>  'number',
                            'name'  =>  'minimum_cart_value',
                            'label' =>  __( 'Nilai Minimum Keranjang' ),
                            'description'   =>  __( 'Berapa nilai minimum keranjang agar kupon ini valid.' ),
                            'value' =>  $entry->minimum_cart_value ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'maximum_cart_value',
                            'label' =>  __( 'Nilai Maksimum Keranjang' ),
                            'description'   =>  __( 'Nilai di atas ini kupon tidak dapat diterapkan.' ),
                            'value' =>  $entry->maximum_cart_value ?? '',
                        ], [
                            'type'  =>  'datetimepicker',
                            'name'  =>  'valid_hours_start',
                            'label' =>  __( 'Jam Berlaku Mulai' ),
                            'description'   =>  __( 'Tentukan jam mulai berlaku kupon setiap hari.' ),
                            'value' =>  $entry->valid_hours_start ?? '',
                        ], [
                            'type'  =>  'datetimepicker',
                            'name'  =>  'valid_hours_end',
                            'label' =>  __( 'Jam Berlaku Berakhir' ),
                            'description'   =>  __( 'Tentukan jam berakhirnya kupon setiap hari.' ),
                            'value' =>  $entry->valid_hours_end ?? '',
                        ], [
                            'type'  =>  'number',
                            'name'  =>  'limit_usage',
                            'label' =>  __( 'Batas Penggunaan' ),
                            'description'   =>  __( 'Tentukan berapa kali kupon dapat digunakan.' ),
                            'value' =>  $entry->limit_usage ?? '',
                        ], 
                    ]
                ],
                'selected_products'  =>  [
                    'label' =>  __( 'Produk' ),
                    'active'    =>  true,
                    'fields'    =>  [
                        [
                            'type'  =>  'multiselect',
                            'name'  =>  'products',
                            'options'   =>  Helper::toJsOptions( Product::get(), [ 'id', 'name' ]),
                            'label'     =>  __( 'Pilih Produk' ),
                            'value'     =>  $entry instanceof Coupon ? $entry->products->map( fn( $product ) => $product->product_id )->toArray() : [],
                            'description'   =>  __( 'Produk berikut harus ada di keranjang agar kupon ini valid.' )
                        ], 
                    ]
                ], 
                'selected_categories'  =>  [
                    'label' =>  __( 'Kategori' ),
                    'active'    =>  false,
                    'fields'    =>  [
                        [
                            'type'  =>  'multiselect',
                            'name'  =>  'categories',
                            'options'   =>  Helper::toJsOptions( ProductCategory::get(), [ 'id', 'name' ]),
                            'label'     =>  __( 'Pilih Kategori' ),
                            'value'         =>  $entry instanceof Coupon ? $entry->categories->map( fn( $category ) => $category->category_id )->toArray() : [],
                            'description'   =>  __( 'Produk yang termasuk dalam salah satu kategori ini harus ada di keranjang agar kupon ini valid.' )
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
        $inputs     =   collect( $inputs )->map( function( $field, $key ) {
            if ( ( in_array( $key, [ 
                'minimum_cart_value',
                'maximum_cart_value',
                'assigned',
                'limit_usage',
            ]) && empty( $field ) ) || is_array( $field ) ) {
                return ! is_array( $field ) ? ( $field ?: 0 ) : $field;
            }

            return $field;
        });

        $inputs     =   collect( $inputs )->filter( function( $field, $key ) {
            if ( ( in_array( $key, [ 
                'minimum_cart_value',
                'maximum_cart_value',
                'assigned',
                'limit_usage',
            ]) && empty( $field ) && $field === null ) || is_array( $field ) ) {
                return false;
            }
            return true;
        });

        if ( ! empty( $inputs[ 'valid_hours_end' ] ) ) {
            $inputs[ 'valid_hours_end' ]     =   Carbon::parse( $inputs[ 'valid_hours_end' ] )->toDateTimeString();
        }

        if ( ! empty( $inputs[ 'valid_hours_start' ] ) ) {
            $inputs[ 'valid_hours_start' ]   =   Carbon::parse( $inputs[ 'valid_hours_start' ] )->toDateTimeString();
        }

        return $inputs;
    }

    /**
     * Filter PUT input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPutInputs( $inputs, Coupon $entry )
    {
        $inputs     =   collect( $inputs )->map( function( $field, $key ) {
            if ( ( in_array( $key, [ 
                'minimum_cart_value',
                'maximum_cart_value',
                'assigned',
                'limit_usage',
            ]) && empty( $field ) ) || is_array( $field ) ) {
                return ! is_array( $field ) ? ( $field ?: 0 ) : $field;
            }

            return $field;
        });

        $inputs     =   collect( $inputs )->filter( function( $field, $key ) {
            if ( ( in_array( $key, [ 
                'minimum_cart_value',
                'maximum_cart_value',
                'assigned',
                'limit_usage',
            ]) && empty( $field ) && $field === null ) || is_array( $field ) ) {
                return false;
            }
            return true;
        });

        if ( ! empty( $inputs[ 'valid_hours_end' ] ) ) {
            $inputs[ 'valid_hours_end' ]     =   Carbon::parse( $inputs[ 'valid_hours_end' ] )->toDateTimeString();
        }

        if ( ! empty( $inputs[ 'valid_hours_start' ] ) ) {
            $inputs[ 'valid_hours_start' ]   =   Carbon::parse( $inputs[ 'valid_hours_start' ] )->toDateTimeString();
        }

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

            foreach( $request->input( 'selected_products.products' ) as $product_id ) {
                $product    =   Product::find( $product_id );
                if ( ! $product instanceof Product ) {
                    throw new Exception( __( 'Tidak dapat menyimpan produk kupon karena produk ini tidak ada.' ) );
                }
            }
    
            foreach( $request->input( 'selected_categories.categories' ) as $category_id ) {
                $category    =   ProductCategory::find( $category_id );
                if ( ! $category instanceof ProductCategory ) {
                    throw new Exception( __( 'Tidak dapat menyimpan kategori kupon karena kategori ini tidak ada.' ) );
                }
            }

        } else {
            throw new NotAllowedException;
        }

        return $request;
    }

    /**
     * After saving a record
     * @param  Request $request
     * @param  Coupon $entry
     * @return  void
     */
    public function afterPost( $request, Coupon $coupon )
    {
        foreach( $request->input( 'selected_products.products' ) as $product_id ) {
            $productRelation                =   new CouponProduct();
            $productRelation->coupon_id     =   $coupon->id;
            $productRelation->product_id    =   $product_id;
            $productRelation->save();
        }

        foreach( $request->input( 'selected_categories.categories' ) as $category_id ) {
            $categoryRelation               =   new CouponCategory();
            $categoryRelation->coupon_id    =   $coupon->id;
            $categoryRelation->category_id  =   $category_id;
            $categoryRelation->save();
        }

        /**
         * @var CustomerService
         */
        $customersService   =   app()->make( CustomerService::class );
        $customersService->setCoupon( $request->all(), $coupon );
        
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

            foreach( $request->input( 'selected_products.products' ) as $product_id ) {
                $product    =   Product::find( $product_id );
                if ( ! $product instanceof Product ) {
                    throw new Exception( __( 'Tidak dapat menyimpan produk kupon karena produk ini tidak ada.' ) );
                }
            }
    
            foreach( $request->input( 'selected_categories.categories' ) as $category_id ) {
                $category    =   ProductCategory::find( $category_id );
                if ( ! $category instanceof ProductCategory ) {
                    throw new Exception( __( 'Tidak dapat menyimpan kategori kupon karena kategori ini tidak ada.' ) );
                }
            }
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
    public function afterPut( $request, $coupon )
    {
        $coupon->categories->each( function( $category ) use ( $request ) {
            if ( ! in_array( $category->category_id, $request->input( 'selected_categories.categories' ) ) ) {
                $category->delete();
            }
        });

        $coupon->products->each( function( $product ) use ( $request ) {
            if ( ! in_array( $product->product_id, $request->input( 'selected_products.products' ) ) ) {
                $product->delete();
            }
        });

        foreach( $request->input( 'selected_products.products' ) as $product_id ) {
            $productRelation                    =   CouponProduct::where( 'coupon_id', $coupon->id )
                ->where( 'product_id', $product_id )
                ->first();

            if ( ! $productRelation instanceof CouponProduct ) {
                $productRelation                =   new CouponProduct;
            }

            $productRelation->coupon_id     =   $coupon->id;
            $productRelation->product_id    =   $product_id;
            $productRelation->save();
        }

        foreach( $request->input( 'selected_categories.categories' ) as $category_id ) {
            $categoryRelation                  =   CouponCategory::where( 'coupon_id', $coupon->id )
                ->where( 'category_id', $category_id )
                ->first();

            if ( ! $categoryRelation instanceof CouponCategory ) {
                $categoryRelation              =   new CouponCategory;
            }

            $categoryRelation->coupon_id     =   $coupon->id;
            $categoryRelation->category_id   =   $category_id;
            $categoryRelation->save();
        }

        /**
         * @var CustomerService
         */
        $customersService   =   app()->make( CustomerService::class );
        $customersService->setCoupon( $request->all(), $coupon );
        
        return $request;
    }

    /**
     * Before Delete
     * @return  void
     */
    public function beforeDelete( $namespace, $id, $coupon ) {
        ns()->restrict( $this->permissions[ 'delete' ] );

        if ($namespace == 'ns.coupons') {
            /**
             * @var CustomerService
             */
            $customerService    =   app()->make( CustomerService::class );
            $customerService->deleteRelatedCustomerCoupon( $coupon );

            $coupon->categories()->delete();
            $coupon->products()->delete();
        }
    }

    /**
     * Define Columns
     * @return  array of columns configuration
     */
    public function getColumns() {
        return [
            'name'  =>  [
                'label'         =>  __('Nama'),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'type'              =>  [
                'label'         =>  __('Tipe'),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'discount_value'  =>  [
                'label'         =>  __('Nilai Diskon'),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'valid_hours_start'  =>  [
                'label'         =>  __('Berlaku Dari'),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'valid_hours_end'  =>  [
                'label'         =>  __('Berlaku Hingga'),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'nexopos_users_username'         =>  [
                'label'         =>  __('Pembuat'),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'     =>  [
                'label'         =>  __('Tanggal Dibuat'),
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
        // Don't overwrite
        $entry->{'$checked'}  =   false;
        $entry->{'$toggled'}  =   false;
        $entry->{'$id'}       =   $entry->id;

        switch ($entry->type) {
            case 'percentage_discount':
                $entry->type = __('Diskon Persentase');
                $entry->discount_value      =   $entry->discount_value . '%';
                break;
            case 'flat_discount':
                $entry->type                =   __('Diskon Tetap');
                $entry->discount_value      =   (string) ns()->currency->define( $entry->discount_value );
                break;
            default:
                $entry->type = __('N/A');
                break;
        }

        $entry->valid_until     =   $entry->valid_until ?? __('Tidak Terdefinisi');

        // you can make changes here
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __('Ubah'),
                'namespace'     =>      'edit.licence',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url('/dashboard/customers/coupons/edit/' . $entry->id)
            ], [
                'label'     =>  __('Hapus'),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'index'     =>  'id',
                'url'       => ns()->url('/api/nexopos/v4/crud/ns.coupons/' . $entry->id),
                'confirm'   =>  [
                    'message'  =>  __('Apakah Anda yakin ingin menghapus ini?'),
                    'title'     =>  __('Hapus sebuah kupon')
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
         * Menghapus lisensi hanya diperbolehkan untuk admin
         * dan supervisor.
         */

        if ( $request->input( 'action' ) == 'delete_selected' ) {

            /**
             * Akan mengontrol jika user memiliki izin untuk melakukannya.
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
                if ( $entity instanceof Coupon ) {
                    $this->beforeDelete( $this->namespace, null, $entity );
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
            'list'      =>  ns()->url( 'dashboard/' . 'customers/coupons' ),
            'create'    =>  ns()->url( 'dashboard/' . 'customers/coupons/create' ),
            'edit'      =>  ns()->url( 'dashboard/' . 'customers/coupons/edit/' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.customers-coupons' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.customers-coupons/{id}' . '' ),
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
                'label'         =>  __('Hapus Grup Terpilih'),
                'identifier'    =>  'delete_selected',
                'url'           =>  ns()->route( 'ns.api.crud-bulk-actions', [
                    'namespace' =>  $this->namespace
                ])
            ]
        ]);
    }

    public function hook( $query )
    {
        $query->orderBy( 'created_at', 'desc' );
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
