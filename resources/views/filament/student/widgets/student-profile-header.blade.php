<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-6">
            {{-- Passport photo --}}
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="Passport photograph"
                     class="h-24 w-24 rounded-full object-cover ring-2 ring-primary-500" />
            @else
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gray-200 text-2xl font-bold text-gray-500">
                    {{ $person ? strtoupper(substr($person->first_name, 0, 1) . substr($person->surname, 0, 1)) : '?' }}
                </div>
            @endif

            {{-- Identity block --}}
            <div class="flex-1">
                <h2 class="text-xl font-bold">
                    {{ $person?->fullName() ?? auth()->user()->name }}
                </h2>

                @if ($enrolment)
                    <p class="text-sm font-mono text-gray-600 dark:text-gray-300">
                        {{ $enrolment->matric_number }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $enrolment->programme?->name }} — {{ $enrolment->level?->label }}
                    </p>
                @else
                    <p class="text-sm text-warning-600">No active enrolment — contact the Registry.</p>
                @endif
            </div>

            {{-- Status badge --}}
            @if ($enrolment)
                <x-filament::badge color="success" size="lg">
                    {{ ucfirst($enrolment->status) }}
                </x-filament::badge>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>