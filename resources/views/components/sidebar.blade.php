<div class="hidden md:flex w-[280px] h-screen glass-panel border-r border-outline-variant/20 flex-col z-20 sticky top-0 animate-slide-in-right origin-left">
    <!-- Header -->
    <div class="p-6">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 ether-gradient rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-on-primary text-xl">checklist</span>
            </div>
            <div>
                <h1 class="text-lg font-black tracking-tighter text-on-surface leading-none">{{ __('app.appName') }}</h1>
                <p class="text-[10px] uppercase tracking-widest text-primary font-bold mt-1">{{ __('app.studentWorkspace') }}</p>
            </div>
        </div>

        <button 
            @if(request()->routeIs('tasks.index'))
                onclick="window.dispatchEvent(new CustomEvent('open-task-modal'))"
            @else
                onclick="window.location.href='{{ route('tasks.index', ['create' => 'true']) }}'"
            @endif
            class="w-full h-12 ether-gradient rounded-xl flex items-center justify-center gap-2 text-on-primary font-bold shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300"
        >
            <span class="material-symbols-outlined text-[20px]">add</span>
            {{ __('app.newTask') }}
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 flex flex-col gap-1 overflow-y-auto no-scrollbar">
        @php
            $navItems = [
                ['path' => 'dashboard', 'icon' => 'grid_view', 'label' => __('app.dashboard')],
                ['path' => 'tasks.index', 'icon' => 'task_alt', 'label' => __('app.myTasks')],
                ['path' => 'courses.index', 'icon' => 'school', 'label' => __('app.myCourses')],
                ['path' => 'archive.index', 'icon' => 'inventory_2', 'label' => __('app.archive')],
            ];
        @endphp

        @foreach($navItems as $item)
            <a href="{{ route($item['path']) }}" 
               class="h-12 px-4 rounded-xl flex items-center gap-3 transition-all duration-300 group relative overflow-hidden {{ request()->routeIs($item['path']) ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface font-medium' }}">
                @if(request()->routeIs($item['path']))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 ether-gradient rounded-r-full"></div>
                @endif
                <span class="material-symbols-outlined text-[22px] {{ request()->routeIs($item['path']) ? 'text-primary' : 'text-on-surface-variant group-hover:text-on-surface' }} transition-colors">
                    {{ $item['icon'] }}
                </span>
                <span class="tracking-wide">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- User Profile -->
    <div class="p-6 mt-auto border-t border-outline-variant/10 bg-surface-container/30">
        <div class="flex items-center gap-3 cursor-pointer group">
            <div class="w-11 h-11 rounded-full bg-surface-container-high border border-outline-variant/30 flex items-center justify-center relative overflow-hidden shrink-0 group-hover:border-primary/50 transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">person</span>
                <div class="absolute inset-0 ether-gradient opacity-0 group-hover:opacity-10 transition-opacity"></div>
            </div>
            <div class="overflow-hidden">
                <h3 class="text-sm font-bold text-on-surface truncate group-hover:text-primary transition-colors">{{ Auth::user()->full_name ?? Auth::user()->username }}</h3>
                <p class="text-xs text-on-surface-variant truncate font-medium">{{ Auth::user()->study_program ?? Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>
