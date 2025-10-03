<?php

use App\Services\Helper;

return [
    'label' =>  __( 'Registrasi' ),
    'fields'    =>  [
            [
            'type'          =>  'switch',
            'name'          =>  'ns_notifications_registrations_notify_administrators',
            'label'         =>  __( 'Beritahu Administrator' ),
            'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
            'value'         =>  intval( $options->get( 'ns_notifications_registrations_notify_administrators' ) ),
            'description'   =>  __( 'Akan memberitahu administrator setiap kali pengguna baru mendaftar.' )
        ], [
            'type'          =>  'text',
            'name'          =>  'ns_notifications_registrations_administrator_email_title',
            'label'         =>  __( 'Judul Notifikasi Administrator' ),
            'value'         =>  $options->get( 'ns_notifications_registrations_administrator_email_title' ),
            'description'   =>  __( 'Tentukan judul email yang dikirim ke administrator.' )
        ], [
            'type'          =>  'textarea',
            'name'          =>  'ns_notifications_registrations_administrator_email_body',
            'label'         =>  __( 'Isi Notifikasi Administrator' ),
            'value'         =>  $options->get( 'ns_notifications_registrations_administrator_email_body' ),
            'description'   =>  __( 'Tentukan isi pesan yang akan dikirim ke administrator.' )
        ], [
            'type'          =>  'switch',
            'name'          =>  'ns_notifications_registrations_notify_user',
            'label'         =>  __( 'Beritahu Pengguna' ),
            'options'       =>  Helper::kvToJsOptions([ __( 'Tidak' ), __( 'Ya' ) ]),
            'value'         =>  intval( $options->get( 'ns_notifications_registrations_notify_user' ) ),
            'description'   =>  __( 'Beritahu pengguna saat akunnya berhasil dibuat.' )
        ], [
            'type'          =>  'text',
            'name'          =>  'ns_notifications_registrations_user_email_title',
            'label'         =>  __( 'Judul Registrasi Pengguna' ),
            'value'         =>  $options->get( 'ns_notifications_registrations_user_email_title' ),
            'description'   =>  __( 'Tentukan judul email yang dikirim ke pengguna saat akunnya dibuat dan aktif.' )
        ], [
            'type'          =>  'textarea',
            'name'          =>  'ns_notifications_registrations_user_email_body',
            'label'         =>  __( 'Isi Registrasi Pengguna' ),
            'value'         =>  $options->get( 'ns_notifications_registrations_user_email_body' ),
            'description'   =>  __( 'Tentukan isi email yang dikirim ke pengguna saat akunnya dibuat dan aktif.' )
        ], [
            'type'          =>  'text',
            'name'          =>  'ns_notifications_registrations_user_activate_title',
            'label'         =>  __( 'Judul Aktivasi Pengguna' ),
            'value'         =>  $options->get( 'ns_notifications_registrations_user_activate_title' ),
            'description'   =>  __( 'Tentukan judul email yang dikirim ke pengguna.' )
        ], [
            'type'          =>  'textarea',
            'name'          =>  'ns_notifications_registrations_user_activate_body',
            'label'         =>  __( 'Isi Aktivasi Pengguna' ),
            'value'         =>  $options->get( 'ns_notifications_registrations_user_activate_body' ),
            'description'   =>  __( 'Tentukan email yang akan dikirim ke pengguna saat akunnya memerlukan aktivasi.' )
        ],
    ]
];
