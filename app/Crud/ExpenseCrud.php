<?php
namespace App\Crud;

use App\Events\ExpenseAfterCreateEvent;
use App\Events\ExpenseAfterUpdateEvent;
use App\Events\ExpenseBeforeCreateEvent;
use App\Events\ExpenseBeforeDeleteEvent;
use App\Events\ExpenseBeforeUpdateEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use App\Models\User;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Role;
use App\Services\Helper;

class ExpenseCrud extends CrudService
{
    /**
     * mendefinisikan tabel dasar
     */
    protected $table       =   'nexopos_expenses';

    /**
     * nama route dasar
     */
    protected $mainRoute       =   'ns.expenses';

    /**
     * Mendefinisikan namespace
     */
    protected $namespace  =   'ns.expenses';

    /**
     * Model yang digunakan
     */
    protected $model       =   Expense::class;

    /**
     * Menambahkan relasi
     */
    public $relations   =  [
        [ 'nexopos_users as user', 'nexopos_expenses.author', '=', 'user.id' ],
        [ 'nexopos_expenses_categories as expense_category', 'expense_category.id', '=', 'nexopos_expenses.category_id' ],
    ];

    protected $pick     =   [
        'user'                  =>  [ 'username' ],
        'expense_category'      =>  [ 'name' ],
    ];

    protected $permissions = [
        'create' => 'nexopos.create.expenses',
        'read' => 'nexopos.read.expenses',
        'update' => 'nexopos.update.expenses',
        'delete' => 'nexopos.delete.expenses',
    ];

    /**
     * Mendefinisikan kondisi where
     * @var  array
     **/
    protected $listWhere    =   [];

    /**
     * Mendefinisikan kondisi where in
     * @var  array
     */
    protected $whereIn      =   [];

    /**
     * Field yang diisi saat post/put
     */
    public $fillable    =   [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
    }

    /**
     * Mengembalikan label untuk CRUD instance
     * @return  array
     */
    public function getLabels()
    {
        return [
            'list_title'             =>  __( 'Daftar Pengeluaran' ),
            'list_description'       =>  __( 'Tampilkan semua pengeluaran.' ),
            'no_entry'               =>  __( 'Belum ada pengeluaran yang tercatat' ),
            'create_new'             =>  __( 'Tambah pengeluaran baru' ),
            'create_title'           =>  __( 'Buat pengeluaran baru' ),
            'create_description'     =>  __( 'Daftarkan pengeluaran baru dan simpan.' ),
            'edit_title'             =>  __( 'Ubah pengeluaran' ),
            'edit_description'       =>  __( 'Modifikasi data pengeluaran.' ),
            'back_to_list'           =>  __( 'Kembali ke Daftar Pengeluaran' ),
        ];
    }

    /**
     * Mengecek apakah fitur aktif
     * @return  boolean
     */
    public function isEnabled( $feature )
    {
        return false; // default false
    }

    /**
     * Mendefinisikan form
     * @param  object|null
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
                            'type'          =>  'switch',
                            'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
                            'name'          =>  'active',
                            'label'         =>  __( 'Aktif' ),
                            'description'   =>  __( 'Tentukan apakah pengeluaran ini berlaku atau tidak. Berlaku untuk pengeluaran berkala dan tidak.' ),
                            'validation'    =>  'required',
                            'value'         =>  $entry->active ?? '',
                        ], [
                            'type'          =>  'select',
                            'name'          =>  'group_id',
                            'label'         =>  __( 'Grup Pengguna' ),
                            'value'         =>  $entry->group_id ?? '',
                            'description'   =>  __( 'Tetapkan pengeluaran pada grup pengguna. Pengeluaran akan dikalikan dengan jumlah entitas.' ),
                            'options'       =>  [ 
                                [
                                    'label' =>  __( 'Tidak Ada' ),
                                    'value' =>  '0',
                                ], 
                                ...Helper::toJsOptions( Role::get(), [ 'id', 'name' ])
                            ],
                        ], [
                            'type'          =>  'select',
                            'options'       =>  Helper::toJsOptions( ExpenseCategory::get(), [ 'id', 'name' ]),
                            'name'          =>  'category_id',
                            'label'         =>  __( 'Kategori Pengeluaran' ),
                            'description'   =>  __( 'Tetapkan pengeluaran pada kategori' ),
                            'validation'    =>  'required',
                            'value'         =>  $entry->category_id ?? '',
                        ], [
                            'type'          =>  'text',
                            'name'          =>  'value',
                            'label'         =>  __( 'Nilai' ),
                            'description'   =>  __( 'Nilai atau biaya pengeluaran.' ),
                            'value'         =>  $entry->value ?? '',
                        ], [
                            'type'          =>  'switch',
                            'name'          =>  'recurring',
                            'label'         =>  __( 'Berkala' ),
                            'description'   =>  __( 'Jika dipilih Ya, pengeluaran akan terjadi sesuai frekuensi yang ditentukan.' ),
                            'options'       =>  [
                                [
                                    'label' =>  __( 'Ya' ),
                                    'value' =>  true
                                ], [
                                    'label' =>  __( 'Tidak' ),
                                    'value' =>  false
                                ]
                            ],
                            'value'         =>  $entry->recurring ?? '',
                        ], [
                            'type'          =>  'select',
                            'options'       =>  [
                                [
                                    'label' =>  __( 'Awal Bulan' ),
                                    'value' =>  'month_starts',
                                ], [
                                    'label' =>  __( 'Tengah Bulan' ),
                                    'value' =>  'month_mids',
                                ], [
                                    'label' =>  __( 'Akhir Bulan' ),
                                    'value' =>  'month_ends',
                                ], [
                                    'label' =>  __( 'X Hari Sebelum Akhir Bulan' ),
                                    'value' =>  'x_before_month_ends',
                                ], [
                                    'label' =>  __( 'X Hari Setelah Awal Bulan' ),
                                    'value' =>  'x_after_month_starts',
                                ]
                            ],
                            'name'          =>  'occurence',
                            'label'         =>  __( 'Frekuensi' ),
                            'description'   =>  __( 'Tentukan seberapa sering pengeluaran ini terjadi' ),
                            'value'         =>  $entry->occurence ?? '',
                        ], [
                            'type'          =>  'text',
                            'name'          =>  'occurence_value',
                            'label'         =>  __( 'Nilai Frekuensi' ),
                            'description'   =>  __( 'Digunakan jika frekuensi berupa X hari setelah awal bulan atau X hari sebelum akhir bulan.' ),
                            'value'         =>  $entry->occurence_value ?? '',
                        ], [
                            'type'          =>  'textarea',
                            'name'          =>  'description',
                            'label'         =>  __( 'Deskripsi' ),
                            'value'         =>  $entry->description ?? '',
                        ], 
                    ]
                ]
            ]
        ];
    }

    /**
     * Filter input POST
     */
    public function filterPostInputs( $inputs )
    {
        return $inputs;
    }

