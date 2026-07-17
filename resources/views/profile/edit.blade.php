@extends('layouts.app')

@section('content')

{{-- Toast flotante --}}
@php
    $toastType = null;
    $toastMsg  = null;

    if (session('status') === 'profile-updated') {
        $toastType = 'success';
        $toastMsg  = 'Perfil actualizado correctamente.';
    } elseif (session('status') === 'password-updated') {
        $toastType = 'success';
        $toastMsg  = 'Contraseña actualizada correctamente.';
    } elseif ($errors->getBag('default')->any()) {
        $toastType = 'error';
        $toastMsg  = 'Error al guardar el perfil: ' . $errors->getBag('default')->first();
    } elseif ($errors->updatePassword->any()) {
        $toastType = 'error';
        $toastMsg  = 'Error al actualizar la contraseña: ' . $errors->updatePassword->first();
    }
@endphp

@if($toastType)
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-3 rounded-lg shadow-lg text-white text-sm font-medium {{ $toastType === 'success' ? 'bg-green-500' : 'bg-red-500' }}"
    style="display: none;"
>
    @if($toastType === 'success')
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    @else
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    @endif
    {{ $toastMsg }}
</div>
@endif

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div> -->
    </div>
</div>
@endsection
