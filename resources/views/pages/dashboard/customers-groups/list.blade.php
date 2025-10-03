@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body' )
<div>
    @include( Hook::filter( 'ns-dashboard-header', '../common/dashboard-header' ) )
    <div id="dashboard-content" class="px-4">
        <div class="page-inner-header mb-4">
            <h3 class="text-3xl text-gray-800 font-bold">{{ __( 'Kelola Grup Pelanggan' ) }}</h3>
            <p class="text-gray-600">{{ __( 'Buat grup untuk mengelompokkan pelanggan' ) }}</p>
        </div>
        <ns-crud 
            src="{{ ns()->url( 'api/nexopos/v4/crud/ns.customers-groups' ) }}" 
            create-url="{{ ns()->url( 'dashboard/customers/groups/create' ) }}"
            id="crud-table-body">
            <template v-slot:bulk-label>{{ __( 'Aksi Massal' ) }}</template>
        </ns-crud>
    </div>
</div>
@endsection