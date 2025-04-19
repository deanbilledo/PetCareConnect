@props([
    'id' => 'modal',
    'maxWidth' => '2xl',
    'title' => null,
    'showCloseButton' => true,
    'closeOnClickOutside' => true,
    'type' => 'default' // Options: default, danger, success, warning, info
])

@php
    $maxWidthClasses = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
        'full' => 'sm:max-w-full',
    ];

    $bgHeaderColors = [
        'default' => 'bg-white',
        'danger' => 'bg-red-50',
        'success' => 'bg-green-50',
        'warning' => 'bg-yellow-50',
        'info' => 'bg-blue-50',
    ];
    
    $headerTextColors = [
        'default' => 'text-gray-900',
        'danger' => 'text-red-800',
        'success' => 'text-green-800',
        'warning' => 'text-yellow-800',
        'info' => 'text-blue-800',
    ];

    $iconColors = [
        'default' => 'text-gray-400',
        'danger' => 'text-red-400',
        'success' => 'text-green-400',
        'warning' => 'text-yellow-400',
        'info' => 'text-blue-400',
    ];

    $borderColors = [
        'default' => 'border-gray-200',
        'danger' => 'border-red-200',
        'success' => 'border-green-200',
        'warning' => 'border-yellow-200',
        'info' => 'border-blue-200',
    ];
@endphp

<div
    x-data="{ 
        show: false,
        title: '{{ $title }}',
        focusedButton: null,
        init() {
            let focusableElements = this.$refs.modalPanel.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex=\'-1\'])');
            if (focusableElements.length) {
                this.focusedButton = focusableElements[0];
            }
        },
        focusButton() {
            if (this.focusedButton) {
                this.$nextTick(() => {
                    this.focusedButton.focus();
                });
            }
        },
        close() {
            this.show = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    x-on:open-modal.window="$event.detail == '{{ $id }}' ? (show = true, document.body.classList.add('overflow-hidden')) : null"
    x-on:close-modal.window="$event.detail == '{{ $id }}' ? close() : null"
    x-on:keydown.escape.window="if ({{ $closeOnClickOutside ? 'true' : 'false' }}) close()"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0 overflow-y-auto"
    style="display: none;"
>
    <div 
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click="if ({{ $closeOnClickOutside ? 'true' : 'false' }}) close()"
    >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-on:click.outside="if ({{ $closeOnClickOutside ? 'true' : 'false' }}) close()"
        class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all w-full sm:w-auto sm:mx-auto {{ $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['2xl'] }}"
        x-ref="modalPanel"
        style="max-height: calc(100vh - 2rem);"
    >
        @if ($title)
        <div class="px-6 py-4 {{ $bgHeaderColors[$type] }} border-b {{ $borderColors[$type] }}">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium {{ $headerTextColors[$type] }}">
                    <span x-text="title">{{ $title }}</span>
                </h3>
                
                @if($showCloseButton)
                <button 
                    x-on:click="close()"
                    type="button" 
                    class="{{ $iconColors[$type] }} hover:{{ $headerTextColors[$type] }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                @endif
            </div>
        </div>
        @endif

        <div class="px-6 py-4 overflow-y-auto" style="max-height: calc(100vh - 12rem);">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="px-6 py-4 bg-gray-50 border-t {{ $borderColors[$type] }} rounded-b-lg text-right">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
