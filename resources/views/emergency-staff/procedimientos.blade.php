@extends('layouts.app')
@section('content')
@include('partials.procedimientos-area', [
    'area_label' => 'Emergencia',
    'back_route'  => 'emergency-staff.dashboard',
    'btn_class'   => 'bg-red-600 hover:bg-red-700',
    'ring_class'  => 'focus:ring-red-100 focus:border-red-300',
])
@endsection
