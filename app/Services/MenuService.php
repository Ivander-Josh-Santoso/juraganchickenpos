<?php
namespace App\Services;

use TorMorten\Eventy\Facades\Eventy as Hook;

class MenuService
{
    protected $menus;

    public function buildMenus()
    {
        $this->menus    =   [
            'dashboard' =>  [
                'label' =>  __( 'Dasbor' ),                
                'permissions'   =>  [ 'update.core', 'read.dashboard' ],
                'icon'          =>  'la-home',
                'childrens'     =>  [
                    'index'             =>  [
                        'label' =>  __( 'Beranda' ),
                        'permissions'   =>  [ 'read.dashboard' ],
                        'href'          =>  ns()->url( '/dashboard' ),
                    ],
                ]
            ], 
            'pos'   =>  [
                'label' =>  __( 'POS' ),
                'icon'  =>  'la-cash-register',
                'permissions'   =>  [ 'nexopos.create.orders' ],
                'href'  =>  ns()->url( '/dashboard/pos' )
            ], 
            'orders'    =>  [
                'label' =>  __( 'Pesanan' ),
                'permissions'   =>  [ 'nexopos.update.orders', 'nexopos.read.orders' ],
                'icon'  =>  'la-list-ol',
                'childrens'     =>  [
                    'order-list'    =>  [
                        'label' =>  __( 'Daftar Pesanan' ),
                        'href'  =>  ns()->url( '/dashboard/orders' ),
                        'permissions'   =>  [ 'nexopos.update.orders', 'nexopos.read.orders' ],
                    ], 
                    'payment-type'  =>  [
                        'label' =>  __( 'Jenis Pembayaran' ),
                        'href'  =>  ns()->url( '/dashboard/orders/payments-types' ),
                        'permissions'   =>  [ 'nexopos.manage-payments-types' ],
                    ],
                ],
            ], 
            'medias'    =>  [
                'label' =>  __( 'Media' ),
                'permissions'   =>  [ 'nexopos.upload.medias', 'nexopos.see.medias' ],
                'icon'          =>  'la-photo-video',
                'href'          =>  ns()->url( '/dashboard/medias' )
            ], 
            'customers' =>  [
                'label' =>  __( 'Pelanggan' ),
                'permissions'   =>  [
                    'nexopos.read.customers',
                    'nexopos.create.customers',
                    'nexopos.read.customers-groups',
                    'nexopos.create.customers-groups',
                    'nexopos.import.customers',
                    'nexopos.read.rewards',
                    'nexopos.create.rewards',
                    'nexopos.read.coupons',
                    'nexopos.create.coupons',
                ],
                'icon'  =>  'la-user-friends',
                'childrens'     =>  [
                    'customers' =>  [
                        'label' =>  __( 'Daftar' ),
                        'permissions'   =>  [ 'nexopos.read.customers' ],
                        'href'  =>  ns()->url( '/dashboard/customers' )
                    ], 
                    'create-customer'  =>   [
                        'label' =>  __( 'Tambah Pelanggan' ),
                        'permissions'   =>  [ 'nexopos.create.customers' ],
                        'href'  =>  ns()->url( '/dashboard/customers/create' )
                    ], 
                    'customers-groups'  =>  [
                        'label' =>  __( 'Grup Pelanggan' ),
                        'permissions'   =>  [ 'nexopos.read.customers-groups' ],
                        'href'  =>  ns()->url( '/dashboard/customers/groups' )
                    ], 
                    'create-customers-group'    =>  [
                        'label' =>  __( 'Tambah Grup' ),
                        'permissions'   =>  [ 'nexopos.create.customers-groups' ],
                        'href'  =>  ns()->url( '/dashboard/customers/groups/create' )
                    ], 
                    // 'list-reward-system'    =>  [
                    //     'label' =>  __( 'Sistem Poin' ),
                    //     'permissions'   =>  [ 'nexopos.read.rewards' ],
                    //     'href'  =>  ns()->url( '/dashboard/customers/rewards-system' )
                    // ],
                    // 'create-reward-system'    =>  [
                    //     'label' =>  __( 'Tambah Poin' ),
                    //     'permissions'   =>  [ 'nexopos.create.rewards' ],
                    //     'href'  =>  ns()->url( '/dashboard/customers/rewards-system/create' )
                    // ],
                    // 'list-coupons'    =>  [
                    //     'label' =>  __( 'Daftar Kupon' ),
                    //     'permissions'   =>  [ 'nexopos.read.coupons' ],
                    //     'href'  =>  ns()->url( '/dashboard/customers/coupons' )
                    // ],
                    // 'create-coupons'    =>  [
                    //     'label' =>  __( 'Tambah Kupon' ),
                    //     'permissions'   =>  [ 'nexopos.create.coupons' ],
                    //     'href'  =>  ns()->url( '/dashboard/customers/coupons/create' )
                    // ],
                ]
            ], 
            'providers' =>  [
                'label' =>  __( 'Penyedia' ),
                'icon'  =>  'la-user-tie',
                'permissions'   =>  [
                    'nexopos.read.providers',
                    'nexopos.create.providers',
                ],
                'childrens'     =>  [
                    'providers' =>  [
                        'label' =>  __( 'Daftar' ),
                        'permissions'   =>  [ 'nexopos.read.providers' ],
                        'href'  =>  ns()->url( '/dashboard/providers' )
                    ], 
                    'create-provider'   =>  [
                        'label' =>  __( 'Tambah Penyedia' ),
                        'permissions'   =>  [ 'nexopos.create.providers' ],
                        'href'  =>  ns()->url( '/dashboard/providers/create' )
                    ]
                ]
            ], 
            'expenses' =>  [
                'label' =>  __( 'Akuntansi' ),
                'icon'      =>  'la-stream',
                'permissions'   =>  [
                    "nexopos.read.expenses",
                    "nexopos.create.expenses",
                    "nexopos.read.expenses-categories",
                    "nexopos.create.expenses-categories",
                ],
                'childrens'     =>  [
                    'expenses' =>  [
                        'label' =>  __( 'Pengeluaran' ),
                        'permissions'   =>  [ 'nexopos.read.expenses' ],
                        'href'  =>  ns()->url( '/dashboard/expenses' )
                    ], 
                    'create-expense'   =>  [
                        'label' =>  __( 'Tambah Pengeluaran' ),
                        'permissions'   =>  [ 'nexopos.create.expenses' ],
                        'href'  =>  ns()->url( '/dashboard/expenses/create' )
                    ],
                    'cash-flow-history'  =>  [
                        'label' =>  __( 'Riwayat Arus Kas' ),
                        'permissions'   =>  [ 'nexopos.read.expenses' ],
                        'href'          =>  ns()->url( '/dashboard/cash-flow/history' )
                    ],
                    'expenses-categories'   =>  [
                        'label' =>  __( 'Akun Pengeluaran' ),
                        'permissions'   =>  [ 'nexopos.read.expenses-categories' ],
                        'href'  =>  ns()->url( '/dashboard/expenses/categories' )
                    ],
                    'create-expenses-categories'   =>  [
                        'label' =>  __( 'Tambah Akun Pengeluaran' ),
                        'permissions'   =>  [ 'nexopos.create.expenses-categories' ],
                        'href'  =>  ns()->url( '/dashboard/expenses/categories/create' )
                    ],
                ]
            ], 
            'inventory' =>  [
                'label' =>  __( 'Inventaris' ),
                'icon'  =>  'la-boxes',
                'permissions'   =>  [
                    'nexopos.read.products',
                    'nexopos.create.products',
                    'nexopos.read.categories',
                    'nexopos.create.categories',
                    'nexopos.read.products-units',
                    'nexopos.create.products-units',
                    'nexopos.read.products-units',
                    'nexopos.create.products-units',
                    'nexopos.make.products-adjustments',
                ],
                'childrens'     =>  [
                    'products'  =>  [
                        'label' =>  __( 'Produk' ),
                        'permissions'   =>  [ 'nexopos.read.products' ],
                        'href'  =>  ns()->url( '/dashboard/products' )
                    ], 
                    'create-products'   =>  [
                        'label' =>  __( 'Tambah Produk' ),
                        'permissions'   =>  [ 'nexopos.create.products' ],
                        'href'  =>  ns()->url( '/dashboard/products/create' )
                    ], 
                    'labels-printing'   =>  [
                        'label' =>  __( 'Cetak Label' ),
                        'href'          =>  ns()->url( '/dashboard/products/print-labels' ),
                        'permissions'   =>  [ 'nexopos.create.products-labels' ]
                    ],
                    'categories'   =>  [
                        'label' =>  __( 'Kategori' ),
                        'permissions'   =>  [ 'nexopos.read.categories' ],
                        'href'  =>  ns()->url( '/dashboard/products/categories' )
                    ], 
                    'create-categories'   =>  [
                        'label' =>  __( 'Tambah Kategori' ),
                        'permissions'   =>  [ 'nexopos.create.categories' ],
                        'href'  =>  ns()->url( '/dashboard/products/categories/create' )
                    ],
                    'units'   =>  [
                        'label' =>  __( 'Satuan' ),
                        'permissions'   =>  [ 'nexopos.read.products-units' ],
                        'href'  =>  ns()->url( '/dashboard/units' )
                    ],
                    'create-units'   =>  [
                        'label' =>  __( 'Tambah Satuan' ),
                        'permissions'   =>  [ 'nexopos.create.products-units' ],
                        'href'  =>  ns()->url( '/dashboard/units/create' )
                    ],
                    'unit-groups'   =>  [
                        'label' =>  __( 'Grup Satuan' ),
                        'permissions'   =>  [ 'nexopos.read.products-units' ],
                        'href'  =>  ns()->url( '/dashboard/units/groups' )
                    ],
                    'create-unit-groups'   =>  [
                        'label' =>  __( 'Tambah Grup Satuan' ),
                        'permissions'   =>  [ 'nexopos.create.products-units' ],
                        'href'  =>  ns()->url( '/dashboard/units/groups/create' )
                    ],
                    'stock-adjustment'      =>  [
                        'label' =>  __( 'Penyesuaian Stok' ),
                        'permissions'       =>  [ 'nexopos.make.products-adjustments' ],
                        'href'              =>  ns()->url( '/dashboard/products/stock-adjustment' )
                    ],
                ]
            ], 
            'taxes'     =>  [
                'label' =>  __( 'Pajak' ),
                'icon'  =>  'la-balance-scale-left',
                'permissions'           =>  [
                    'nexopos.create.taxes',
                    'nexopos.read.taxes',
                    'nexopos.update.taxes',
                    'nexopos.delete.taxes',
                ],
                'childrens' =>  [
                    'taxes-groups'   =>  [
                        'label' =>  __( 'Grup Pajak' ),
                        'permissions'   =>  [ 'nexopos.read.taxes' ],
                        'href'          =>  ns()->url( '/dashboard/taxes/groups' )
                    ],
                    'create-taxes-group'   =>  [
                        'label' =>  __( 'Tambah Grup Pajak' ),
                        'permissions'   =>  [ 'nexopos.create.taxes' ],
                        'href'          =>  ns()->url( '/dashboard/taxes/groups/create' )
                    ],
                    'taxes'             =>  [
                        'label' =>  __( 'Pajak' ),
                        'permissions'   =>  [ 'nexopos.read.taxes' ],
                        'href'          =>  ns()->url( '/dashboard/taxes' )
                    ],
                    'create-tax'        =>  [
                        'label' =>  __( 'Tambah Pajak' ),
                        'permissions'   =>  [ 'nexopos.create.taxes' ],
                        'href'          =>  ns()->url( '/dashboard/taxes/create' )
                    ]
                ]
            ],
            'modules' =>  [
                'label' =>  __( 'Modul' ),
                'icon'  =>  'la-plug',
                'permissions'   =>  [ 'manage.modules' ],
                'childrens'     =>  [
                    'modules'  =>  [
                        'label' =>  __( 'Daftar' ),
                        'href'  =>  ns()->url( '/dashboard/modules' )
                    ], 
                    'upload-module'   =>  [
                        'label' =>  __( 'Unggah Modul' ),
                        'href'  =>  ns()->url( '/dashboard/modules/upload' )
                    ], 
                ]
            ], 
            'users'      =>  [
                'label' =>  __( 'Pengguna' ),
                'icon'          =>  'la-users',
                'childrens'     =>  [
                    'profile'  =>  [
                        'label' =>  __( 'Daftar' ),
                        'permissions'   =>  [ 'manage.profile' ],
                        'href'  =>  ns()->url( '/dashboard/users/profile' )
                    ], 
                    'users'  =>  [
                        'label' =>  __( 'Daftar' ),
                        'permissions'   =>  [ 'read.users' ],
                        'href'  =>  ns()->url( '/dashboard/users' )
                    ], 
                    'create-user'  =>  [
                        'label' =>  __( 'Tambah Pengguna' ),
                        'permissions'   =>  [ 'create.users' ],
                        'href'  =>  ns()->url( '/dashboard/users/create' )
                    ], 
                    'roles'  =>  [
                        'label' =>  __( 'Peran' ),
                        'permissions'   =>  [ 'read.roles' ],
                        'href'  =>  ns()->url( '/dashboard/users/roles' )
                    ], 
                    'create-role'  =>  [
                        'label' =>  __( 'Tambah Peran' ),
                        'permissions'   =>  [ 'create.roles' ],
                        'href'  =>  ns()->url( '/dashboard/users/roles/create' )
                    ], 
                    'permissions'  =>  [
                        'label' =>  __( 'Manajer Izin' ),
                        'permissions'   =>  [ 'update.roles' ],
                        'href'  =>  ns()->url( '/dashboard/users/roles/permissions-manager' )
                    ], 
                    'profile'  =>  [
                        'label' =>  __( 'Profil' ),
                        'href'  =>  ns()->url( '/dashboard/users/profile' )
                    ], 
                ]
            ],
            'procurements'      =>  [
                'label' =>  __( 'Pengadaan' ),
                'icon'          =>  'la-truck-loading',
                'permissions'   =>  [ 'nexopos.read.procurements', 'nexopos.create.procurements' ],
                'childrens'     =>  [
                    'procurements'  =>  [
                        'label' =>  __( 'Daftar Pengadaan' ),
                        'permissions'   =>  [ 'nexopos.read.procurements' ],
                        'href'          =>  ns()->url( '/dashboard/procurements' )
                    ], 
                    'procurements-create'  =>  [
                        'label' =>  __( 'Pengadaan Baru' ),
                        'permissions'   =>  [ 'nexopos.create.procurements' ],
                        'href'          =>  ns()->url( '/dashboard/procurements/create' )
                    ], 
                    'procurements-products'  =>  [
                        'label' =>  __( 'Produk' ),
                        'permissions'   =>  [ 'nexopos.update.procurements' ],
                        'href'          =>  ns()->url( '/dashboard/procurements/products' )
                    ], 
                ]
            ],
            'reports'      =>  [
                'label' =>  __( 'Laporan' ),
                'icon'          =>  'la-chart-pie',
                'permissions'   =>  [
                    'nexopos.reports.sales',
                    'nexopos.reports.best_sales',
                    'nexopos.reports.cash_flow',
                    'nexopos.reports.yearly',
                    'nexopos.reports.customers',
                    'nexopos.reports.inventory',
                    'nexopos.reports.payment-types',
                ],
                'childrens'     =>  [
                    'sales'  =>  [
                        'label' =>  __( 'Laporan Penjualan' ),
                        'permissions'   =>  [ 'nexopos.reports.sales' ],
                        'href'  =>  ns()->url( '/dashboard/reports/sales' )
                    ], 
                    'products-report'  =>  [
                        'label' =>  __( 'Penjualan Terbaik' ),
                        'permissions'   =>  [ 'nexopos.reports.products-report' ],
                        'href'  =>  ns()->url( '/dashboard/reports/products-report' )
                    ], 
                    'low-stock'  =>  [
                        'label' =>  __( 'Laporan Stok Rendah' ),
                        'permissions'   =>  [ 'nexopos.reports.low-stock' ],
                        'href'  =>  ns()->url( '/dashboard/reports/low-stock' )
                    ], 
                    'sold-stock'  =>  [
                        'label' =>  __( 'Stok Terjual' ),
                        'href'  =>  ns()->url( '/dashboard/reports/sold-stock' )
                    ], 
                    'profit'  =>  [
                        'label' =>  __( 'Laba & Rugi' ),
                        'href'  =>  ns()->url( '/dashboard/reports/profit' )
                    ], 
                    'cash-flow'  =>  [
                        'label' =>  __( 'Arus Kas' ),
                        'permissions'   =>  [ 'nexopos.reports.cash_flow' ],
                        'href'  =>  ns()->url( '/dashboard/reports/cash-flow' )
                    ], 
                    'annulal-sales'  =>  [
                        'label' =>  __( 'Laporan Tahunan' ),
                        'permissions'   =>  [ 'nexopos.reports.yearly' ],
                        'href'  =>  ns()->url( '/dashboard/reports/annual-report' )
                    ], 
                    'payment-types'  =>  [
                        'label' =>  __( 'Penjualan per Pembayaran' ),
                        'permissions'   =>  [ 'nexopos.reports.payment-types' ],
                        'href'  =>  ns()->url( '/dashboard/reports/payment-types' )
                    ], 
                ]
            ],
            'settings'      =>  [
                'label' =>  __( 'Pengaturan' ),
                'icon'          =>  'la-cogs',
                'permissions'   =>  [ 'manage.options' ],
                'childrens'     =>  [
                    'general'   =>  [
                        'label' =>  __( 'Umum' ),
                        'href'  =>  ns()->url( '/dashboard/settings/general' )
                    ], 
                    'pos'       =>  [
                        'label' =>  __( 'POS' ),
                        'href'  =>  ns()->url( '/dashboard/settings/pos' )
                    ],  
                    'customers' =>  [
                        'label' =>  __( 'Pelanggan' ),
                        'href'  =>  ns()->url( '/dashboard/settings/customers' )
                    ], 
                    'supplies-delivery'     =>  [
                        'label' =>  __( 'Pengiriman & Persediaan' ),
                        'href'              =>  ns()->url( '/dashboard/settings/supplies-deliveries' )
                    ],
                    'orders'        =>  [
                        'label' =>  __( 'Pesanan' ),
                        'href'      =>  ns()->url( '/dashboard/settings/orders' )
                    ],
                    'accounting'    =>  [
                        'label' =>  __( 'Akuntansi' ),
                        'href'      =>  ns()->url( '/dashboard/settings/accounting' )
                    ],
                    'reports'       =>  [
                        'label' =>  __( 'Laporan' ),
                        'href'      =>  ns()->url( '/dashboard/settings/reports' )
                    ],
                    'invoice-settings'  =>  [
                        'label' =>  __( 'Pengaturan Faktur' ),
                        'href'          =>  ns()->url( '/dashboard/settings/invoice-settings' )
                    ],
                    'service-providers'     =>  [
                        'label' =>  __( 'Penyedia Layanan' ),
                        'href'              =>  ns()->url( '/dashboard/settings/service-providers' )
                    ],
                    'notifications'     =>  [
                        'label' =>  __( 'Notifikasi' ),
                        'href'          =>  ns()->url( '/dashboard/settings/notifications' )
                    ],
                    'workers'           =>  [
                        'label' =>  __( 'Pekerja' ),
                        'href'          =>  ns()->url( '/dashboard/settings/workers' ),
                    ],
                    'reset'         =>  [
                        'label' =>  __( 'Reset' ),
                        'href'      =>  ns()->url( '/dashboard/settings/reset' )
                    ]
                ]
            ],
        ];
    }

    /**
     * returns the list of available menus
     * @return Array of menus
     */
    public function getMenus()
    {
        $this->buildMenus();
        $this->menus    =   Hook::filter( 'ns-dashboard-menus', $this->menus );
        $this->toggleActive();
        return $this->menus;
    }

    /**
     * Will make sure active menu
     * is toggled
     * @return void
     */
    public function toggleActive()
    {
        foreach( $this->menus as $identifier => &$menu ) {
            if ( isset( $menu[ 'href' ] ) && $menu[ 'href' ] === url()->current() ) {
                $menu[ 'toggled' ]  =   true;
            }

            if ( isset( $menu[ 'childrens' ] ) ) {
                foreach( $menu[ 'childrens' ] as $subidentifier => &$submenu ) {
                    if ( $submenu[ 'href' ] === url()->current() ) {
                        $menu[ 'toggled' ]      =   true;
                        $submenu[ 'active' ]    =   true;
                    }
                }
            }
        }
    }
}