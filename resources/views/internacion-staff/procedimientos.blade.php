@extends('layouts.app')
@section('content')
@include('partials.procedimientos-area', [
    'area_label' => 'Internación',
    'back_route'  => 'internacion-staff.dashboard',
    'btn_class'   => 'bg-indigo-600 hover:bg-indigo-700',
    'ring_class'  => 'focus:ring-indigo-100 focus:border-indigo-300',
])
@endsection
