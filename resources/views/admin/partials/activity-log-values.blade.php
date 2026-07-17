{{-- Renderiza un bloque de valores de bitácora (anteriores o nuevos).
     Parámetros: $entries (array clave=>valor ya filtrado), $tone ('old'|'new'), $title --}}
@php
    $tone = $tone ?? 'new';
    $isNew = $tone === 'new';
    $textClass = $isNew ? 'text-green-700' : 'text-red-700';
    $chipClass = $isNew ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800';
@endphp
@if(!empty($entries))
    <div class="{{ $textClass }}">
        <span class="font-medium">{{ $title }}:</span>
        <div class="mt-1 space-y-1">
            @foreach($entries as $key => $value)
                <div class="flex items-start gap-2 text-sm">
                    <span class="font-medium">{{ \App\Models\ActivityLog::humanKey($key) }}:</span>
                    @if(is_array($value) && count($value) > 0)
                        <details class="min-w-0">
                            <summary class="cursor-pointer select-none {{ $chipClass }} px-2 py-0.5 rounded text-xs">
                                {{ \App\Models\ActivityLog::summarize($value) }}
                            </summary>
                            <pre class="mt-1 max-h-64 overflow-auto whitespace-pre-wrap break-all rounded border border-gray-200 bg-white p-2 text-[11px] leading-snug text-gray-700">{{ \App\Models\ActivityLog::prettyJson($value) }}</pre>
                        </details>
                    @else
                        <span class="{{ $chipClass }} px-2 py-0.5 rounded text-xs break-all">{{ \App\Models\ActivityLog::formatScalar($key, $value) }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
