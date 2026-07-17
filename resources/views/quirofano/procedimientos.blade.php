@extends('layouts.app')
@section('content')
@include('partials.procedimientos-area', [
    'area_label' => 'Cirugía',
    'back_route'  => 'quirofano.index',
    'btn_class'   => 'bg-teal-600 hover:bg-teal-700',
    'ring_class'  => 'focus:ring-teal-100 focus:border-teal-300',
])
@endsection
