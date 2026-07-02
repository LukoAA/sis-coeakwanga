<?php

namespace Modules\Academics\Filament\Resources\Schools;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Academics\Filament\Resources\Schools\Pages\CreateSchool;
use Modules\Academics\Filament\Resources\Schools\Pages\EditSchool;
use Modules\Academics\Filament\Resources\Schools\Pages\ListSchools;
use Modules\Academics\Filament\Resources\Schools\Pages\ViewSchool;
use Modules\Academics\Filament\Resources\Schools\Schemas\SchoolForm;
use Modules\Academics\Filament\Resources\Schools\Schemas\SchoolInfolist;
use Modules\Academics\Filament\Resources\Schools\Tables\SchoolsTable;
use Modules\Academics\Models\School;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Academics';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SchoolForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchoolInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolsTable::configure($table);
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
            'index' => ListSchools::route('/'),
            'create' => CreateSchool::route('/create'),
            'view' => ViewSchool::route('/{record}'),
            'edit' => EditSchool::route('/{record}/edit'),
        ];
    }
}
