@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body' )
<div class="h-full flex-auto flex flex-col">
    @include( Hook::filter( 'ns-dashboard-header', '../common/dashboard-header' ) )
    <div class="px-4 flex-auto flex flex-col" id="dashboard-content">
        <div class="page-inner-header mb-4">
            <h3 class="text-3xl text-gray-800 font-bold">{{ $title ?? __( 'Halaman Tak Bernama' ) }}</h3>
            <p class="text-gray-600">{{ $description ?? __( 'Tidak Ada Deskripsi' ) }}</p>
        </div>
        <ns-procurement
            submit-url="{{ ns()->url( '/api/nexopos/v4/procurements' ) }}"
            src="{{ ns()->url( '/api/nexopos/v4/forms/ns.procurement' ) }}"
            return-url="{{ ns()->url( '/dashboard/procurements' ) }}">
            <template v-slot:title>{{ __( 'Nama Pengadaan' ) }}</template>
            <template v-slot:error-no-products>{{ __( 'Tidak dapat melanjutkan, tidak ada produk yang diberikan.' ) }}</template>
            <template v-slot:error-invalid-products>{{ __( 'Tidak dapat melanjutkan, satu atau lebih produk tidak valid.' ) }}</template>
            <template v-slot:error-invalid-form>{{ __( 'Tidak dapat melanjutkan, formulir pengadaan tidak valid.' ) }}</template>
            <template v-slot:error-no-submit-url>{{ __( 'Tidak dapat melanjutkan, URL submit tidak disediakan.' ) }}</template>
            <template v-slot:search-placeholder>{{ __( 'SKU, Barcode, Nama Produk.' ) }}</template>
        </ns-procurement>
    </div>
</div>
@endsection
