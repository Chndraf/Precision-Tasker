@extends('layouts.app')

@section('title', __('app.myCourses') . ' - ' . __('app.appName'))

@section('content')
<div x-data="{ 
    showModal: false, 
    editMode: false, 
    courseForm: { id: '', name: '', code: '', icon_string: 'school' },
    icons: ['school', 'science', 'computer', 'menu_book', 'calculate', 'history_edu', 'language', 'biotech', 'functions', 'architecture', 'gavel', 'psychology']
}" @open-course-modal.window="showModal = true; editMode = false; courseForm = { id: '', name: '', code: '', icon_string: 'school' }"
   @edit-course.window="showModal = true; editMode = true; courseForm = $event.detail"
   class="space-y-8 pb-12 relative"
>
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 glass-panel p-6 sm:p-8 rounded-[2rem] relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-primary/10 rounded-full blur-[80px] -mr-10 -mt-10 pointer-events-none"></div>
        <div class="absolute left-0 bottom-0 w-48 h-48 bg-tertiary-container/10 rounded-full blur-[60px] -ml-10 -mb-10 pointer-events-none"></div>

        <div class="flex items-center gap-6 z-10">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-surface-container-high border border-outline-variant/30 rounded-[1.5rem] flex items-center justify-center shadow-xl shrink-0">
                <span class="material-symbols-outlined text-4xl sm:text-5xl text-on-surface-variant">school</span>
            </div>
            <div>
                <p class="text-sm sm:text-base font-medium text-primary mb-1 uppercase tracking-widest">{{ __('app.academicSetup') }}</p>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tighter text-on-surface mb-2">{{ __('app.myCoursesTitle') }}</h1>
                <p class="text-sm sm:text-base text-on-surface-variant max-w-xl leading-relaxed">
                    {{ __('app.myCoursesSubtitle') }}
                </p>
            </div>
        </div>

        <button @click="$dispatch('open-course-modal')" class="h-12 px-6 ether-gradient text-on-primary font-bold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 z-10 w-full md:w-auto justify-center shrink-0">
            <span class="material-symbols-outlined">add</span>
            {{ __('app.newCourse') }}
        </button>
    </header>

    @if(session('error'))
        <div class="glass-panel px-6 py-4 rounded-xl border border-error/20 bg-error-container/10 text-error flex items-center gap-3 animate-fade-in">
            <span class="material-symbols-outlined">error</span>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Courses Grid -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 stagger-children">
            @foreach($courses as $course)
                @php
                    $cTasks = $course->tasks()->get();
                    $total = $cTasks->count();
                    $completed = $cTasks->where('status_task', 'Completed')->count();
                    $pending = $total - $completed;
                    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
                @endphp
                <div class="glass-panel p-6 rounded-[2rem] group hover:border-primary/30 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden flex flex-col h-full">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="flex items-start justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl ether-gradient flex items-center justify-center shadow-lg shadow-primary/20">
                                <span class="material-symbols-outlined text-on-primary text-2xl">{{ $course->icon_string ?? 'school' }}</span>
                            </div>
                            
                            <!-- Actions Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false" class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high flex items-center justify-center text-on-surface-variant hover:text-on-surface transition-colors border border-outline-variant/20">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                                
                                <div x-show="open" x-transition class="absolute right-0 mt-2 w-36 bg-surface-container-high border border-outline-variant/30 rounded-xl shadow-xl overflow-hidden z-50" style="display: none;">
                                    <button @click="open = false; $dispatch('edit-course', { id: {{ $course->id }}, name: '{{ addslashes($course->name) }}', code: '{{ addslashes($course->code) }}', icon_string: '{{ $course->icon_string }}' })" class="w-full px-4 py-2 flex items-center gap-2 hover:bg-primary/10 text-on-surface-variant hover:text-primary transition-colors text-sm font-medium text-left">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        {{ __('app.edit') }}
                                    </button>
                                    <form method="POST" action="{{ route('courses.destroy', $course) }}" onsubmit="return confirm('{{ __('app.confirmDeleteCourse') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-4 py-2 flex items-center gap-2 hover:bg-error/10 text-error transition-colors text-sm font-medium text-left">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                            {{ __('app.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 flex-1">
                            <h3 class="text-xl font-bold text-on-surface mb-1 truncate" title="{{ $course->name }}">{{ $course->name }}</h3>
                            @if($course->code)
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-surface-container-high text-xs font-bold text-on-surface-variant border border-outline-variant/10 uppercase tracking-wider">
                                    {{ $course->code }}
                                </span>
                            @else
                                <div class="h-6"></div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="text-center">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase tracking-widest mb-1">{{ __('app.tasks') }}</p>
                                    <p class="text-2xl font-black text-on-surface">{{ $total }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase tracking-widest mb-1">{{ __('app.pending') }}</p>
                                    <p class="text-2xl font-black text-error">{{ $pending }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase tracking-widest mb-1">{{ __('app.completed') }}</p>
                                    <p class="text-2xl font-black text-primary">{{ $completed }}</p>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-bold mb-2">
                                    <span class="text-on-surface-variant">{{ __('app.taskProgress') }}</span>
                                    <span class="text-primary">{{ $progress }}%</span>
                                </div>
                                <div class="h-2 w-full bg-surface-container-highest rounded-full overflow-hidden">
                                    <div class="h-full bg-primary transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="glass-panel rounded-[2rem] p-12 flex flex-col items-center justify-center text-center max-w-2xl mx-auto border border-dashed border-outline-variant/30 animate-slide-up" style="animation-delay: 0.2s;">
            <div class="w-20 h-20 bg-surface-container-high rounded-full flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-4xl text-on-surface-variant">school</span>
            </div>
            <h3 class="text-2xl font-black text-on-surface mb-2">{{ __('app.noCoursesTitle') }}</h3>
            <p class="text-on-surface-variant mb-8">{{ __('app.noCoursesSubtitle') }}</p>
            <button @click="$dispatch('open-course-modal')" class="h-11 px-6 ether-gradient text-on-primary font-bold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300">
                {{ __('app.createFirstCourse') }}
            </button>
        </div>
    @endif

    <!-- Slide-out Modal for Create/Edit -->
    <div x-show="showModal" class="fixed top-20 bottom-0 left-0 right-0 z-40 overflow-hidden" style="display: none;">
        <!-- Backdrop -->
        <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-background/80 backdrop-blur-sm" @click="showModal = false"></div>

        <!-- Panel -->
        <div class="absolute inset-y-0 right-0 w-full max-w-md flex">
            <div x-show="showModal" 
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-full h-full glass-panel border-l border-outline-variant/20 shadow-2xl flex flex-col bg-surface"
            >
                <div class="h-20 px-6 border-b border-outline-variant/20 flex items-center justify-between shrink-0 bg-surface-container/50">
                    <div>
                        <h2 class="text-xl font-black text-on-surface" x-text="editMode ? '{{ __('app.editCourse') }}' : '{{ __('app.newCourse') }}'"></h2>
                    </div>
                    <button @click="showModal = false" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant hover:text-on-surface transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto no-scrollbar p-6">
                    <form id="courseFormElement" method="POST" :action="editMode ? '{{ url('courses') }}/' + courseForm.id : '{{ route('courses.store') }}'" class="space-y-6">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Name -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.courseNameLabel') }} *</label>
                            <input type="text" name="name" x-model="courseForm.name" required class="w-full h-12 bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/40" placeholder="{{ __('app.courseNamePlaceholder') }}">
                        </div>

                        <!-- Code -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.courseCodeLabel') }}</label>
                            <input type="text" name="code" x-model="courseForm.code" class="w-full h-12 bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/40" placeholder="{{ __('app.courseCodePlaceholder') }}">
                            <p class="text-[10px] text-on-surface-variant/60 ml-1">{{ __('app.courseCodeHelp') }}</p>
                        </div>

                        <!-- Icon Selection -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.iconLabel') }}</label>
                                <div class="flex items-center gap-2 text-[10px] text-on-surface-variant">
                                    <span>{{ __('app.previewLabel') }}:</span>
                                    <span class="material-symbols-outlined text-primary text-[18px]" x-text="courseForm.icon_string"></span>
                                </div>
                            </div>
                            
                            <input type="hidden" name="icon_string" x-model="courseForm.icon_string">
                            
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 bg-surface-container-highest p-3 rounded-xl border border-outline-variant/10">
                                <template x-for="icon in icons" :key="icon">
                                    <button 
                                        type="button" 
                                        @click="courseForm.icon_string = icon"
                                        class="aspect-square rounded-lg flex items-center justify-center transition-all duration-200"
                                        :class="courseForm.icon_string === icon ? 'bg-primary text-on-primary shadow-md scale-110' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface hover:scale-105'"
                                    >
                                        <span class="material-symbols-outlined text-[24px]" x-text="icon"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-6 border-t border-outline-variant/20 bg-surface-container/50 shrink-0 flex gap-3">
                    <button @click="showModal = false" class="flex-1 h-12 rounded-xl border border-outline-variant/30 text-on-surface-variant font-bold hover:bg-surface-container-high transition-colors">
                        {{ __('app.cancel') }}
                    </button>
                    <button onclick="document.getElementById('courseFormElement').submit()" class="flex-1 h-12 ether-gradient text-on-primary font-bold rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
                        {{ __('app.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
