<?php
namespace App\Crud;

use App\Events\ProductBeforeDeleteEvent;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnitQuantity;
use App\Models\TaxGroup;
use App\Models\UnitGroup;
use App\Services\Helper;
use App\Services\TaxService;

class ProductCrud extends CrudService
{
    /**
     * define the base table
     */
    protected $table       =   'nexopos_products';

    /**
     * base route name
     */
    protected $mainRoute       =   'ns.products';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.products';

    /**
     * Model Used
     */
    protected $model       =   Product::class;

    /**
     * Adding relation
     */
    public $relations   =  [
        [ 'nexopos_users as user', 'nexopos_products.author', '=', 'user.id' ],
        [ 'nexopos_products_categories as category', 'nexopos_products.category_id', '=', 'category.id' ],
        'leftJoin'  =>  [
            [ 'nexopos_products as parent', 'nexopos_products.parent_id', '=', 'parent.id' ],
            [ 'nexopos_taxes_groups as taxes_groups', 'nexopos_products.tax_group_id', '=', 'taxes_groups.id' ],
        ],
    ];

    protected $pick     =   [
        'parent'    =>  [ 'name' ],
        'user'      =>  [ 'username' ],
        'category'  =>  [ 'name' ],
    ];

    /**
     * Define permissions
     * @param  array
     */
    protected $permissions  =   [
        'create'    =>  'nexopos.create.products',
        'read'      =>  'nexopos.read.products',
        'update'    =>  'nexopos.update.products',
        'delete'    =>  'nexopos.delete.products',
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
     * protected tax service
     * @param TaxService
     */
    protected $taxService;

    /**
     * Define Constructor
     * @param   
     */
    public function __construct()
    {
        parent::__construct();

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );

        $this->taxService       =   app()->make( TaxService::class );
    }

