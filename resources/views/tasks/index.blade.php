@extends('layouts.app')

@section('title', __('app.manageTasks') . ' - ' . __('app.appName'))

@section('content')
<div x-data="{ 
    showModal: false, 
    editMode: false, 
    taskForm: { id: '', title: '', description: '', course_id: '', priority: 'Low', deadline: '' }
}" x-init="if (new URLSearchParams(window.location.search).get('create') === 'true') { $dispatch('open-task-modal'); window.history.replaceState({}, '', window.location.pathname); }"
   @open-task-modal.window="showModal = true; editMode = false; taskForm = { id: '', title: '', description: '', course_id: '', priority: 'Low', deadline: '' }"
   @edit-task.window="showModal = true; editMode = true; taskForm = $event.detail"
   class="space-y-8 pb-12 relative"
>
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 glass-panel p-6 sm:p-8 rounded-[2rem] relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-primary/10 rounded-full blur-[80px] -mr-10 -mt-10 pointer-events-none"></div>
        <div class="absolute left-0 bottom-0 w-48 h-48 bg-tertiary-container/10 rounded-full blur-[60px] -ml-10 -mb-10 pointer-events-none"></div>

        <div class="flex items-center gap-6 z-10">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-surface-container-high border border-outline-variant/30 rounded-[1.5rem] flex items-center justify-center shadow-xl shrink-0">
                <span class="material-symbols-outlined text-4xl sm:text-5xl text-on-surface-variant">task</span>
            </div>
            <div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tighter text-on-surface mb-2">{{ __('app.manageTasks') }}</h1>
                <p class="text-sm sm:text-base text-on-surface-variant max-w-xl leading-relaxed">
                    {{ __('app.manageTasksSubtitle') }}
                </p>
            </div>
        </div>

        <button @click="$dispatch('open-task-modal')" class="h-12 px-6 ether-gradient text-on-primary font-bold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 z-10 w-full md:w-auto justify-center shrink-0">
            <span class="material-symbols-outlined">add</span>
            {{ __('app.newTask') }}
        </button>
    </header>

    @if(session('error'))
        <div class="glass-panel px-6 py-4 rounded-xl border border-error/20 bg-error-container/10 text-error flex items-center gap-3 animate-fade-in">
            <span class="material-symbols-outlined">error</span>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filters Row -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fade-in" style="animation-delay: 0.1s;">
        <div class="flex bg-surface-container-high p-1 rounded-xl w-full sm:w-auto overflow-x-auto no-scrollbar">
            <a href="{{ route('tasks.index') }}" class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition-all {{ !request('course_id') ? 'bg-surface-container-highest text-on-surface shadow-md' : 'text-on-surface-variant hover:text-on-surface' }}">
                {{ __('app.allTasks') }}
            </a>
            @if($courses->count() > 0)
                <div class="w-[1px] bg-outline-variant/20 mx-2 my-2"></div>
                @foreach($courses->take(3) as $c)
                    <a href="{{ route('tasks.index', ['course_id' => $c->id] + request()->except('course_id')) }}" class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition-all {{ request('course_id') == $c->id ? 'bg-surface-container-highest text-on-surface shadow-md' : 'text-on-surface-variant hover:text-on-surface' }}">
                        {{ $c->name }}
                    </a>
                @endforeach
            @endif
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('tasks.index', ['sort' => 'deadline'] + request()->except('sort')) }}" class="h-10 px-4 rounded-xl flex items-center gap-2 text-sm font-bold transition-colors {{ request('sort') === 'deadline' ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-surface-container hover:bg-surface-container-high text-on-surface-variant border border-outline-variant/20' }}">
                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                <span class="hidden sm:inline">{{ __('app.closestDeadline') }}</span>
            </a>
            <a href="{{ route('tasks.index', ['sort' => 'priority'] + request()->except('sort')) }}" class="h-10 px-4 rounded-xl flex items-center gap-2 text-sm font-bold transition-colors {{ request('sort') === 'priority' ? 'bg-error/10 text-error border border-error/20' : 'bg-surface-container hover:bg-surface-container-high text-on-surface-variant border border-outline-variant/20' }}">
                <span class="material-symbols-outlined text-[18px]">priority_high</span>
                <span class="hidden sm:inline">{{ __('app.highestPriority') }}</span>
            </a>
        </div>
    </div>

    <!-- Tasks List -->
    @if($tasks->count() > 0)
        <div class="space-y-4 stagger-children">
            @foreach($tasks as $task)
                <div class="glass-panel p-5 rounded-2xl group hover:border-primary/30 transition-all duration-300 relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                    
                    <div class="flex items-start sm:items-center gap-4 relative z-10">
                        <form method="POST" action="{{ route('tasks.complete', $task) }}" class="shrink-0 mt-1 sm:mt-0">
                            @csrf
                            <button type="submit" class="w-6 h-6 rounded-md border-2 border-outline-variant/40 flex items-center justify-center text-transparent hover:border-primary hover:text-primary transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50">
                                <span class="material-symbols-outlined text-[16px] font-bold">check</span>
                            </button>
                        </form>
                        
                        <div>
                            <h3 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors cursor-pointer" @click="$dispatch('edit-task', { id: {{ $task->id }}, title: '{{ addslashes($task->title) }}', description: '{{ addslashes($task->description) }}', course_id: '{{ $task->course_id }}', priority: '{{ $task->priority }}', deadline: '{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i') : '' }}' })">
                                {{ $task->title }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs">
                                <div class="flex items-center gap-1 text-on-surface-variant bg-surface-container-high px-2 py-1 rounded-md border border-outline-variant/10">
                                    <span class="material-symbols-outlined text-[14px]">school</span>
                                    <span class="font-medium">{{ $task->course->name ?? __('app.noCourseAssigned') }}</span>
                                </div>

                                @if($task->deadline)
                                    @php
                                        $deadline = \Carbon\Carbon::parse($task->deadline);
                                        $isUrgent = $deadline->isPast() || $deadline->diffInHours(now()) < 24;
                                    @endphp
                                    <div class="flex items-center gap-1 px-2 py-1 rounded-md border {{ $isUrgent ? 'bg-error-container/20 text-error border-error/20' : 'bg-surface-container-high text-on-surface-variant border-outline-variant/10' }}">
                                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                        <span class="font-medium">{{ $deadline->format('M d, g:i A') }}</span>
                                    </div>
                                @endif

                                @php
                                    $pColor = 'bg-surface-container-high text-on-surface-variant border-outline-variant/10';
                                    if ($task->priority === 'High') $pColor = 'bg-error/10 text-error border-error/20';
                                    if ($task->priority === 'Medium') $pColor = 'bg-primary/10 text-primary border-primary/20';
                                @endphp
                                <div class="flex items-center gap-1 px-2 py-1 rounded-md border {{ $pColor }}">
                                    <span class="material-symbols-outlined text-[14px]">{{ $task->priority === 'High' ? 'priority_high' : ($task->priority === 'Medium' ? 'remove' : 'arrow_downward') }}</span>
                                    <span class="font-bold uppercase tracking-widest text-[9px]">{{ __('app.' . strtolower($task->priority)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 relative z-10 w-full sm:w-auto justify-end border-t border-outline-variant/10 sm:border-0 pt-3 sm:pt-0">
                        <button @click="$dispatch('edit-task', { id: {{ $task->id }}, title: '{{ addslashes($task->title) }}', description: '{{ addslashes($task->description) }}', course_id: '{{ $task->course_id }}', priority: '{{ $task->priority }}', deadline: '{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i') : '' }}' })" class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-colors border border-outline-variant/20 group/btn" title="{{ __('app.edit') }}">
                            <span class="material-symbols-outlined text-[18px] group-hover/btn:scale-110 transition-transform">edit</span>
                        </button>
                        
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('{{ __('app.confirmDeleteTask') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-error/10 hover:text-error transition-colors border border-outline-variant/20 group/btn" title="{{ __('app.delete') }}">
                                <span class="material-symbols-outlined text-[18px] group-hover/btn:scale-110 transition-transform">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="glass-panel rounded-[2rem] p-12 flex flex-col items-center justify-center text-center max-w-2xl mx-auto border border-dashed border-outline-variant/30 animate-slide-up" style="animation-delay: 0.2s;">
            <div class="w-20 h-20 bg-surface-container-high rounded-full flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-4xl text-on-surface-variant">inventory_2</span>
            </div>
            <h3 class="text-2xl font-black text-on-surface mb-2">{{ __('app.noTasksYet') }}</h3>
            <p class="text-on-surface-variant mb-8">{{ __('app.noTasksSubtitle') }}</p>
            <button @click="$dispatch('open-task-modal')" class="h-11 px-6 ether-gradient text-on-primary font-bold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300">
                {{ __('app.createFirstTask') }}
            </button>
        </div>
    @endif

    <!-- Slide-out Modal for Create/Edit -->
    <div x-show="showModal" class="fixed inset-0 md:top-20 md:bottom-0 z-40 overflow-hidden" style="display: none;">
        <!-- Backdrop -->
        <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-background/80 backdrop-blur-sm" @click="showModal = false"></div>

        <!-- Panel -->
        <div class="absolute inset-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md flex">
            <div x-show="showModal" 
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-y-full md:translate-y-0 md:translate-x-full"
                 x-transition:enter-end="translate-y-0 md:translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-y-0 md:translate-x-0"
                 x-transition:leave-end="translate-y-full md:translate-y-0 md:translate-x-full"
                 class="w-full h-full glass-panel md:border-l border-outline-variant/20 shadow-2xl flex flex-col bg-surface"
            >
                <div class="h-16 md:h-20 px-4 md:px-6 border-b border-outline-variant/20 flex items-center justify-between shrink-0 bg-surface-container/50">
                    <div>
                        <h2 class="text-lg md:text-xl font-black text-on-surface" x-text="editMode ? '{{ __('app.editTask') }}' : '{{ __('app.newTask') }}'"></h2>
                    </div>
                    <button @click="showModal = false" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant hover:text-on-surface transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto no-scrollbar p-4 md:p-6 pb-24 md:pb-6">
                    <form id="taskFormElement" method="POST" :action="editMode ? '{{ url('tasks') }}/' + taskForm.id : '{{ route('tasks.store') }}'" class="space-y-4 md:space-y-6">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Title -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.taskNameLabel') }} *</label>
                            <input type="text" name="title" x-model="taskForm.title" required class="w-full h-12 bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/40" placeholder="{{ __('app.taskNamePlaceholder') }}">
                        </div>

                        <!-- Course -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.associatedCourse') }}</label>
                            <select name="course_id" x-model="taskForm.course_id" class="w-full h-12 bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                <option value="">-- {{ __('app.selectCourse') }} --</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Priority -->
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.priorityLabel') }}</label>
                                <select name="priority" x-model="taskForm.priority" class="w-full h-12 bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                    <option value="Low">{{ __('app.low') }}</option>
                                    <option value="Medium">{{ __('app.medium') }}</option>
                                    <option value="High">{{ __('app.high') }}</option>
                                </select>
                            </div>

                            <!-- Deadline -->
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.deadlineLabel') }}</label>
                                <input type="datetime-local" name="deadline" x-model="taskForm.deadline" class="w-full h-12 bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all [color-scheme:dark]">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant ml-1">{{ __('app.descriptionLabel') }}</label>
                            <textarea name="description" x-model="taskForm.description" rows="4" class="w-full bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/40 resize-none" placeholder="{{ __('app.descriptionPlaceholder') }}"></textarea>
                        </div>
                    </form>
                </div>

                <div class="p-4 md:p-6 border-t border-outline-variant/20 bg-surface-container/50 shrink-0 flex gap-3 sticky bottom-0 safe-bottom">
                    <button @click="showModal = false" class="flex-1 h-12 rounded-xl border border-outline-variant/30 text-on-surface-variant font-bold hover:bg-surface-container-high active:bg-surface-container-high transition-colors">
                        {{ __('app.cancel') }}
                    </button>
                    <button onclick="document.getElementById('taskFormElement').submit()" class="flex-1 h-12 ether-gradient text-on-primary font-bold rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                        {{ __('app.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
