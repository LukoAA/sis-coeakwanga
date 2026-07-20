<?php

namespace App\Filament\Student\Resources\Registrations;

use App\Filament\Student\Resources\Registrations\Pages\CreateRegistration;
use App\Filament\Student\Resources\Registrations\Pages\EditRegistration;
use App\Filament\Student\Resources\Registrations\Pages\ListRegistrations;
use App\Filament\Student\Resources\Registrations\Pages\ViewRegistration;
use App\Filament\Student\Resources\Registrations\Schemas\RegistrationForm;
use App\Filament\Student\Resources\Registrations\Schemas\RegistrationInfolist;
use App\Filament\Student\Resources\Registrations\Tables\RegistrationsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Registration\Models\Registration;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

   protected static ?string $navigationLabel = 'My Registration';

    protected static ?string $modelLabel = 'course registration';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $enrolmentIds = \Modules\People\Models\Enrolment::where(
            'person_id', auth()->user()?->person_id ?? 0
        )->pluck('id');

        return parent::getEloquentQuery()->whereIn('enrolment_id', $enrolmentIds);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }
}
