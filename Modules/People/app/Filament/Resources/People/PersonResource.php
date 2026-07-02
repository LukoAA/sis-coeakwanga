<?php

namespace Modules\People\Filament\Resources\People;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\People\Filament\Resources\People\Pages\CreatePerson;
use Modules\People\Filament\Resources\People\Pages\EditPerson;
use Modules\People\Filament\Resources\People\Pages\ListPeople;
use Modules\People\Filament\Resources\People\Schemas\PersonForm;
use Modules\People\Filament\Resources\People\Tables\PeopleTable;
use Modules\People\Models\Person;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;
    protected static string | \UnitEnum | null $navigationGroup = 'People';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PersonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeopleTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPeople::route('/'),
            'create' => CreatePerson::route('/create'),
            'edit' => EditPerson::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
