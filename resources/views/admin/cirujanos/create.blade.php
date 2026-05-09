@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Nuevo Cirujano</h1>
            <p class="text-sm text-gray-500">Crear un nuevo cirujano en el sistema</p>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-lg">
            <form action="{{ route('admin.cirujanos.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="ci" class="block text-sm font-medium text-gray-700 mb-1">Cédula de Identidad *</label>
                    <input type="number" name="ci" id="ci" value="{{ old('ci') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('admin.cirujanos.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Crear Cirujano</button>
                </div>
            </form>
        </div>
    </div>
@endsection
