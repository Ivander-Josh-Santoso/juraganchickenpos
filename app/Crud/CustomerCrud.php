<?php
namespace App\Crud;

use App\Events\CustomerAfterCreatedEvent;
use App\Events\CustomerAfterUpdatedEvent;
use App\Events\CustomerBeforeDeletedEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CrudService;
use App\Services\Helper;
use App\Services\Options;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerBillingAddress;
use App\Models\CustomerGroup;
use App\Models\CustomerShippingAddress;
use App\Services\Users;
use Exception;
use TorMorten\Eventy\Facades\Events as Hook;

class CustomerCrud extends CrudService
{
    /**
     * define the base table
     */
    protected $table      =   'nexopos_customers';

    /**
     * base route name
     */
    protected $mainRoute      =   'ns.customers.index';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.customers';

    /**
     * Model Used
     */
    protected $model      =   \App\Models\Customer::class;

    /**
     * Adding relation
     */
    public $relations   =  [
        [ 'nexopos_customers_groups', 'nexopos_customers.group_id', '=', 'nexopos_customers_groups.id' ],
        [ 'nexopos_users', 'nexopos_customers.author', '=', 'nexopos_users.id' ],
    ];

    /**
     * all tabs mentionned on the tabs relation
     * are ignored on the parent model.
     */
    protected $tabsRelations    =   [
        'shipping'      =>      [ CustomerShippingAddress::class, 'customer_id', 'id' ],
        'billing'       =>      [ CustomerBillingAddress::class, 'customer_id', 'id' ],
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

    protected $permissions = [
        'create' => 'nexopos.create.customers',
        'read' => 'nexopos.read.customers',
        'update' => 'nexopos.update.customers',
        'delete' => 'nexopos.delete.customers',
    ];

    /**
     * Define Constructor
     * @param   
     */
    public function __construct()
    {
        parent::__construct();

        $this->options      =   app()->make( Options::class );

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
            'list_title'             =>  __( 'Daftar Pelanggan' ),
            'list_description'       =>  __( 'Tampilkan semua pelanggan.' ),
            'no_entry'               =>  __( 'Belum ada pelanggan yang terdaftar' ),
            'create_new'             =>  __( 'Tambah pelanggan baru' ),
            'create_title'           =>  __( 'Buat pelanggan baru' ),
            'create_description'     =>  __( 'Daftarkan pelanggan baru dan simpan.' ),
            'edit_title'             =>  __( 'Ubah pelanggan' ),
            'edit_description'       =>  __( 'Modifikasi Pelanggan.' ),
            'back_to_list'           =>  __( 'Kembali ke Daftar Pelanggan' ),
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
    public function getForm( Customer $entry = null ) 
    {
        return [
            'main'  =>  [
                'label' =>  __( 'Nama Pelanggan' ),
                'name'  =>  'name',
                'validation'    =>  'required',
                'value'         =>  $entry->name ?? '',
                'description'   =>  __( 'Berikan nama unik untuk pelanggan.' )
            ], 
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'fields'    =>  [
                        // [
                        //     'type'          =>  'text',
                        //     'label'         =>  __( 'Nama Belakang' ),
                        //     'name'          =>  'surname',
                        //     'value'         =>  $entry->surname ?? '',
                        //     'description'   =>  __( 'Berikan nama belakang pelanggan' )
                        // ], 
                        [
                            'type'          =>  'select',
                            'label'         =>  __( 'Grup' ),
                            'name'          =>  'group_id',
                            'value'         =>  $entry->group_id ?? '',
                            'options'       =>  Helper::toJsOptions( CustomerGroup::all(), [ 'id', 'name' ]),
                            'description'   =>  __( 'Tetapkan pelanggan ke dalam grup' )
                        ], 
                        // [
                        //     'type'          =>  'email',
                        //     'label'         =>  __( 'Email' ),
                        //     'name'          =>  'email',
                        //     'value'         =>  $entry->email ?? '',
                        //     'validation'    =>  [
                        //         'required',
                        //         'email',
                        //         $entry instanceof Customer ? Rule::unique( 'nexopos_customers', 'email' )->ignore( $entry->id ) : Rule::unique( 'nexopos_customers', 'email' )
                        //     ],
                        //     'description'   =>  __( 'Berikan email pelanggan' )
                        // ], 
                        [
                            'type'          =>  'text',
                            'label'         =>  __( 'Nomor Telepon' ),
                            'name'          =>  'phone',
                            'value'         =>  $entry->phone ?? '',
                            'description'   =>  __( 'Berikan nomor telepon pelanggan' )
                        ], 
                        // [
                        //     'type'          =>  'text',
                        //     'label'         =>  __( 'Kotak Pos' ),
                        //     'name'          =>  'pobox',
                        //     'value'         =>  $entry->pobox ?? '',
                        //     'description'   =>  __( 'Berikan kotak pos pelanggan' )
                        // ],
                        // [
                        //     'type'          =>  'select',
                        //     'options'       =>  Helper::kvToJsOptions([
                        //         ''          =>  __( 'Belum Ditentukan' ),
                        //         'male'      =>  __( 'Laki-laki' ),
                        //         'female'    =>  __( 'Perempuan' )          
                        //     ]),
                        //     'label'         =>  __( 'Jenis Kelamin' ),
                        //     'name'          =>  'gender',
                        //     'value'         =>  $entry->gender ?? '',
                        //     'description'   =>  __( 'Berikan jenis kelamin pelanggan' )
                        // ]
                    ]
                ],
                'billing'  =>  [
                    'label'     =>  __( 'Alamat Penagihan' ),
                    'fields'    =>  [
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'name',
                        //     'value' =>  $entry->billing->name ?? '',
                        //     'label' =>  __( 'Nama' ),
                        //     'description'   =>  __( 'Berikan nama alamat penagihan.' ),
                        //     'attributes' => [
                        //         'readonly' => true,
                        //         'style' => 'background-color:#f5f5f5;cursor:not-allowed;'
                        //     ],
                        // ], [
                        //     'type'  =>  'text',
                        //     'name'  =>  'surname',
                        //     'value' =>  $entry->billing->surname ?? '',
                        //     'label' =>  __( 'Nama Belakang' ),
                        //     'description'   =>  __( 'Berikan nama belakang alamat penagihan.' )
                        // ], 
                        [
                            'type'  =>  'text',
                            'name'  =>  'phone',
                            'value' =>  $entry->billing->phone ?? '',
                            'label' =>  __( 'Telepon' ),
                            'description'   =>  __( 'Nomor telepon alamat penagihan.' )
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'address_1',
                            'value' =>  $entry->billing->address_1 ?? '',
                            'label' =>  __( 'Alamat' ),
                            'description'   =>  __( 'Alamat penagihan pertama.' )
                        ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'address_2',
                        //     'value' =>  $entry->billing->address_2 ?? '',
                        //     'label' =>  __( 'Alamat 2' ),
                        //     'description'   =>  __( 'Alamat penagihan kedua.' )
                        // ],
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'country',
                        //     'value' =>  $entry->billing->country ?? '',
                        //     'label' =>  __( 'Negara' ),
                        //     'description'   =>  __( 'Negara alamat penagihan.' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'city',
                        //     'value' =>  $entry->billing->city ?? '',
                        //     'label' =>  __( 'Kota' ),
                        //     'description'   =>  __( 'Kota' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'pobox',
                        //     'value' =>  $entry->billing->pobox ?? '',
                        //     'label' =>  __( 'Kotak Pos' ),
                        //     'description'   =>  __( 'Alamat pos' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'company',
                        //     'value' =>  $entry->billing->company ?? '',
                        //     'label' =>  __( 'Perusahaan' ),
                        //     'description'   =>  __( 'Perusahaan' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'email',
                        //     'value' =>  $entry->billing->email ?? '',
                        //     'label' =>  __( 'Email' ),
                        //     'description'   =>  __( 'Email' )
                        // ], 
                    ]
                ],
                'shipping'  =>  [
                    'label'     =>  __( 'Alamat Pengiriman' ),
                    'fields'    =>  [
                        [
                        //     'type'  =>  'text',
                        //     'name'  =>  'name',
                        //     'value' =>  $entry->shipping->name ?? '',
                        //     'label' =>  __( 'Nama' ),
                        //     'description'   =>  __( 'Berikan nama alamat pengiriman.' )
                        // ], [
                        //     'type'  =>  'text',
                        //     'name'  =>  'surname',
                        //     'value' =>  $entry->shipping->surname ?? '',
                        //     'label' =>  __( 'Nama Belakang' ),
                        //     'description'   =>  __( 'Berikan nama belakang alamat pengiriman.' )
                        // ], [
                            'type'  =>  'text',
                            'name'  =>  'phone',
                            'value' =>  $entry->shipping->phone ?? '',
                            'label' =>  __( 'Telepon' ),
                            'description'   =>  __( 'Nomor telepon alamat pengiriman.' )
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'address_1',
                            'value' =>  $entry->shipping->address_1 ?? '',
                            'label' =>  __( 'Alamat' ),
                            'description'   =>  __( 'Alamat pengiriman.' )
                        ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'address_2',
                        //     'value' =>  $entry->shipping->address_2 ?? '',
                        //     'label' =>  __( 'Alamat 2' ),
                        //     'description'   =>  __( 'Alamat pengiriman kedua.' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'country',
                        //     'value' =>  $entry->shipping->country ?? '',
                        //     'label' =>  __( 'Negara' ),
                        //     'description'   =>  __( 'Negara alamat pengiriman.' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'city',
                        //     'value' =>  $entry->shipping->city ?? '',
                        //     'label' =>  __( 'Kota' ),
                        //     'description'   =>  __( 'Kota' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'pobox',
                        //     'value' =>  $entry->shipping->pobox ?? '',
                        //     'label' =>  __( 'Kotak Pos' ),
                        //     'description'   =>  __( 'Alamat pos' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'company',
                        //     'value' =>  $entry->shipping->company ?? '',
                        //     'label' =>  __( 'Perusahaan' ),
                        //     'description'   =>  __( 'Perusahaan' )
                        // ], 
                        // [
                        //     'type'  =>  'text',
                        //     'name'  =>  'email',
                        //     'value' =>  $entry->shipping->email ?? '',
                        //     'label' =>  __( 'Email' ),
                        //     'description'   =>  __( 'Email' )
                        // ], 
                    ]
                ],


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
        return collect( $inputs )->map( function( $value, $key ) {
            if ( $key === 'group_id' && empty( $value ) ) {
                $value     =   $this->options->get( 'ns_customers_default_group', false );
                if ( $value === false ) {
                    throw new Exception( __( 'Tidak ada grup yang dipilih dan grup default belum dikonfigurasi.' ) );
                }
            }
            return $value;
        });
    }

    /**
     * Filter PUT input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPutInputs( $inputs, \App\Models\Customer $entry )
    {
        return collect( $inputs )->map( function( $value, $key ) {
            if ( $key === 'group_id' && empty( $value ) ) {
                $value     =   $this->options->get( 'ns_customers_default_group', false );
                if ( $value === false ) {
                    throw new Exception( __( 'Tidak ada grup yang dipilih dan grup default belum dikonfigurasi.' ) );
                }
            }
            return $value;
        });
    }

    /**
     * After Crud POST
     * @param  object entry
     * @return  void
     */
    public function afterPost( $inputs, Customer $customer )
    {
        CustomerAfterCreatedEvent::dispatch( $customer );

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
    public function afterPut( $inputs, Customer $customer )
    {
        CustomerAfterUpdatedEvent::dispatch( $customer );
        
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
    public function beforeDelete( $namespace, $id, Customer $customer ) {
        if ( $namespace == 'ns.customers' ) {
            $this->allowedTo( 'delete' );

            CustomerBeforeDeletedEvent::dispatch( $customer );
        }
    }

    /**
     * before creating
     * @return  void
     */
    public function beforePost( $request ) {
        $this->allowedTo( 'create' );
    }

    /**
     * before updating
     * @return  void
     */
    public function beforePut( $request, $customer ) {
        $this->allowedTo( 'update' );
    }

    /**
     * Define Columns
     * @return  array of columns configuration
     */
    public function getColumns() {
        return [
            'name'  =>  [
                'label'  =>  __( 'Nama' )
            ],
            'surname'  =>  [
                'label'  =>  __( 'Nama Belakang' )
            ],
            'nexopos_customers_groups_name'  =>  [
                'label'  =>  __( 'Grup' )
            ],
            'email'  =>  [
                'label'  =>  __( 'Email' )
            ],
            // 'account_amount'  =>  [
            //     'label'  =>  __( 'Kredit Akun' )
            // ],
            'owed_amount'  =>  [
                'label'  =>  __( 'Jumlah Terhutang' )
            ],
            'purchases_amount'  =>  [
                'label'  =>  __( 'Jumlah Pembelian' )
            ],
            // 'gender'  =>  [
            //     'label'  =>  __( 'Jenis Kelamin' )
            // ],
            'nexopos_users_username'  =>  [
                'label'  =>  __( 'Pembuat' )
            ],
            'created_at'  =>  [
                'label'  =>  __( 'Dibuat Pada' )
            ],
        ];
    }

    /**
     * Define actions
     */
    public function setActions( $entry, $namespace )
    {
        $entry->owed_amount         =   ( string ) ns()->currency->define( $entry->owed_amount );
        $entry->account_amount      =   ( string ) ns()->currency->define( $entry->account_amount );
        $entry->purchases_amount    =   ( string ) ns()->currency->define( $entry->purchases_amount );
        
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Ubah' ),
                'namespace'     =>      'edit_customers_group',
                'type'          =>      'GOTO',
                'url'           =>      ns()->url( 'dashboard/customers/edit/' . $entry->id )
            ], [
                'label'         =>      __( 'Pesanan' ),
                'namespace'     =>      'customers_orders',
                'type'          =>      'GOTO',
                'url'           =>      ns()->url( 'dashboard/customers/' . $entry->id . '/orders' )
            ], [
                'label'         =>      __( 'Hadiah' ),
                'namespace'     =>      'customers_rewards',
                'type'          =>      'GOTO',
                'url'           =>      ns()->url( 'dashboard/customers/' . $entry->id . '/rewards' )
            ], [
                'label'         =>      __( 'Kupon' ),
                'namespace'     =>      'customers_rewards',
                'type'          =>      'GOTO',
                'url'           =>      ns()->url( 'dashboard/customers/' . $entry->id . '/coupons' )
            ], [
                'label'         =>      __( 'Riwayat Akun' ),
                'namespace'     =>      'customers_rewards',
                'type'          =>      'GOTO',
                'url'           =>      ns()->url( 'dashboard/customers/' . $entry->id . '/account-history' )
            ], [
                'label'     =>      __( 'Hapus' ),
                'namespace' =>      'delete',
                'type'      =>      'DELETE',
                'url'       =>      ns()->url( '/api/nexopos/v4/crud/ns.customers/' . $entry->id ),
                'confirm'   =>  [
                    'message'   =>  __( 'Apakah Anda yakin ingin menghapus ini?' ),
                    'title'     =>  __( 'Hapus pelanggan' )
                ]
            ]
        ];

        $entry->{ '$checked' }           =   false;
        $entry->{ '$toggled' }           =   false;
        $entry->{ '$id' }                =   $entry->id;
        $entry->surname                  =   $entry->surname ?? __( 'Belum Ditentukan' );
        $entry->pobox                   =   $entry->pobox ?? __( 'Belum Ditentukan' );
        $entry->reward_system_id        =   $entry->reward_system_id ?? __( 'Belum Ditentukan' );
        
        switch( $entry->gender ) {
            case 'male': $entry->gender = __( 'Laki-laki' );break;
            case 'female': $entry->gender = __( 'Perempuan' );break;
            default: $entry->gender = __( 'Belum Ditentukan' );break;
        }

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
        $user   =   app()->make( Users::class );
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

            foreach ( $request->input( 'entries_id' ) as $id ) {
                $entity     =   $this->model::find( $id );
                if ( $entity instanceof Customer ) {
                    $entity->delete();
                    $status[ 'success' ]++;
                } else {
                    $status[ 'failed' ]++;
                }
            }
            return $status;
        }
        return false;
    }

    /**
     * get Links
     * @return  array of links
     */
    public function getLinks()
    {
        return  [
            'list'      =>  ns()->url( '/dashboard/customers' ),
            'create'    =>  ns()->url( '/dashboard/customers/create' ),
            'edit'      =>  ns()->url( '/dashboard/customers/edit/{id}' ),
            'post'      =>  ns()->url( '/api/nexopos/v4/crud/ns.customers' ),
            'put'       =>  ns()->url( '/api/nexopos/v4/crud/ns.customers/{id}' ),
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
                'label'         =>  __( 'Hapus Pelanggan Terpilih' ),
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
