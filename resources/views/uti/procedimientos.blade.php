@extends('layouts.app')
@section('content')
@include('partials.procedimientos-area', [
    'area_label' => 'UTI — Terapia Intensiva',
    'back_route'  => 'uti.dashboard',
    'btn_class'   => 'bg-cyan-600 hover:bg-cyan-700',
    'ring_class'  => 'focus:ring-cyan-100 focus:border-cyan-300',
])
@endsection
