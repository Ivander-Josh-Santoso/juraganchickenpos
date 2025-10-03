<?php

use App\Classes\Hook;
use App\Classes\Output;
?>
@extends('layout.base')

@section('layout.base.body')
<div id="page-container" class="h-screen w-full flex items-center justify-center bg-gray-300 overflow-y-auto">
    <div class="container mx-auto px-4 md:px-0 flex flex-col md:flex-row items-center justify-center gap-8">

        {{-- Login Box --}}
        <div class="w-full md:w-1/2 lg:w-2/5 bg-white rounded-lg shadow-md p-6">
            {{-- Logo / Title --}}
            <div class="mb-6 text-center">
                <h1 class="text-3xl font-bold text-blue-500 leading-tight">
                    Juragan Chicken<br><span class="tracking-wide">POS</span>
                </h1>
            </div>



            {{-- Session Message & Form --}}
            <x-session-message></x-session-message>
            {!! Hook::filter('ns.before-login-fields', new Output) !!}
            @include('/common/auth/sign-in-form')
            {!! Hook::filter('ns.after-login-fields', new Output) !!}
        </div>

        {{-- Gambar Ayam --}}
        <div class="w-full md:w-1/2 lg:w-2/5 flex justify-center">
            <img src="{{ asset('images/wallpaper.png') }}" alt="Ayam Juragan" class="w-full max-w-[350px] h-auto object-contain">
        </div>
        
    </div>
</div>
@endsection

@section('layout.base.footer')
    @parent
    {!! Hook::filter('ns-login-footer', new Output) !!}
    <script src="{{ asset(ns()->isProduction() ? 'js/auth.min.js' : 'js/auth.js') }}"></script>
@endsection
