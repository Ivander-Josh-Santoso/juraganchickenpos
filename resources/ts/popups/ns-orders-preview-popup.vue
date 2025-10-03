<script>
import { nsHttpClient, nsSnackBar } from "@/bootstrap";
import { nsCurrency } from "@/filters/currency";
import { forkJoin } from "rxjs";
import nsOrderRefund from "@/pages/dashboard/orders/ns-order-refund.vue";
import nsPromptPopupVue from "./ns-prompt-popup.vue";
import nsPosConfirmPopupVue from "./ns-pos-confirm-popup.vue";
import nsOrderPayment from "@/pages/dashboard/orders/ns-order-payment.vue";
import nsOrderDetails from "@/pages/dashboard/orders/ns-order-details.vue";
import nsOrderInstalments from "@/pages/dashboard/orders/ns-order-instalments.vue";
import { __ } from "@/libraries/lang";

/**
 * @var {ExtendedVue}
 */
const nsOrderPreviewPopup   =   {
    filters: {
        nsCurrency
    },
    name: 'ns-preview-popup',
    data() {
        return {
            active: 'details',
            order: new Object,
            products: [],
            payments: [],
        }
    },
    components: {
        nsOrderRefund,
        nsOrderPayment,
        nsOrderDetails,
        nsOrderInstalments,
    },
    computed: {
        isVoidable() {
            return [ 'paid', 'partially_paid', 'unpaid' ].includes( this.order.payment_status );
        },
        isDeleteAble() {
            return [ 'hold' ].includes( this.order.payment_status );
        },
    },
    methods: {
        __,
        closePopup() {
            this.$popup.close();
        },
        setActive( active ) {
            this.active     =   active;
        },
        refresh() {
            this.$popupParams.component.$emit( 'updated' );
            this.loadOrderDetails( this.$popupParams.order.id );
        },
        printOrder() {
            const order     =   this.$popupParams.order;

            nsHttpClient.get( `/api/nexopos/v4/orders/${order.id}/print/receipt` )
                .subscribe( result => {
                    nsSnackBar.success( result.message ).subscribe();
                });
        },
        loadOrderDetails( orderId ) {
            forkJoin([
                nsHttpClient.get( `/api/nexopos/v4/orders/${orderId}` ),
                nsHttpClient.get( `/api/nexopos/v4/orders/${orderId}/products` ),
                nsHttpClient.get( `/api/nexopos/v4/orders/${orderId}/payments` ),
            ])
                .subscribe( result => {
                    this.order              =   result[0];
                    this.products           =   result[1];
                    this.payments           =   result[2];
                });
        },
        deleteOrder() {
            Popup.show( nsPosConfirmPopupVue, {
                title: __( 'Konfirmasi Tindakan' ),
                message: __( 'Apakah Anda yakin ingin menghapus pesanan ini?' ),
                onAction: ( action ) => {
                    if ( action ) {
                        nsHttpClient.delete( `/api/nexopos/v4/orders/${this.$popupParams.order.id}` )
                            .subscribe( result => {
                                nsSnackBar.success( result.message ).subscribe();
                                this.refreshCrudTable();
                                this.closePopup();
                            }, error => {
                                nsSnackBar.error( error.message ).subscribe();
                            })
                    }
                }
            })
        },
        voidOrder() {
            Popup.show( nsPromptPopupVue, {
                title: __( 'Konfirmasi Tindakan' ),
                message: __( 'Pesanan ini akan dibatalkan. Tindakan ini akan dicatat. Pertimbangkan untuk memberikan alasan pembatalan.' ),
                onAction:  ( reason ) => {
                    if ( reason !== false ) {
                        nsHttpClient.post( `/api/nexopos/v4/orders/${this.$popupParams.order.id}/void`, { reason })
                            .subscribe( result => {
                                nsSnackBar.success( result.message ).subscribe();
                                this.refreshCrudTable();
                                this.closePopup();
                            }, error => {
                                nsSnackBar.error( error.message ).subscribe();
                            })
                    }
                }
            });
        },
        refreshCrudTable() {
            this.$popupParams.component.$emit( 'updated', true );
        }
    },
    watch: {
        active() {
            if ( this.active === 'details' ) {
                this.loadOrderDetails( this.$popupParams.order.id );
            }
        }
    },
    mounted() {
        this.loadOrderDetails( this.$popupParams.order.id );
        
        this.$popup.event.subscribe( action => {
            if ( action.event === 'click-overlay' ) {
                this.$popup.close();
            }
        })
    }
}

window.nsOrderPreviewPopup      =   nsOrderPreviewPopup;

export default nsOrderPreviewPopup;
</script>

<template>
    <div class="h-95vh w-95vw md:h-6/7-screen md:w-6/7-screen overflow-hidden shadow-xl bg-white flex flex-col">
        <div class="border-b border-gray-300 p-3 flex items-center justify-between">
            <div>
                <h3>{{ __( 'Opsi Pesanan' ) }}</h3>
            </div>
            <div>
                <ns-close-button @click="closePopup()"></ns-close-button>
            </div>
        </div>
        <div class="p-2 overflow-scroll bg-gray-100 flex flex-auto">
            <ns-tabs v-if="order.id" :active="active" @active="setActive( $event )">
                <!-- Rincian -->
                <ns-tabs-item :label="__( 'Rincian' )" identifier="details" class="overflow-y-auto">
                    <ns-order-details :order="order"></ns-order-details>
                </ns-tabs-item>

                <!-- Pembayaran -->
                <ns-tabs-item v-if="! [ 'order_void', 'hold', 'refunded', 'partially_refunded' ].includes( order.payment_status )" :label="__( 'Pembayaran' )" identifier="payments" class="overflow-y-auto">
                    <ns-order-payment @changed="refresh()" :order="order"></ns-order-payment>
                </ns-tabs-item>

                <!-- Refund -->
                <ns-tabs-item v-if="! [ 'order_void', 'hold', 'refunded' ].includes( order.payment_status )" :label="__( 'Pengembalian & Retur' )" identifier="refund" class="flex overflow-y-auto">
                    <ns-order-refund @changed="refresh()" :order="order"></ns-order-refund>
                </ns-tabs-item>

                <!-- Cicilan -->
                <ns-tabs-item v-if="[ 'partially_paid' ].includes( order.payment_status )" :label="__( 'Cicilan' )" identifier="instalments" class="flex overflow-y-auto">
                    <ns-order-instalments @changed="refresh()" :order="order"></ns-order-instalments>
                </ns-tabs-item>
            </ns-tabs>
            <div v-if="! order.id" class="h-full w-full flex items-center justify-center">
                <ns-spinner></ns-spinner>
            </div>
        </div> 
        <div class="p-2 flex justify-between border-t border-gray-200">
            <div>
                <ns-button v-if="isVoidable" @click="voidOrder()" type="danger">
                    <i class="las la-ban"></i>
                    {{ __( 'Batalkan' ) }}
                </ns-button>
                <ns-button v-if="isDeleteAble" @click="deleteOrder()" type="danger">
                    <i class="las la-trash"></i>
                    {{ __( 'Hapus' ) }}
                </ns-button>
            </div>
            <div>
                <!-- <ns-button @click="printOrder()" type="info">
                    <i class="las la-print"></i>
                    {{ __( 'Cetak' ) }}
                </ns-button> -->
            </div>
        </div>
    </div>
</template>
