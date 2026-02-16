<!-- SIDEBAR -->
<aside class="w-64 sidebar-bg text-white flex flex-col flex-shrink-0 transition-all duration-300" style="background-color: #1e3a8a;">
    <!-- Logo Area -->
    <div class="h-16 flex items-center px-6 border-b border-blue-800/50">
        <div class="flex items-center gap-3">
            <div class="p-1.5 bg-blue-500/20 rounded-lg">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/> 
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight">HIS / ERP CEMSA</h1>
                <p class="text-[10px] text-blue-200 uppercase tracking-wider">Sistema Clínico</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-blue-600 rounded-lg text-white shadow-md' : 'text-blue-100 hover:bg-blue-800 hover:text-white rounded-lg transition-colors' }} group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? '' : 'opacity-75' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('reception') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('reception') ? 'bg-blue-600 rounded-lg text-white shadow-md' : 'text-blue-100 hover:bg-blue-800 hover:text-white rounded-lg transition-colors' }} group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reception') ? '' : 'opacity-75' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span class="font-medium">Recepción</span>
        </a>

        <!-- SECCIÓN PACIENTES (Expandible) -->
        <div class="pt-2 pb-1">
            <button onclick="togglePatientsMenu()" class="w-full flex items-center px-4 py-2 text-white font-semibold hover:bg-blue-800 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-sm flex-1 text-left">Pacientes</span>
                <svg id="patients-arrow" class="w-4 h-4 transition-transform duration-300 {{ request()->routeIs('patients.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            </button>
            
            <!-- Submenú (Oculto por defecto) -->
            <div id="patients-submenu" class="space-y-0.5 mt-1 max-h-0 overflow-hidden transition-all duration-300 {{ request()->routeIs('patients.*') ? 'max-h-96' : '' }}">
                <a href="{{ route('patients.index') }}" class="flex items-center pl-12 pr-4 py-2.5 {{ request()->routeIs('patients.index') ? 'bg-blue-600 rounded-r-lg text-white shadow-sm border-l-4 border-blue-300' : 'text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors' }}">
                    <span class="font-medium text-sm">Maestro de Pacientes</span>
                </a>
                
                <a href="#" class="flex items-center pl-12 pr-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-sm">Admisión</span>
                </a>
                <a href="#" class="flex items-center pl-12 pr-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm">Consulta Externa</span>
                </a>
                <a href="#" class="flex items-center pl-12 pr-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-sm">Emergencias</span>
                </a>
                <a href="#" class="flex items-center pl-12 pr-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span class="text-sm">Enfermería</span>
                </a>
                <a href="#" class="flex items-center pl-12 pr-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm">UTI</span>
                </a>
                <a href="#" class="flex items-center pl-12 pr-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    <span class="text-sm">Quirófano</span>
                </a>
                <a href="#" class="flex items-center pl-12 pr-4 py-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-sm">Hospitalización</span>
                </a>
            </div>
        </div>

        <!-- Resto de items -->
        <a href="#" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 hover:text-white rounded-lg transition-colors group">
            <svg class="w-5 h-5 mr-3 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">Administración</span>
        </a>
        <a href="#" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 hover:text-white rounded-lg transition-colors group">
            <svg class="w-5 h-5 mr-3 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="font-medium">Logística</span>
        </a>
        <a href="#" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 hover:text-white rounded-lg transition-colors group">
            <svg class="w-5 h-5 mr-3 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="font-medium">Gerencial</span>
        </a>
        <a href="#" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 hover:text-white rounded-lg transition-colors group">
            <svg class="w-5 h-5 mr-3 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="font-medium">Seguridad</span>
        </a>
    </nav>
</aside>

<script>
    function togglePatientsMenu() {
        const submenu = document.getElementById('patients-submenu');
        const arrow = document.getElementById('patients-arrow');
        
        submenu.classList.toggle('max-h-0');
        submenu.classList.toggle('max-h-96');
        arrow.classList.toggle('rotate-180');
    }
</script>