    /**
     * Filter input PUT
     */
    public function filterPutInputs( $inputs, Expense $entry )
    {
        return $inputs;
    }

    /**
     * Sebelum menyimpan record
     */
    public function beforePost( $request )
    {
        $this->allowedTo( 'create' );

        event( new ExpenseBeforeCreateEvent( $request ) );

        return $request;
    }

    /**
     * Setelah menyimpan record
     */
    public function afterPost( $request, Expense $entry )
    {
        event( new ExpenseAfterCreateEvent( $entry, $request ) );

        return $request;
    }

    public function hook( $query )
    {
        $query->orderBy( 'id', 'desc' );
    }
    
    /**
     * Get parameter
     */
    public function get( $param )
    {
        switch( $param ) {
            case 'model' : return $this->model ; break;
        }
    }

    /**
     * Sebelum update record
     */
    public function beforePut( $request, $entry )
    {
        $this->allowedTo( 'update' );

        event( new ExpenseBeforeUpdateEvent( $entry, $request ) );

        return $request;
    }

    /**
     * Setelah update record
     */
    public function afterPut( $request, $entry )
    {
        event( new ExpenseAfterUpdateEvent( $entry, $request ) );

        return $request;
    }
    
    /**
     * Proteksi akses ke UI CRUD
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
     * Sebelum hapus record
     */
    public function beforeDelete( $namespace, $id, $model ) {
        if ( $namespace == 'ns.expenses' ) {
            
            $this->allowedTo( 'delete' );

            event( new ExpenseBeforeDeleteEvent( $model ) );
        }
    }

    /**
     * Definisi kolom
     */
    public function getColumns() {
        return [
            'name'  =>  [
                'label'  =>  __( 'Nama' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'expense_category_name'  =>  [
                'label'  =>  __( 'Kategori' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'value'  =>  [
                'label'  =>  __( 'Nilai' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'recurring'  =>  [
                'label'  =>  __( 'Berkala' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'occurence'  =>  [
                'label'  =>  __( 'Frekuensi' ),
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
     * Definisi aksi
     */
    public function setActions( $entry, $namespace )
    {
        // Jangan timpa
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        $entry->value           =   (string) ns()->currency->value( $entry->value );
        $entry->recurring       =   (bool) $entry->recurring ? __( 'Ya' ) : __( 'Tidak' );

        switch( $entry->occurence ) {
            case 'month_starts' : $entry->occurence = __( 'Awal Bulan' );break;
            case 'month_mids' : $entry->occurence = __( 'Tengah Bulan' );break;
            case 'month_ends' : $entry->occurence = __( 'Akhir Bulan' );break;
            case 'x_after_month_starts' : $entry->occurence = __( 'X Hari Setelah Awal Bulan' );break;
            case 'x_before_month_ends' : $entry->occurence = __( 'X Hari Sebelum Akhir Bulan' );break;
            default: $entry->occurence = __( 'Frekuensi Tidak Dikenal' ); break;
        }

        // Anda dapat mengubah di sini
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Ubah' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'expenses' . '/edit/' . $entry->id )
            ], [
                'label'     =>  __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       => ns()->url( '/api/nexopos/v4/crud/ns.expenses/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Apakah Anda ingin menghapus ini?' ),
                ]
            ]
        ];

        return $entry;
    }

    /**
     * Aksi hapus massal
     */
    public function bulkAction( Request $request ) 
    {
        /**
         * Hanya admin dan supervisor yang diizinkan menghapus.
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
                if ( $entity instanceof Expense ) {
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
     * Mendapatkan link
     */
    public function getLinks()
    {
        return  [
            'list'      => ns()->url( 'dashboard/' . 'expenses' ),
            'create'    => ns()->url( 'dashboard/' . 'expenses/create' ),
            'edit'      => ns()->url( 'dashboard/' . 'expenses/edit/{id}' ),
            'post'      => ns()->url( 'api/nexopos/v4/crud/' . 'ns.expenses' ),
            'put'       => ns()->url( 'api/nexopos/v4/crud/' . 'ns.expenses/' . '{id}' ),
        ];
    }

    /**
     * Mendapatkan aksi massal
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
     * Mendapatkan format ekspor
     */
    public function getExports()
    {
        return [];
    }
}
