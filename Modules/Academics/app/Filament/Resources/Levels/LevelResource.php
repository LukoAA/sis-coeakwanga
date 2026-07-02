<?php

namespace Modules\Academics\Filament\Resources\Levels;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Academics\Filament\Resources\Levels\Pages\CreateLevel;
use Modules\Academics\Filament\Resources\Levels\Pages\EditLevel;
use Modules\Academics\Filament\Resources\Levels\Pages\ListLevels;
use Modules\Academics\Filament\Resources\Levels\Pages\ViewLevel;
use Modules\Academics\Filament\Resources\Levels\Schemas\LevelForm;
use Modules\Academics\Filament\Resources\Levels\Schemas\LevelInfolist;
use Modules\Academics\Filament\Resources\Levels\Tables\LevelsTable;
use Modules\Academics\Models\Level;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Academics';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LevelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LevelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LevelsTable::configure($table);
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
            'index' => ListLevels::route('/'),
            'create' => CreateLevel::route('/create'),
            'view' => ViewLevel::route('/{record}'),
            'edit' => EditLevel::route('/{record}/edit'),
        ];
    }
}
