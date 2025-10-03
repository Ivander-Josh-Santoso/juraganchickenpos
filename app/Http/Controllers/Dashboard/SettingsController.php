<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\DashboardController;
use App\Http\Requests\SettingsRequest;
use App\Services\CrudService;
use App\Services\Options;
use App\Services\SettingsPage;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use TorMorten\Eventy\Facades\Events as Hook;

class SettingsController extends DashboardController
{
    private $taxService;
    private $options;

    public function __construct(
        Options $options
    )
    {
        $this->options  =   $options;

        parent::__construct();
    }

    public function getSettings( $identifier )
    {
        ns()->restrict([ 'manage.options' ]);
        
        switch( $identifier ) {
            case 'customers'; return $this->customersSettings(); break;
            case 'general'; return $this->generalSettings(); break;
            case 'invoices'; return $this->invoiceSettings(); break;
            case 'orders'; return $this->ordersSettings(); break;
            case 'pos'; return $this->posSettings(); break;
            case 'supplies-deliveries'; return $this->suppliesDeliveries(); break;
            case 'reports'; return $this->reportsSettings(); break;
            case 'service-providers'; return $this->serviceProviders(); break;
            case 'invoice-settings'; return $this->invoiceSettings(); break;
            case 'expenses-settings'; return $this->expenseSettings(); break;
            case 'reset'; return $this->resetSettings(); break;
            case 'notifications'; return $this->notificationsSettings(); break;
            case 'workers'; return $this->workersSettings(); break;
            case 'accounting'; return $this->accountingSettings(); break;
            default : return $this->handleDefaultSettings( $identifier );break;
        }
    }

    public function handleDefaultSettings( $identifier )
    {
        $settings       =   Hook::filter( 'ns.settings', false, $identifier );

        if ( $settings instanceof SettingsPage ) {
            return $settings->renderForm();
        }

        return abort( 404, __( 'Halaman Pengaturan Tidak Ditemukan' ) );
    }

    /**
     * Get settings form using the identifier
     * @param string identifier
     * @return array
     */
    public function getSettingsForm( $identifier )
    {
        $settings       =   Hook::filter( 'ns.settings', false, $identifier );

        if ( $settings instanceof SettingsPage ) {
            return $settings->getForm();
        }

        throw new Exception( __( 'Tidak dapat menginisialisasi halaman pengaturan. Identifier "'. $identifier . ', tidak mengembalikan instance SettingsPage."' ) );
    }

    public function customersSettings()
    {
        return $this->view( 'pages.dashboard.settings.customers', [
            'title'     =>      __( 'Pengaturan Pelanggan' ),
            'description'   =>  __( 'Konfigurasikan pengaturan pelanggan pada aplikasi.' )
        ]);
    }

    public function generalSettings()
    {
        return $this->view( 'pages.dashboard.settings.general', [
            'title'     =>      __( 'Pengaturan Umum' ),
            'description'   =>  __( 'Konfigurasikan pengaturan umum aplikasi.' )
        ]);
    }

    public function expenseSettings()
    {
        return $this->view( 'pages.dashboard.settings.expenses', [
            'title'     =>      __( 'Pengaturan Pengeluaran' ),
            'description'   =>  __( 'Konfigurasikan pengaturan pengeluaran aplikasi.' )
        ]);
    }

    public function notificationsSettings()
    {
        return $this->view( 'pages.dashboard.settings.notifications', [
            'title'     =>      __( 'Pengaturan Notifikasi' ),
            'description'   =>  __( 'Konfigurasikan pengaturan notifikasi aplikasi.' )
        ]);
    }

    public function accountingSettings()
    {
        return $this->view( 'pages.dashboard.settings.accounting', [
            'title'         =>  __( 'Pengaturan Akuntansi' ),
            'description'   =>  __( 'Konfigurasikan pengaturan akuntansi aplikasi.' )
        ]);
    }

    public function invoiceSettings()
    {
        return $this->view( 'pages.dashboard.settings.invoices', [
            'title'     =>      __( 'Pengaturan Faktur' ),
            'description'   =>  __( 'Konfigurasikan pengaturan faktur.' )
        ]);
    }

    public function ordersSettings()
    {
        return $this->view( 'pages.dashboard.settings.orders', [
            'title'     =>      __( 'Pengaturan Pesanan' ),
            'description'   =>  __( 'Konfigurasikan pengaturan pesanan.' )
        ]);
    }

    public function posSettings()
    {
        return $this->view( 'pages.dashboard.settings.pos', [
            'title'     =>      __( 'Pengaturan POS' ),
            'description'   =>  __( 'Konfigurasikan pengaturan point of sale (POS).' )
        ]);
    }

    public function suppliesDeliveries()
    {
        return $this->view( 'pages.dashboard.settings.supplies-deliveries', [
            'title'     =>      __( 'Pengaturan Persediaan & Pengiriman' ),
            'description'   =>  __( 'Konfigurasikan pengaturan persediaan dan pengiriman.' )
        ]);
    }

    public function reportsSettings()
    {
        return $this->view( 'pages.dashboard.settings.reports', [
            'title'     =>      __( 'Pengaturan Laporan' ),
            'description'   =>  __( 'Konfigurasikan laporan.' )
        ]);
    }

    public function resetSettings()
    {
        /**
         * @temp
         */
        if ( Auth::user()->role->namespace !== 'admin' ) {
            throw new Exception( __( 'Akses Ditolak' ) );
        }

        return $this->view( 'pages.dashboard.settings.reset', [
            'title'     =>      __( 'Reset Pengaturan' ),
            'description'   =>  __( 'Reset data dan aktifkan mode demo.' )
        ]);
    }

    public function serviceProviders()
    {
        return $this->view( 'pages.dashboard.settings.service-providers', [
            'title'     =>      __( 'Pengaturan Penyedia Layanan' ),
            'description'   =>  __( 'Konfigurasikan pengaturan penyedia layanan.' )
        ]);
    }

    public function workersSettings()
    {
        return $this->view( 'pages.dashboard.settings.workers', [
            'title'     =>      __( 'Pengaturan Pekerja' ),
            'description'   =>  __( 'Konfigurasikan pengaturan pekerja.' )
        ]);
    }

    public function saveSettingsForm( SettingsRequest $request, $identifier )
    {
        ns()->restrict([ 'manage.options' ]);

        $resource   =   Hook::filter( 'ns.settings', false, $identifier );
        
        if ( ! $resource instanceof SettingsPage ) {
            throw new Exception( sprintf( 
                __( '%s bukan instance dari "%s".' ),
                $identifier,
                SettingsPage::class
            ) );
        }
        
        return $resource->saveForm( $request );
    }
}
