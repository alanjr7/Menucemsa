<div class="flex flex-col h-full w-full">

    <!-- Header del Sidebar (Logo y Título) -->
    <div class="h-16 shrink-0 flex items-center bg-slate-100 border-b border-slate-200 transition-all duration-300"
        :class="sidebarOpen ? 'px-6 justify-start' : 'px-0 justify-center'">
        <div class="flex items-center gap-3">
            <div
                class="p-2 bg-gradient-to-br from-[#3B82F6] to-[#2563EB] rounded-xl shadow-lg shadow-blue-200 shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div x-show="sidebarOpen" x-transition.opacity.duration.200ms class="whitespace-nowrap">
                <h1 class="font-bold text-lg leading-tight uppercase tracking-wide text-slate-800">HIS / CEMSA</h1>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Sistema Clínico</p>
            </div>
        </div>
    </div>

    <!-- Navegación Dinámica -->
    <nav class="flex-1 py-4 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden"
        :class="sidebarOpen ? 'px-4' : 'px-2'">

        @if(auth()->check())
            @php
                // Obtenemos los menús principales (sin padre) con sus hijos, ordenados.
                // En un proyecto real, esto es mejor pasarlo desde un ViewComposer
                $menus = \App\Models\Menu::whereNull('parent_id')->with('children')->orderBy('order')->get();
            @endphp

            @foreach($menus as $menu)
                @php /** @var \App\Models\Menu $menu */ @endphp
                @if($menu->canBeSeenBy(auth()->user()))

                    @php
                        // Verificamos si el menú actual o alguno de sus hijos está activo
                        $patterns = $menu->active_pattern ? explode(',', $menu->active_pattern) : [$menu->route];
                        $isActive = request()->routeIs(...$patterns);

                        if (!$isActive && $menu->children->isNotEmpty()) {
                            $isActive = $menu->children->contains(function ($child) {
                                return request()->routeIs($child->route);
                            });
                        }

                        // Colores base para Tailwind (evita que Tailwind purgue las clases dinámicas)
                        $colorClass = match ($menu->color) {
                            'red' => ['bg' => 'bg-red-600', 'textActive' => 'text-red-400', 'hover' => 'group-hover:text-red-300', 'shadow' => 'shadow-red-900/20', 'border' => 'border-red-500/30'],
                            'emerald' => ['bg' => 'bg-emerald-600', 'textActive' => 'text-emerald-400', 'hover' => 'group-hover:text-emerald-300', 'shadow' => 'shadow-emerald-900/20', 'border' => 'border-emerald-500/30'],
                            'purple' => ['bg' => 'bg-purple-600', 'textActive' => 'text-purple-400', 'hover' => 'group-hover:text-purple-300', 'shadow' => 'shadow-purple-900/20', 'border' => 'border-purple-500/30'],
                            'yellow' => ['bg' => 'bg-yellow-600', 'textActive' => 'text-yellow-400', 'hover' => 'group-hover:text-yellow-300', 'shadow' => 'shadow-yellow-900/20', 'border' => 'border-yellow-500/30'],
                            'cyan' => ['bg' => 'bg-cyan-600', 'textActive' => 'text-cyan-400', 'hover' => 'group-hover:text-cyan-300', 'shadow' => 'shadow-cyan-900/20', 'border' => 'border-cyan-500/30'],
                            'orange' => ['bg' => 'bg-orange-600', 'textActive' => 'text-orange-400', 'hover' => 'group-hover:text-orange-300', 'shadow' => 'shadow-orange-900/20', 'border' => 'border-orange-500/30'],
                            'slate' => ['bg' => 'bg-slate-600', 'textActive' => 'text-slate-400', 'hover' => 'group-hover:text-slate-300', 'shadow' => 'shadow-slate-900/20', 'border' => 'border-slate-500/30'],
                            default => ['bg' => 'bg-[#2563EB]', 'textActive' => 'text-[#2563EB]', 'hover' => 'group-hover:text-[#1E40AF]', 'shadow' => 'shadow-blue-200', 'border' => 'border-[#2563EB]/30'],
                        };
                    @endphp

                    <!-- Si el menú NO tiene submenús (Es un enlace directo) -->
                    @if($menu->children->isEmpty())
                        <a href="{{ $menu->route && Route::has($menu->route) ? route($menu->route) : '#' }}"
                            class="flex items-center py-2.5 rounded-lg transition-all duration-200 group {{ $isActive ? $colorClass['bg'] . ' text-white shadow-md ' . $colorClass['shadow'] : 'text-slate-700 hover:bg-slate-100 hover:text-[#2563EB]' }}"
                            :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">

                            <svg class="w-6 h-6 shrink-0 transition-colors text-slate-600 group-hover:text-[#2563EB]"
                                :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <!-- Aquí pintamos el Path del icono desde la base de datos -->
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->icon_path }}" />
                            </svg>
                            <span class="text-sm font-semibold whitespace-nowrap" x-show="sidebarOpen"
                                x-transition.opacity.duration.200ms>{{ $menu->name }}</span>
                        </a>

                        <!-- Si el menú SÍ tiene submenús (Es un Dropdown) -->
                    @else
                        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                                class="w-full flex items-center py-2.5 text-slate-700 hover:bg-slate-100 hover:text-[#2563EB] rounded-lg transition-all duration-200 group"
                                :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">

                                <svg class="w-6 h-6 shrink-0 transition-colors text-slate-600 group-hover:text-[#2563EB]"
                                    :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->icon_path }}" />
                                </svg>

                                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap"
                                    x-show="sidebarOpen">{{ $menu->name }}</span>

                                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>

                            <!-- Submenús -->
                            <div x-show="open && sidebarOpen" x-collapse
                                class="pl-4 mt-1 mb-2 space-y-1 border-l {{ $colorClass['border'] }} ml-5">
                                @foreach($menu->children as $child)
                                    @if($child->canBeSeenBy(auth()->user()))
                                        @php $isChildActive = request()->routeIs($child->route); @endphp
                                        <a href="{{ $child->route && Route::has($child->route) ? route($child->route) : '#' }}"
                                            class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ $isChildActive ? 'text-white ' . $colorClass['bg'] . ' font-bold' : 'text-slate-600 hover:text-[#2563EB] hover:bg-slate-50' }}">
                                            {{ $child->name }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                @endif
            @endforeach
        @endif

    </nav>
</div>