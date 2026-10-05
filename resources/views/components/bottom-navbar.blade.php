{{-- Bottom Navigation Bar (Mobile Only) --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 glass-panel border-t border-outline-variant/20 backdrop-blur-xl bg-surface/95 safe-bottom">
    <div class="flex items-center justify-around px-2 py-2">
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
               class="flex flex-col items-center justify-center gap-1 py-2 px-3 rounded-xl transition-all duration-300 group relative min-w-[70px] {{ request()->routeIs($item['path']) ? 'text-primary' : 'text-on-surface-variant active:bg-surface-container' }}">
                
                {{-- Active Indicator --}}
                @if(request()->routeIs($item['path']))
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-1 ether-gradient rounded-b-full"></div>
                @endif
                
                {{-- Icon --}}
                <div class="relative">
                    <span class="material-symbols-outlined text-[24px] {{ request()->routeIs($item['path']) ? 'text-primary' : 'text-on-surface-variant' }} transition-colors">
                        {{ $item['icon'] }}
                    </span>
                    @if(request()->routeIs($item['path']))
                        <div class="absolute inset-0 ether-gradient opacity-20 blur-xl"></div>
                    @endif
                </div>
                
                {{-- Label --}}
                <span class="text-[10px] font-medium tracking-wide {{ request()->routeIs($item['path']) ? 'text-primary font-bold' : 'text-on-surface-variant' }}">
                    {{ Str::limit($item['label'], 10) }}
                </span>
            </a>
        @endforeach
    </div>
</nav>

{{-- Floating Action Button (FAB) for New Task - Mobile Only --}}
<button 
    @if(request()->routeIs('tasks.index'))
        onclick="window.dispatchEvent(new CustomEvent('open-task-modal'))"
    @else
        onclick="window.location.href='{{ route('tasks.index', ['create' => 'true']) }}'"
    @endif
    class="md:hidden fixed bottom-20 right-4 z-50 w-14 h-14 ether-gradient rounded-full flex items-center justify-center shadow-2xl shadow-primary/40 hover:shadow-primary/60 active:scale-95 transition-all duration-300"
>
    <span class="material-symbols-outlined text-on-primary text-[28px]">add</span>
</button>
