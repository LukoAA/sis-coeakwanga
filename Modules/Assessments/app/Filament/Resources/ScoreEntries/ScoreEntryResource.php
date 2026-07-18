<?php

namespace Modules\Assessments\Filament\Resources\ScoreEntries;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Assessments\Filament\Resources\ScoreEntries\Pages\CreateScoreEntry;
use Modules\Assessments\Filament\Resources\ScoreEntries\Pages\EditScoreEntry;
use Modules\Assessments\Filament\Resources\ScoreEntries\Pages\ListScoreEntries;
use Modules\Assessments\Filament\Resources\ScoreEntries\Pages\ViewScoreEntry;
use Modules\Assessments\Filament\Resources\ScoreEntries\Schemas\ScoreEntryForm;
use Modules\Assessments\Filament\Resources\ScoreEntries\Schemas\ScoreEntryInfolist;
use Modules\Assessments\Filament\Resources\ScoreEntries\Tables\ScoreEntriesTable;
use Modules\Assessments\Models\ScoreEntry;

class ScoreEntryResource extends Resource
{
    protected static ?string $model = ScoreEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ScoreEntryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScoreEntryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScoreEntriesTable::configure($table);
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
            'index' => ListScoreEntries::route('/'),
            'create' => CreateScoreEntry::route('/create'),
            'view' => ViewScoreEntry::route('/{record}'),
            'edit' => EditScoreEntry::route('/{record}/edit'),
        ];
    }
}