    /**
     * Return the label used for the crud 
     * instance
     * @return  array
     **/
    public function getLabels()
    {
        return [
            'list_title'             =>  __( 'Daftar Produk' ),
            'list_description'       =>  __( 'Tampilkan semua produk.' ),
            'no_entry'               =>  __( 'Belum ada produk yang terdaftar' ),
            'create_new'             =>  __( 'Tambah produk baru' ),
            'create_title'           =>  __( 'Buat produk baru' ),
            'create_description'     =>  __( 'Daftarkan produk baru dan simpan.' ),
            'edit_title'             =>  __( 'Edit produk' ),
            'edit_description'       =>  __( 'Modifikasi produk.' ),
            'back_to_list'           =>  __( 'Kembali ke Produk' ),
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
        if ( $entry instanceof Product ) {
            $unitGroup      =   UnitGroup::where( 'id', $entry->unit_group )->with( 'units' )->first() ?? [];
            $units          =   UnitGroup::find( $entry->unit_group )->units;
        } else {
            $unitGroup      =   null;
            $units          =   [];
        }

        $groups             =   UnitGroup::get();
        $fields             =   [
            [
                'type'          =>  'select',
                'errors'        =>  [],
                'name'          =>  'unit_id',
                'options'       =>  Helper::toJsOptions( $units, [ 'id', 'name' ] ),
                'label'         =>  __( 'Unit Penjualan' ),
                'description'   =>  __( 'Unit yang ditetapkan untuk penjualan' ),
                'validation'    =>  'required',
            ], [
                'type'  =>  'number',
                'errors'        =>  [],
                'name'  =>  'sale_price_edit',
                'label' =>  __( 'Harga Jual' ),
                'description'   =>  __( 'Tentukan harga jual reguler.' ),
                'validation'    =>  'required',
            ], [
                'type'  =>  'number',
                'errors'        =>  [],
                'name'  =>  'low_quantity',
                'label' =>  __( 'Jumlah Stok Rendah' ),
                'description'   =>  __( 'Jumlah berapa yang dianggap stok rendah.' ),
            ], [
                'type'  =>  'switch',
                'errors'        =>  [],
                'name'  =>  'stock_alert_enabled',
                'label' =>  __( 'Peringatan Stok' ),
                'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
                'description'   =>  __( 'Tentukan apakah peringatan stok harus diaktifkan untuk unit ini.' ),
            ], [
                'type'          =>  'number',
                'errors'        =>  [],
                'name'          =>  'wholesale_price_edit',
                'label'         =>  __( 'Harga Grosir' ),
                'description'   =>  __( 'Tentukan harga grosir.' ),
                'validation'    =>  'required',
            ], [
                'type'          =>  'media',
                'errors'        =>  [],
                'name'          =>  'preview_url',
                'label'         =>  __( 'Url Pratinjau' ),
                'description'   =>  __( 'Berikan pratinjau unit saat ini.' ),
            ], [
                'type'          =>  'hidden',
                'errors'        =>  [],
                'name'          =>  'id',
            ], [
                'type'          =>  'hidden',
                'errors'        =>  [],
                'name'          =>  'quantity',
            ]
        ];

        return Hook::filter( 'ns-products-crud-form', [
            'main' =>  [
                'label'         =>  __( 'Nama' ),
                'name'          =>  'name',
                'value'         =>  $entry->name ?? '',
                'validation'    =>  'required',
                'description'   =>  __( 'Berikan nama pada resource.' )
            ],
            'variations'     =>  [
                [
                    'id'    =>  $entry->id ?? '',
                    'tabs'  =>  [
                        'identification'   =>  [
                            'label'     =>  __( 'Identifikasi' ),
                            'fields'    =>  [
                                [
                                    'type'  =>  'text',
                                    'name'  =>  'name',
                                    'description'   =>  __( 'Nama unik produk. Jika ini varian, nama relevan untuk varian tersebut.' ),
                                    'label' =>  __( 'Nama' ),
                                    'validation'    =>  'required',
                                    'value' =>  $entry->name ?? '',
                                ], [
                                    'type'  =>  'text',
                                    'name'  =>  'barcode',
                                    'description'   =>  __( 'Tentukan nilai barcode. Fokuskan kursor terlebih dahulu sebelum memindai produk.' ),
                                    'label' =>  __( 'Barcode' ),
                                    'validation'    =>  'required',
                                    'value' =>  $entry->barcode ?? '',
                                ], [
                                    'type'  =>  'select',
                                    'description'   =>  __( 'Tentukan tipe barcode yang dipindai.' ),
                                    'options'   =>  Helper::kvToJsOptions([
                                        'ean8'      =>  __( 'EAN 8' ),
                                        'ean13'     =>  __( 'EAN 13' ),
                                        'codabar'   =>  __( 'Codabar' ),
                                        'code128'   =>  __( 'Code 128' ),
                                        'code39'    =>  __( 'Code 39' ),
                                        'code11'    =>  __( 'Code 11' ),
                                        'upca'      =>  __( 'UPC A' ),
                                        'upce'      =>  __( 'UPC E' ),
                                    ]),
                                    'name'  =>  'barcode_type',
                                    'label' =>  __( 'Tipe Barcode' ),
                                    'validation'    =>  'required',
                                    'value' =>  $entry->barcode_type ?? 'ean8',
                                ], [
                                    'type'  =>  'switch',
                                    'description'   =>  __( 'Tentukan apakah produk dapat dicari di POS.' ),
                                    'options'   =>  Helper::kvToJsOptions([
                                        true      =>  __( 'Ya' ),
                                        false     =>  __( 'Tidak' ),
                                    ]),
                                    'name'  =>  'searchable',
                                    'label' =>  __( 'Dapat Dicari' ),
                                    'value' =>  $entry->searchable ?? true,
                                ], [
                                    'type'          =>  'select',
                                    'description'   =>  __( 'Pilih kategori mana yang ditetapkan pada item.' ),
                                    'options'       =>  Helper::toJsOptions( ProductCategory::get(), [ 'id', 'name' ]),
                                    'name'          =>  'category_id',
                                    'label'         =>  __( 'Kategori' ),
                                    'validation'    =>  'required',
                                    'value'         =>  $entry->category_id ?? '',
                                ], [
                                    'type'          =>  'select',
                                    'options'       =>  Helper::kvToJsOptions( Hook::filter( 'ns-products-type', [
                                        'materialized'      =>  __( 'Produk Materialized' ),
                                        'dematerialized'    =>  __( 'Produk Dematerialized' ),
                                    ] ) ),
                                    'description'   =>  __( 'Tentukan tipe produk. Berlaku untuk semua variasi.' ),
                                    'name'          =>  'type',
                                    'validation'    =>  'required',
                                    'label'         =>  __( 'Tipe Produk' ),
                                    'value'         =>  $entry->type ?? 'materialized',
                                ], [
                                    'type'  =>  'text',
                                    'name'  =>  'sku',
                                    'description'   =>  __( 'Tentukan nilai SKU unik untuk produk.' ),
                                    'label' =>  __( 'SKU' ),
                                    'validation'    =>  'required',
                                    'value' =>  $entry->sku ?? '',
                                ], [
                                    'type'  =>  'select',
                                    'options'   =>  Helper::kvToJsOptions([
                                        'available'     =>  __( 'Tersedia' ),
                                        'unavailable'   =>  __( 'Tersembunyi' ),
                                    ]),
                                    'description'   =>  __( 'Tentukan apakah produk tersedia untuk dijual.' ),
                                    'name'  =>  'status',
                                    'validation'    =>  'required',
                                    'label' =>  __( 'Status' ),
                                    'value' =>  $entry->status ?? 'available',
                                ], [
                                    'type'      =>  'switch',
                                    'options'   =>  Helper::kvToJsOptions([
                                        'enabled'   =>  __( 'Ya' ),
                                        'disabled'  =>  __( 'Tidak' ),
                                    ]),
                                    'description'   =>  __( 'Aktifkan pengelolaan stok pada produk. Tidak berlaku untuk jasa atau produk yang tidak terhitung.' ),
                                    'name'  =>  'stock_management',
                                    'label' =>  __( 'Manajemen Stok Aktif' ),
                                    'validation'    =>  'required',
                                    'value' =>  $entry->stock_management ?? 'enabled',
                                ], [
                                    'type'  =>  'textarea',
                                    'name'  =>  'description',
                                    'label' =>  __( 'Deskripsi' ),
                                    'value' =>  $entry->description ?? '',
                                ],                  
                            ]
                        ],
                        'units'     =>  [
                            'label' =>  __( 'Unit' ),
                            'fields'    =>  [
                                [
                                    'type'  =>  'switch',
                                    'description'   =>  __( 'Produk tidak akan terlihat di grid dan hanya dapat ditemukan menggunakan pembaca barcode atau barcode terkait.' ),
                                    'options'   =>  Helper::kvToJsOptions([
                                        true      =>  __( 'Ya' ),
                                        false     =>  __( 'Tidak' ),
                                    ]),
                                    'name'  =>  'accurate_tracking',
                                    'label' =>  __( 'Pelacakan Akurat' ),
                                    'value' =>  $entry->accurate_tracking ?? false,
                                ], [
                                    'type'          =>  'select',
                                    'options'       =>  Helper::toJsOptions( $groups, [ 'id', 'name' ] ),
                                    'name'          =>  'unit_group',
                                    'description'   =>  __( 'Grup unit yang berlaku untuk item ini. Grup ini akan diterapkan selama pemesanan.' ),
                                    'label'         =>  __( 'Grup Unit' ),
                                    'validation'    =>  'required',
                                    'value'         =>  $entry->unit_group ?? '',
                                ], [
                                    'type'          =>  'group',
                                    'name'          =>  'selling_group',
                                    'description'   =>  __( 'Tentukan unit untuk penjualan.' ),
                                    'label'         =>  __( 'Unit Penjualan' ),
                                    'fields'        =>  $fields,   
                                    
                                    /**
                                     * We make sure to popular the unit quantity
                                     * with the entry values using the fields array. 
                                     */
                                    'groups'        =>  ( $entry instanceof Product ? ProductUnitQuantity::withProduct( $entry->id )
                                            ->get()
                                            ->map( function( $productUnitQuantity ) use ( $fields ) {
                                                return collect( $fields )->map( function( $field ) use ( $productUnitQuantity ) {
                                                    $field[ 'value' ]   =   $productUnitQuantity->{ $field[ 'name' ] };
                                                    return $field;
                                                });
                                            })  : [] ),
                                    'options'       =>  $entry instanceof Product ? UnitGroup::find( $entry->unit_group )->units : [],
                                ]
                            ]
                        ],
                        'expiracy'      =>  [
                            'label'     =>  __( 'Kedaluwarsa' ),
                            'fields'    =>  [
                                [
                                    'type'          =>  'switch',
                                    'name'          =>  'expires',
                                    'validation'    =>  'required',
                                    'label'         =>  __( 'Produk Kadaluarsa' ),
                                    'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
                                    'description'   =>  __( 'Setel ke "Tidak" waktu kedaluwarsa akan diabaikan.' ),
                                    'value'         =>  ( $entry !== null && $entry->expires ? 1 : 0 ),
                                ], [
                                    'type'              =>  'select',
                                    'options'           =>  Helper::kvToJsOptions([
                                        'prevent_sales' =>  __( 'Cegah Penjualan' ),
                                        'allow_sales'   =>  __( 'Izinkan Penjualan' ),
                                    ]),
                                    'description'       =>  __( 'Tentukan tindakan saat produk sudah kedaluwarsa.' ),
                                    'name'              =>  'on_expiration',
                                    'label'             =>  __( 'Saat Kedaluwarsa' ),
                                    'value'             =>  $entry->on_expiration ?? 'prevent-sales',
                                ]
                            ]
                        ],
                        'taxes'     =>  [
                            'label' =>  __( 'Pajak' ),
                            'fields'    =>  [
                                [
                                    'type'  =>  'select',
                                    'options'   =>  Helper::toJsOptions( TaxGroup::get(), [ 'id', 'name' ]),
                                    'description'   =>  __( 'Pilih grup pajak yang berlaku untuk produk/variasi.' ),
                                    'name'  =>  'tax_group_id',
                                    'label' =>  __( 'Grup Pajak' ),
                                    'value' =>  $entry->tax_group_id ?? '',
                                ], [
                                    'type'  =>  'select',
                                    'options'   =>  Helper::kvToJsOptions([
                                        'inclusive' =>  __( 'Termasuk' ),
                                        'exclusive' =>  __( 'Tidak Termasuk' ),
                                    ]),
                                    'description'   =>  __( 'Tentukan tipe pajak.' ),
                                    'name'  =>  'tax_type',
                                    'label' =>  __( 'Tipe Pajak' ),
                                    'value' =>  $entry->tax_type ?? 'inclusive',
                                ], 
                            ]
                        ],
                        'images'     =>  [
                            'label'     =>  __( 'Gambar' ),
                            'fields'    =>  [
                                [
                                    'type'  =>  'media',
                                    'name'  =>  'image',
                                    'label' =>  __( 'Gambar' ),
                                    'description'   =>  __( 'Pilih gambar untuk ditambahkan pada galeri produk' ),
                                ], [
                                    'type'          =>  'switch',
                                    'name'          =>  'primary',
                                    'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
                                    'label'         =>  __( 'Gambar Utama' ),
                                    'description'   =>  __( 'Tentukan apakah gambar ini menjadi utama. Jika lebih dari satu utama, sistem akan memilih salah satu.' ),
                                ]
                            ],
                            'groups'    =>  $entry ? $entry->galleries->map( function( $gallery ) {
                                return [
                                    [
                                        'type'          =>  'media',
                                        'name'          =>  'image',
                                        'label'         =>  __( 'Gambar' ),
                                        'value'         =>  $gallery->url,
                                        'description'   =>  __( 'Pilih gambar untuk ditambahkan pada galeri produk' ),
                                    ], [
                                        'type'          =>  'switch',
                                        'name'          =>  'primary',
                                        'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
                                        'label'         =>  __( 'Gambar Utama' ),
                                        'value'         =>  ( int ) $gallery->featured,
                                        'description'   =>  __( 'Tentukan apakah gambar ini menjadi utama. Jika lebih dari satu utama, sistem akan memilih salah satu.' ),
                                    ]
                                ];
                            }) : [],
                        ]
                    ]
                ]
            ]
        ], $entry );
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
    public function filterPutInputs( $inputs, Product $entry )
    {
        return $inputs;
    }

    /**
     * Will only calculate taxes
     * @param array $fields
     * @return array $fields
     * @deprecated
     */
    private function calculateTaxes( $inputs, Product $product = null )
    {
        $inputs[ 'incl_tax_sale_price' ]        =   $inputs[ 'sale_price_edit' ];
        $inputs[ 'excl_tax_sale_price' ]        =   $inputs[ 'sale_price_edit' ];
        $inputs[ 'sale_price' ]                  =   $inputs[ 'sale_price_edit' ];

        $inputs[ 'incl_tax_wholesale_price' ]   =   $inputs[ 'wholesale_price_edit' ];
        $inputs[ 'excl_tax_wholesale_price' ]   =   $inputs[ 'wholesale_price_edit' ];
        $inputs[ 'wholesale_price' ]             =   $inputs[ 'wholesale_price_edit' ];

        $this->taxService->computeTax( $product, $inputs[ 'tax_id' ] );

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
     * @param  Product $entry
     * @return  void
     */
    public function afterPost( $request, Product $entry )
    {
        // $this->calculateTaxes( $request->all(), $entry );

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
    public function afterPut( $request, Product $product )
    {
        /**
         * delete all assigned taxes as it 
         * be newly assigned
         */
        if ( $product instanceof Product ) {
            $product->taxes()->delete();
        }

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
        if ( $namespace == 'ns.products' ) {
            $this->allowedTo( 'delete' );
        }

        ProductBeforeDeleteEvent::dispatch( $model );

        $this->deleteProductAttachedRelation( $model );
    }

    public function deleteProductAttachedRelation( $model )
    {
        $model->galleries->each( fn( $gallery ) => $gallery->delete() );
        $model->variations->each( fn( $variation ) => $variation->delete() );
        $model->product_taxes->each( fn( $product_taxes ) => $product_taxes->delete() );
        $model->unit_quantities->each( fn( $unitQuantity ) => $unitQuantity->delete() );
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
                'width'         =>  '150px',
                '$sort'         =>  false
            ],
            'sku'               =>  [
                'label'         =>  __( 'Sku' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'category_name'  =>  [
                'label'  =>  __( 'Kategori' ),
                'width'         =>  '150px',
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'type'  =>  [
                'label'         =>  __( 'Tipe' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'status'  =>  [
                'label'         =>  __( 'Status' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'user_username'  =>  [
                'label'         =>  __( 'Pembuat' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'  =>  [
                'label'         =>  __( 'Tanggal' ),
                'width'         =>  '150px',
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
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        $entry->type                 =   $entry->type === 'materialized' ? __( 'Materialized' ) : __( 'Dematerialized' );
        $entry->stock_management     =   $entry->stock_management === 'enabled' ? __( 'Aktif' ) : __( 'Nonaktif' );
        $entry->status               =   $entry->status === 'available' ? __( 'Tersedia' ) : __( 'Tersembunyi' );
        // you can make changes here
        $entry->{'$actions'}    =   [
            [
                'label'         =>      '<i class="mr-2 las la-edit"></i> ' . __( 'Ubah' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'products' . '/edit/' . $entry->id )
            ], [
                'label'         =>      '<i class="mr-2 las la-eye"></i> ' . __( 'Pratinjau' ),
                'namespace'     =>      'ns.quantities',
                'type'          =>      'POPUP',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'products' . '/edit/' . $entry->id )
            ], [
                'label'         =>      '<i class="mr-2 las la-balance-scale-left"></i> ' . __( 'Lihat Jumlah' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'products/' . $entry->id . '/units' )
            ], [
                'label'         =>      '<i class="mr-2 las la-history"></i> ' . __( 'Lihat Riwayat' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'products/' . $entry->id . '/history' )
            ], [
                'label'     =>  '<i class="mr-2 las la-trash"></i> ' . __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       => ns()->url( '/api/nexopos/v4/crud/ns.products/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Apakah Anda ingin menghapus ini?' ),
                ]
            ]
        ];

        return $entry;
    }

    public function hook( $query )
    {
        return $query->orderBy( 'updated_at', 'desc' );
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
                if ( $entity instanceof Product ) {
                    $this->deleteProductAttachedRelation( $entity );
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
            'list'      =>  'ns.products',
            'create'    =>  'ns.products/create',
            'edit'      =>  'ns.products/edit/#'
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
                'confirm'       =>  __( 'Apakah Anda ingin menghapus item yang dipilih?' ),
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

