<?php
use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $medias                 =   new Permission;
    $medias->name           =   __( 'Unggah Media' );
    $medias->namespace      =   'nexopos.upload.medias';
    $medias->description    =   __( 'Memberi izin kepada pengguna untuk mengunggah media.' );
    $medias->save();

    $medias                 =   new Permission;
    $medias->name           =   __( 'Lihat Media' );
    $medias->namespace      =   'nexopos.see.medias';
    $medias->description    =   __( 'Memberi izin kepada pengguna untuk melihat media.' );
    $medias->save();

    $medias                 =   new Permission;
    $medias->name           =   __( 'Hapus Media' );
    $medias->namespace      =   'nexopos.delete.medias';
    $medias->description    =   __( 'Memberi izin kepada pengguna untuk menghapus media.' );
    $medias->save();

    $medias                 =   new Permission;
    $medias->name           =   __( 'Perbarui Media' );
    $medias->namespace      =   'nexopos.update.medias';
    $medias->description    =   __( 'Memberi izin kepada pengguna untuk memperbarui media yang telah diunggah.' );
    $medias->save();

}
