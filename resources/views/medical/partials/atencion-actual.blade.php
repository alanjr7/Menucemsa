<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-8">
    <div class="flex justify-between items-start border-b pb-4">
        <div>
            <h3 class="text-blue-900 font-bold text-lg">Atención en Consulta</h3>
            <p class="text-gray-500 text-sm">López, María - HC-001235 - 32 años</p>
        </div>
        <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full border border-orange-200">
            ● En Proceso
        </span>
    </div>

    <div>
        <h4 class="font-bold text-gray-700 mb-4">Signos Vitales</h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="border rounded-lg p-3 bg-gray-50">
                <p class="text-[10px] text-gray-400 uppercase">Presión Arterial</p>
                <p class="text-lg font-semibold text-gray-800">120/80 <span class="text-xs text-gray-500">mmHg</span></p>
            </div>
            <div class="border rounded-lg p-3 bg-gray-50">
                <p class="text-[10px] text-gray-400 uppercase">Frecuencia Cardíaca</p>
                <p class="text-lg font-semibold text-gray-800">75 <span class="text-xs text-gray-500">lpm</span></p>
            </div>
            <div class="border rounded-lg p-3 bg-gray-50">
                <p class="text-[10px] text-gray-400 uppercase">Temperatura</p>
                <p class="text-lg font-semibold text-gray-800">36.5 <span class="text-xs text-gray-500">°C</span></p>
            </div>
            <div class="border rounded-lg p-3 bg-gray-50">
                <p class="text-[10px] text-gray-400 uppercase">Saturación O₂</p>
                <p class="text-lg font-semibold text-gray-800">98 <span class="text-xs text-gray-500">%</span></p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div>
            <label class="block font-bold text-gray-700 text-sm mb-2">Motivo de Consulta</label>
            <textarea class="w-full border-none bg-gray-50 rounded-lg focus:ring-2 focus:ring-blue-500 italic text-gray-600" rows="2">Dolor abdominal hace 2 días...</textarea>
        </div>
        <div>
            <label class="block font-bold text-gray-700 text-sm mb-2">Enfermedad Actual</label>
            <textarea class="w-full border-none bg-gray-50 rounded-lg focus:ring-2 focus:ring-blue-500 italic text-gray-600" rows="3">Paciente refiere dolor en epigastrio...</textarea>
        </div>
        <div>
            <label class="block font-bold text-gray-700 text-sm mb-2">Examen Físico</label>
            <textarea class="w-full border-none bg-gray-50 rounded-lg focus:ring-2 focus:ring-blue-500 italic text-gray-600" rows="3">Abdomen: blando, depresible...</textarea>
        </div>
        <div>
            <label class="block font-bold text-gray-700 text-sm mb-2">Diagnóstico (CIE-10)</label>
            <input type="text" class="w-full border-none bg-gray-50 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-600" placeholder="Buscar diagnóstico...">
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-6 border-t">
        <button class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Receta
        </button>
        <button class="bg-[#10b981] text-white px-6 py-2 rounded-lg font-bold hover:bg-emerald-600 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Finalizar Consulta
        </button>
    </div>
</div>
