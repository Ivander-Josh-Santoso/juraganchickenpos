<?php
use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $expensesCategories                 =   new Permission;
    $expensesCategories->name           =   __( 'Buat Kategori Pengeluaran' );
    $expensesCategories->namespace      =   'nexopos.create.expenses-categories';
    $expensesCategories->description    =   __( 'Izinkan pengguna membuat kategori pengeluaran' );
    $expensesCategories->save();

    $expensesCategories                 =   new Permission;
    $expensesCategories->name           =   __( 'Hapus Kategori Pengeluaran' );
    $expensesCategories->namespace      =   'nexopos.delete.expenses-categories';
    $expensesCategories->description    =   __( 'Izinkan pengguna menghapus kategori pengeluaran' );
    $expensesCategories->save();

    $expensesCategories                 =   new Permission;
    $expensesCategories->name           =   __( 'Perbarui Kategori Pengeluaran' );
    $expensesCategories->namespace      =   'nexopos.update.expenses-categories';
    $expensesCategories->description    =   __( 'Izinkan pengguna memperbarui kategori pengeluaran' );
    $expensesCategories->save();

    $expensesCategories                 =   new Permission;
    $expensesCategories->name           =   __( 'Baca Kategori Pengeluaran' );
    $expensesCategories->namespace      =   'nexopos.read.expenses-categories';
    $expensesCategories->description    =   __( 'Izinkan pengguna membaca kategori pengeluaran' );
    $expensesCategories->save();
}
