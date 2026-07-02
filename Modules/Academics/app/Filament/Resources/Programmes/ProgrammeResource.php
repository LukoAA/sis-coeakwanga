<?php

namespace Modules\Academics\Filament\Resources\Programmes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Academics\Filament\Resources\Programmes\Pages\CreateProgramme;
use Modules\Academics\Filament\Resources\Programmes\Pages\EditProgramme;
use Modules\Academics\Filament\Resources\Programmes\Pages\ListProgrammes;
use Modules\Academics\Filament\Resources\Programmes\Pages\ViewProgramme;
use Modules\Academics\Filament\Resources\Programmes\Schemas\ProgrammeForm;
use Modules\Academics\Filament\Resources\Programmes\Schemas\ProgrammeInfolist;
use Modules\Academics\Filament\Resources\Programmes\Tables\ProgrammesTable;
use Modules\Academics\Models\Programme;

class ProgrammeResource extends Resource
{
    protected static ?string $model = Programme::class;
    
    protected static string | \UnitEnum | null $navigationGroup = 'Academics';
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    

    public static function form(Schema $schema): Schema
    {
        return ProgrammeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProgrammeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgrammesTable::configure($table);
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
            'index' => ListProgrammes::route('/'),
            'create' => CreateProgramme::route('/create'),
            'view' => ViewProgramme::route('/{record}'),
            'edit' => EditProgramme::route('/{record}/edit'),
        ];
    }
}