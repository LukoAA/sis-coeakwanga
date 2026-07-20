<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            {{-- Passport photo / initials --}}
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="Passport photograph"
                     style="height: 6rem; width: 6rem; border-radius: 9999px; object-fit: cover; flex-shrink: 0;" />
            @else
                <div style="height: 6rem; width: 6rem; border-radius: 9999px; background: #e5e7eb; color: #6b7280;
                            display: flex; align-items: center; justify-content: center;
                            font-size: 1.5rem; font-weight: 700; flex-shrink: 0;">
                    {{ $person ? strtoupper(substr($person->first_name, 0, 1) . substr($person->surname, 0, 1)) : '?' }}
                </div>
            @endif

            {{-- Identity block --}}
            <div style="flex: 1; min-width: 0;">
                <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">
                    {{ $person?->fullName() ?? auth()->user()->name }}
                </h2>

                @if ($enrolment)
                    <p style="font-family: monospace; font-size: 0.875rem; margin: 0.25rem 0; opacity: 0.75;">
                        {{ $enrolment->matric_number }}
                    </p>
                    <p style="font-size: 0.875rem; margin: 0; opacity: 0.6;">
                        {{ $enrolment->programme?->name }} — {{ $enrolment->level?->label }}
                    </p>
                @else
                    <p style="font-size: 0.875rem; color: #d97706;">No active enrolment — contact the Registry.</p>
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