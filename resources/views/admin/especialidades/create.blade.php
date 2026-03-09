<x-app-layout>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Crear especialidad</h1>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
            <form method="POST" action="{{ route('admin.especialidades.store') }}">
                @include('admin.especialidades.form', ['especialidad' => null])

                <div class="mt-6 flex gap-3 justify-end">
                    <a href="{{ route('admin.especialidades.index') }}" class="px-4 py-2 border rounded-lg">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
