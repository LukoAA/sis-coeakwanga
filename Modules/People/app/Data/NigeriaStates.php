<?php

namespace Modules\People\Data;

class NigeriaStates
{
    /**
     * State => [LGAs]. Keyed by state name.
     */
    public static function all(): array
    {
        return [
            'Abia' => ['Aba North', 'Aba South', 'Arochukwu', 'Bende', 'Ikwuano', 'Isiala Ngwa North', 'Isiala Ngwa South', 'Isuikwuato', 'Obi Ngwa', 'Ohafia', 'Osisioma', 'Ugwunagbo', 'Ukwa East', 'Ukwa West', 'Umuahia North', 'Umuahia South', 'Umu Nneochi'],
            'Adamawa' => ['Demsa', 'Fufore', 'Ganye', 'Girei', 'Gombi', 'Guyuk', 'Hong', 'Jada', 'Lamurde', 'Madagali', 'Maiha', 'Mayo Belwa', 'Michika', 'Mubi North', 'Mubi South', 'Numan', 'Shelleng', 'Song', 'Toungo', 'Yola North', 'Yola South'],
            // ... remaining states
        ];
    }

    public static function stateNames(): array
    {
        return array_combine(array_keys(static::all()), array_keys(static::all()));
    }

    public static function lgasFor(?string $state): array
    {
        $lgas = static::all()[$state] ?? [];
        return array_combine($lgas, $lgas);
    }
}