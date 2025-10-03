<?php
/**
 * NexoPOS Controller
 * @since  1.0
**/

namespace App\Http\Controllers\Dashboard;

use App\Classes\Hook;
use App\Classes\Output;
use App\Crud\ProductHistoryCrud;
use App\Crud\ProductUnitQuantitiesCrud;
use App\Exceptions\NotAllowedException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\DashboardController;
use App\Models\ProcurementProduct;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductHistory;
use App\Models\Unit;
use App\Models\ProductUnitQuantity;
use App\Services\CrudService;
use App\Services\DateService;
use App\Services\Helper;
use App\Services\ProductService;
use App\Services\Options;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ProductsController extends DashboardController
{
    /** @var ProductService */
    protected $productService;

    /**
     * @var DateService
     */
    protected $dateService;

    public function __construct( 
        ProductService $productService,
        DateService $dateService
    )
    {
        parent::__construct();

        $this->productService   =   $productService;
        $this->dateService      =   $dateService;
    }

    public function saveProduct( Request $request )
    {
        $primary    =   collect( $request->input( 'variations' ) )
            ->filter( fn( $variation ) => isset( $variation[ '$primary' ] ) )
            ->first();

        $source                                 =   $primary;
        $units                                  =   $primary[ 'units' ];

        /**
         * this is made to ensure the array 
         * provided aren't flatten
         */
        unset( $primary[ 'units' ] );
        unset( $primary[ 'images' ] );

        $primary[ 'identification' ][ 'name' ]          =   $request->input( 'name' );
        $primary                                        =    Helper::flatArrayWithKeys( $primary )->toArray();
        $primary[ 'product_type' ]                      =   'product';

        /**
         * let's restore the fields before
         * storing that.
         */
        $primary[ 'images' ]        =   $source[ 'images' ];
        $primary[ 'units' ]         =   $source[ 'units' ];
        
        unset( $primary[ '$primary' ] );

        /**
         * As foreign fields aren't handled with 
         * they are complex (array), this methods allow
         * external script to reinject those complex fields.
         */
        $primary        =   Hook::filter( 'ns-create-products-inputs', $primary, $source );

        /**
         * the method "create" is capable of 
         * creating either a product or a variable product
         */
        return $this->productService->create( $primary );
    }

    /**
     * returns a list of available 
     * product
     * @return array
     */
    public function getProduts()
    {
        return $this->productService->getProducts();
    }

    /**
     * Update a product using
     * a provided id
     * @param Request
     * @param int product id
     * @return array
     */
    public function updateProduct( Request $request, Product $product )
    {
        $primary    =   collect( $request->input( 'variations' ) )
            ->filter( fn( $variation ) => isset( $variation[ '$primary' ] ) )
            ->first();

        $source                                 =   $primary;
        $units                                  =   $primary[ 'units' ];
        
        /**
         * this is made to ensure the array 
         * provided aren't flatten
         */
        unset( $primary[ 'images' ] );
        unset( $primary[ 'units' ] );

        $primary[ 'identification' ][ 'name' ]          =   $request->input( 'name' );
        $primary                                        =    Helper::flatArrayWithKeys( $primary )->toArray();
        $primary[ 'product_type' ]                      =   'product';

        /**
         * let's restore the fields before
         * storing that.
         */
        $primary[ 'images' ]                =   $source[ 'images' ];
        $primary[ 'units' ]                 =   $source[ 'units' ];
        
        unset( $primary[ '$primary' ] );

        /**
         * As foreign fields aren't handled with 
         * they are complex (array), this methods allow
         * external script to reinject those complex fields.
         */
        $primary        =   Hook::filter( 'ns-update-products-inputs', $primary, $source, $product );

        /**
         * the method "create" is capable of 
         * creating either a product or a variable product
         */
        return $this->productService->update( $product, $primary );
    }

    /**
     * @todo must be extracted to a service
     */
    public function searchProduct( Request $request )
    {
        return $this->productService->searchProduct( $request->input( 'search' ) );
    }

    public function refreshPrices( $id )
    {
        $product    =   $this->productService->get( $id );
        $this->productService->refreshPrices( $product );
        
        return [
            'status'    =>  'success',
            'message'   =>  __( 'Harga produk telah diperbarui.' ),
            'data'      =>  compact( 'product' )
        ];
    }

    public function reset( $identifier )
    {
        $product        =   $this->productService->getProductUsingArgument(
            request()->query( 'as' ) ?? 'id',
            $identifier
        );
        
        return $this->productService->resetProduct( $product );
    }

    /**
     * return the full history of a product
     * @param int product id
     * @return array
     */
    public function history( $identifier )
    {
        $product        =   $this->productService->getProductUsingArgument(
            request()->query( 'as' ) ?? 'id',
            $identifier
        );

        return $this->productService->getProductHistory( 
            $product->id
        );
    }

    public function units( $identifier )
    {
        $product        =   $this->productService->getProductUsingArgument(
            request()->query( 'as' ) ?? 'id',
            $identifier
        );
        
        return $this->productService->getUnitQuantities( 
            $product->id
        );
    }

    public function getUnitQuantities( Product $product )
    {
        return $this->productService->getProductUnitQuantities( $product );
    }

    /**
     * delete a product
     * @param int product_id
     * @return array reponse
     */
    public function deleteProduct( $identifier )
    {
        $product        =   $this->productService->getProductUsingArgument(
            request()->query( 'as' ) ?? 'id',
            $identifier
        );

        return $this->productService->deleteProduct( $product );
    }

    /**
     * Return a single product ig that exists
     * with his variations
     * @param string|int filter
     * @return array found product
     */
    public function singleProduct( $identifier )
    {
        return $this->productService->getProductUsingArgument(
            request()->query( 'as' ) ?? 'id',
            $identifier
        );
    }

    /**
     * return all available variations
     * @return array
     */
    public function getAllVariations()
    {
        return $this->productService->getProductVariations();
    }

    /**
     * delete all available product variations
     */
    public function deleteAllVariations()
    {
        return $this->productService->deleteVariations();
    }

    public function deleteAllProducts()
    {
        return $this->productService->deleteAllProducts();        
    }

    public function getProductVariations( $identifier )
    {
        $product    =   $this->productService->getProductUsingArgument(
            request()->query( 'as' ) ?? 'id',
            $identifier
        );

        return $product->variations;
    }

    /**
     * delete a single variation product
     * @param int product id
     * @param int variation id
     * @return array status of the operation
     */
    public function deleteSingleVariation( $product_id, int $variation_id )
    {
        /**
         * @todo consider registering an event for 
         * catching when a single is about to be delete
         */

        /** @var Product */
        $product    =   $this->singleProduct( $product_id );

        $results    =   $product->variations->map( function( $variation ) use ( $variation_id ) {
            if ( $variation->id === $variation_id ) {
                $variation->delete();
                return 1;
            }
            return 0;
        });

        $opResult   =   $results->reduce( function( $before, $after ) {
            return $before + $after;
        });

        return floatval( $opResult ) > 0 ? [
            'status'        =>      'success',
            'message'       =>      __( 'Varian tunggal berhasil dihapus.' )
        ] : [
            'status'        =>      'failed',
            'message'       =>      sprintf( __( 'Varian tidak dihapus karena mungkin tidak ada atau tidak terkait dengan produk induk "%s".' ), $product->name )
        ];
    }

    /**
     * Create a single product
     * variation
     * @param int product id (parent)
     * @param Request data
     * @return array
     */
    public function createSingleVariation( $product_id, Request $request )
    {
        $product    =   $this->productService->get( $product_id );

        return $this->productService->createProductVariation( 
            $product, 
            $request->all() 
        );
    }

    public function editSingleVariation( $parent_id, $variation_id, Request $request )
    {
        $parent     =   $this->productService->get( $parent_id );
        return $this->productService->updateProductVariation( $parent, $variation_id, $request->all() );
    }

    public function listProducts()
    {
        ns()->restrict([ 'nexopos.read.products' ]);

        Hook::addFilter( 'ns-crud-footer', function( Output $output ) {
            $output->addView( 'pages.dashboard.products.quantity-popup' );
            return $output;
        });

        return $this->view( 'pages.dashboard.crud.table', [
            'title'         =>      __( 'Daftar Produk' ),
            'createUrl'     =>  ns()->url( '/dashboard/products/create' ),
            'description'   =>  __( 'Daftar semua produk yang tersedia dalam sistem' ),
            'src'           =>  ns()->url( '/api/nexopos/v4/crud/ns.products' ),
        ]);
    }

    public function editProduct( Product $product )
    {
        ns()->restrict([ 'nexopos.update.products' ]);

        return $this->view( 'pages.dashboard.products.create', [
            'title'         =>  __( 'Edit Produk' ),
            'description'   =>  __( 'Lakukan modifikasi pada produk' ),
            'submitUrl'     =>  ns()->url( '/api/nexopos/v4/products/' . $product->id ),
            'returnUrl'     =>  ns()->url( '/dashboard/products' ),
            'unitsUrl'      =>  ns()->url( '/api/nexopos/v4/units-groups/{id}/units' ),
            'submitMethod'  =>  'PUT',
            'src'           =>  ns()->url( '/api/nexopos/v4/crud/ns.products/form-config/' . $product->id ),
        ]);
    }

    public function createProduct()
    {
        ns()->restrict([ 'nexopos.create.products' ]);

        return $this->view( 'pages.dashboard.products.create', [
            'title'         =>  __( 'Buat Produk Baru' ),
            'description'   =>  __( 'Tambahkan produk baru ke dalam sistem' ),
            'submitUrl'     =>  ns()->url( '/api/nexopos/v4/products' ),
            'returnUrl'     =>  ns()->url( '/dashboard/products' ),
            'unitsUrl'      =>  ns()->url( '/api/nexopos/v4/units-groups/{id}/units' ),
            'src'           =>  ns()->url( '/api/nexopos/v4/crud/ns.products/form-config' ),
        ]);
    }

    /**
     * Renders the crud table for the product
     * units
     * @return View
     */
    public function productUnits( Product $product )
    {
        return ProductUnitQuantitiesCrud::table([
            'queryParams'   =>  [
                'product_id'    =>  $product->id
            ]
        ]);
    }

    /**
     * render the crud table for the product
     * history
     * @return View
     */
    public function productHistory( $identifier )
    {
        Hook::addFilter( 'ns-crud-footer', function( Output $output, $identifier ) {
            $output->addView( 'pages.dashboard.products.history' );
            return $output;
        }, 10, 2 );

        return ProductHistoryCrud::table([
            'queryParams'    =>  [
                'product_id'    =>  $identifier
            ]
        ]);
    }

    public function showStockAdjustment()
    {
        return $this->view( 'pages.dashboard.products.stock-adjustment', [
            'title'         =>  __( 'Penyesuaian Stok' ),
            'description'   =>  __( 'Sesuaikan stok produk yang sudah ada.' ),
            'actions'       =>  Helper::kvToJsOptions([
                ProductHistory::ACTION_ADDED        =>  __( 'Tambah' ),
                ProductHistory::ACTION_DELETED      =>  __( 'Hapus' ),
                ProductHistory::ACTION_DEFECTIVE    =>  __( 'Rusak' ),
                ProductHistory::ACTION_LOST         =>  __( 'Hilang' ),
            ])
        ]);
    }

    public function getUnitQuantity( Product $product, Unit $unit )
    {
        $quantity   =   $this->productService->getUnitQuantity( $product->id, $unit->id );

        if ( $quantity instanceof ProductUnitQuantity ) {
            return $quantity;
        }

        throw new Exception( __( 'Stok tidak tersedia untuk produk yang diminta.' ) );
    }

    public function deleteUnitQuantity( ProductUnitQuantity $unitQuantity )
    {
        ns()->restrict([ 'nexopos.delete.products-units', 'nexopos.make.products-adjustments' ]);

        if ( $unitQuantity->quantity > 0 ) {
            $this->productService->stockAdjustment( ProductHistory::ACTION_DELETED, [
                'unit_price'    =>  $unitQuantity->sale_price,
                'unit_id'       =>  $unitQuantity->unit_id,
                'product_id'    =>  $unitQuantity->product_id,
                'quantity'      =>  $unitQuantity->quantity,
            ]);
        }

        $unitQuantity->delete();

        return [
            'status'    =>  'success',
            'message'   =>  __( 'Jumlah unit produk telah dihapus.' )
        ];
    }

    public function createAdjustment( Request $request )
    {
        ns()->restrict([ 'nexopos.make.products-adjustments' ]);

        $validator =        Validator::make( $request->all(), [
            'products'  =>  'required'
        ]);

        if ( $validator->fails() ) {
            throw new Exception( __( 'Tidak dapat melanjutkan karena permintaan tidak valid.' ) );
        }

        $results        =   [];

        /**
         * We need to make sure the action
         * made are actually supported.
         */
        foreach( $request->input( 'products' ) as $unit ) {
            if ( 
                ! in_array( $unit[ 'adjust_action' ], ProductHistory::STOCK_INCREASE ) &&
                ! in_array( $unit[ 'adjust_action' ], ProductHistory::STOCK_REDUCE )
            ) {
                throw new Exception( sprintf( __( 'Aksi tidak didukung untuk produk %s.' ), $unit[ 'name' ] ) );
            }
        }

        /**
         * now we can adjust the stock of the items
         */
        foreach( $request->input( 'products' ) as $product ) {
            $results[]          =   $this->productService->stockAdjustment( $product[ 'adjust_action' ], [
                'unit_price'                =>  $product[ 'adjust_unit' ][ 'sale_price' ],
                'unit_id'                   =>  $product[ 'adjust_unit' ][ 'unit_id' ],
                'procurement_product_id'    =>  $product[ 'procurement_product_id' ] ?? null,
                'product_id'                =>  $product[ 'id' ],
                'quantity'                  =>  $product[ 'adjust_quantity' ],
                'description'               =>  $product[ 'adjust_reason' ] ?? '',
            ]);
        }

        return [
            'status'    =>  'success',
            'message'   =>  __( 'Stok telah berhasil disesuaikan.' ),
            'data'      =>  $results
        ];
    }

    public function searchUsingArgument( $reference )
    {
        $procurementProduct     =   ProcurementProduct::barcode( $reference )->first();
        $productUnitQuantity    =   ProductUnitQuantity::barcode( $reference )->with( 'unit' )->first();
        $product                =   Product::barcode( $reference )
            ->searchable()
            ->first();

        if ( $procurementProduct instanceof ProcurementProduct ) {
            $product    =   $procurementProduct->product;
            
            /**
             * check if the product has expired
             * and the sales are disallowed.
             */
            if ( 
                $this->dateService->copy()->greaterThan( $procurementProduct->expiration_date ) && 
                $product->expires && 
                $product->on_expiration === Product::EXPIRES_PREVENT_SALES ) {
                    throw new NotAllowedException( __( 'Tidak dapat menambah produk ke keranjang karena sudah kedaluwarsa.' ) );
                }

            /**
             * We need to add  a reference of the procurement product
             * in order to deplete the available quantity accordingly.
             * Will also be helpful to track how products are sold.
             */
            $product->procurement_product_id       =   $procurementProduct->id;

        } else if ( $productUnitQuantity instanceof ProductUnitQuantity ) {

            /**
             * if a product unit quantity is loaded. Then we make sure to return the parent
             * product with the selected unit quantity.
             */
            $productUnitQuantity->load( 'unit' );
            
            $product      =   Product::find( $productUnitQuantity->product_id );
            $product->load( 'unit_quantities.unit' );
            $product->selectedUnitQuantity  =   $productUnitQuantity;

        } else if ( $product instanceof Product ) {

            $product->load( 'unit_quantities.unit' );

            if ( $product->accurate_tracking ) {
                throw new NotAllowedException( __( 'Tidak dapat menambah produk yang memiliki pelacakan akurat menggunakan barcode biasa.' ) );
            }
        }        

        if ( $product instanceof Product ) {
            return [
                'type'      =>  'product',
                'product'   =>  $product
            ];
        }

        throw new NotFoundException( __( 'Tidak ada produk yang cocok dengan permintaan saat ini.' ) );
    }

    public function printLabels()
    {
        return $this->view( 'pages.dashboard.products.print-labels', [
            'title'         =>  __( 'Cetak Label' ),
            'description'   =>  __( 'Sesuaikan dan cetak label produk.' ),
        ]);
    }

    public function getProcuredProducts( Product $product )
    {
        return $product->procurementHistory->map( function( $procurementProduct ) {
            $procurementProduct->procurement    =   $procurementProduct->procurement()->select( 'name' )->first();
            return $procurementProduct;
        });
    }
}
