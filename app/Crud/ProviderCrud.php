<?php
namespace App\Crud;

use App\Exceptions\NotAllowedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Models\User;
use Exception;
use App\Models\Provider;
use App\Services\Users;
use TorMorten\Eventy\Facades\Events as Hook;

class ProviderCrud extends CrudService
{
    protected $table      =   'nexopos_providers';
    protected $mainRoute      =   'ns.providers';
    protected $namespace  =   'ns.providers';
    protected $model      =   Provider::class;

    public $relations   =  [
        [ 'nexopos_users', 'nexopos_users.id', '=', 'nexopos_providers.author' ]
    ];

    protected $listWhere    =   [];
    protected $whereIn      =   [];
    public $fillable    =   [];

    protected $permissions  =   [
        'create'    =>  'nexopos.create.providers',
        'read'      =>  'nexopos.read.providers',
        'update'    =>  'nexopos.update.providers',
        'delete'    =>  'nexopos.delete.providers',
    ];

    public function __construct()
    {
        parent::__construct();

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
    }

    public function getLabels()
    {
        return [
            'list_title'            =>  __( 'Daftar Penyedia' ),
            'list_description'      =>  __( 'Menampilkan semua penyedia.' ),
            'no_entry'              =>  __( 'Belum ada penyedia yang terdaftar' ),
            'create_new'            =>  __( 'Tambah penyedia baru' ),
            'create_title'          =>  __( 'Buat penyedia baru' ),
            'create_description'    =>  __( 'Daftarkan penyedia baru dan simpan.' ),
            'edit_title'            =>  __( 'Edit penyedia' ),
            'edit_description'      =>  __( 'Ubah penyedia.' ),
            'back_to_list'          =>  __( 'Kembali ke daftar penyedia' ),
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
                'name'          =>  'name',
                'value'         =>  $entry->name ?? '',
                'description'   =>  __( 'Berikan nama untuk sumber daya.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'Umum' ),
                    'fields'    =>  [
                        // [
                        //     // 'type'  =>  'text',
                        //     // 'name'  =>  'email',
                        //     // 'label' =>  __( 'Email' ),
                        //     // 'description'   =>  __( 'Masukkan email penyedia. Bisa digunakan untuk mengirim email otomatis.' ),
                        //     // 'value' =>  $entry->email ?? '',
                        // ], [
                        //     // 'type'  =>  'text',
                        //     // 'name'  =>  'surname',
                        //     // 'label' =>  __( 'Nama Belakang' ),
                        //     // 'description'   =>  __( 'Nama belakang penyedia jika diperlukan.' ),
                        //     // 'value' =>  $entry->surname ?? '',
                        // ], 
                        
                        [
                            'type'  =>  'text',
                            'name'  =>  'phone',
                            'label' =>  __( 'Telepon' ),
                            'description'   =>  __( 'Nomor telepon kontak penyedia.' ),
                            'value' =>  $entry->phone ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'address_1',
                            'label' =>  __( 'Alamat' ),
                            'description'   =>  __( 'Alamat penyedia.' ),
                            'value' =>  $entry->address_1 ?? '',
                        ], 
                        // [
                        //     // 'type'  =>  'text',
                        //     // 'name'  =>  'address_2',
                        //     // 'label' =>  __( 'Alamat 2' ),
                        //     // 'description'   =>  __( 'Alamat kedua penyedia.' ),
                        //     // 'value' =>  $entry->address_2 ?? '',
                        // ], 
                        [
                            'type'  =>  'textarea',
                            'name'  =>  'description',
                            'label' =>  __( 'Deskripsi' ),
                            'description'   =>  __( 'Detail lebih lanjut mengenai penyedia.' ),
                            'value' =>  $entry->description ?? '',
                        ], 
                    ]
                ]
            ]
        ];
    }

    public function filterPostInputs( $inputs )
    {
        return $inputs;
    }

    public function filterPutInputs( $inputs, Provider $entry )
    {
        return $inputs;
    }

    public function beforePost( $request )
    {
        $this->allowedTo( 'create' );

        return $request;
    }

    public function afterPost( $request, Provider $entry )
    {
        return $request;
    }

    public function get( $param )
    {
        switch( $param ) {
            case 'model' : return $this->model ; break;
        }
    }

    public function beforePut( $request, $entry )
    {
        $this->allowedTo( 'update' );

        return $request;
    }

    public function afterPut( $request, $entry )
    {
        return $request;
    }
    
    public function canAccess( $fields )
    {
        $users      =   app()->make( Users::class );
        
        if ( $users->is([ 'admin' ]) ) {
            return [
                'status'    =>  'success',
                'message'   =>  __( 'Akses diberikan.' )
            ];
        }

        throw new Exception( __( 'Anda tidak memiliki akses ke sumber daya tersebut' ) );
    }

    public function beforeDelete( $namespace, $id, $model ) {
        if ( $namespace == 'ns.providers' ) {
            $this->allowedTo( 'delete' );
        }
    }

    public function getColumns() {
        return [
            'name'  =>  [
                'label'  =>  __( 'Nama' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            // 'email'  =>  [
            //     'label'  =>  __( 'Email' ),
            //     '$direction'    =>  '',
            //     '$sort'         =>  false
            // ],
            'phone'  =>  [
                'label'  =>  __( 'Telepon' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            // 'amount_due'  =>  [
            //     'label'  =>  __( 'Jumlah Hutang' ),
            //     '$direction'    =>  '',
            //     '$sort'         =>  false
            // ],
            'amount_paid'  =>  [
                'label'  =>  __( 'Jumlah Dibayar' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'nexopos_users_username'  =>  [
                'label'  =>  __( 'Pembuat' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'  =>  [
                'label'         =>  __( 'Dibuat Pada' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
        ];
    }

    public function setActions( $entry, $namespace )
    {
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        $entry->phone           =   $entry->phone ?? __( 'Tidak Ada' );
        $entry->email           =   $entry->email ?? __( 'Tidak Ada' );

        $entry->amount_due      =   ns()->currency->define( $entry->amount_due )->format();
        $entry->amount_paid     =   ns()->currency->define( $entry->amount_paid )->format();

        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Edit' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'providers' . '/edit/' . $entry->id )
            ], [
                'label'         =>      __( 'Lihat Pengadaan' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'providers/' . $entry->id .  '/procurements/' )
            ], [
                'label'         =>      __( 'Lihat Produk' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      ns()->url( '/dashboard/' . 'providers/' . $entry->id .  '/products/' )
            ], [
                'label'     =>  __( 'Hapus' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       =>  ns()->url( '/api/nexopos/v4/crud/ns.providers/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Apakah Anda yakin ingin menghapus ini?' ),
                ]
            ]
        ];

        return $entry;
    }

    public function bulkAction( Request $request ) 
    {
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

            foreach ( $request->input( 'entries' ) as $id ) {
                $entity     =   $this->model::find( $id );
                if ( $entity instanceof Provider ) {
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

    public function getLinks()
    {
        return  [
            'list'      =>  ns()->url( 'dashboard/' . 'providers' ),
            'create'    =>  ns()->url( 'dashboard/' . 'providers/create' ),
            'edit'      =>  ns()->url( 'dashboard/' . 'providers/edit/' ),
            'post'      =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.providers' ),
            'put'       =>  ns()->url( 'api/nexopos/v4/crud/' . 'ns.providers/{id}' . '' ),
        ];
    }

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

    public function getExports()
    {
        return [];
    }
}
