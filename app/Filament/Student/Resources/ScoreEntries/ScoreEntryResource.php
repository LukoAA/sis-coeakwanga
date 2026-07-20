<?php

namespace App\Filament\Student\Resources\ScoreEntries;

use App\Filament\Student\Resources\ScoreEntries\Pages\CreateScoreEntry;
use App\Filament\Student\Resources\ScoreEntries\Pages\EditScoreEntry;
use App\Filament\Student\Resources\ScoreEntries\Pages\ListScoreEntries;
use App\Filament\Student\Resources\ScoreEntries\Pages\ViewScoreEntry;
use App\Filament\Student\Resources\ScoreEntries\Schemas\ScoreEntryForm;
use App\Filament\Student\Resources\ScoreEntries\Schemas\ScoreEntryInfolist;
use App\Filament\Student\Resources\ScoreEntries\Tables\ScoreEntriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
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

    protected static ?string $navigationLabel = 'My Results';

    protected static ?string $modelLabel = 'result';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $enrolmentIds = \Modules\People\Models\Enrolment::where(
            'person_id', auth()->user()?->person_id ?? 0
        )->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('enrolment_id', $enrolmentIds)
            ->where('status', \Modules\Assessments\Models\ScoreEntry::STATUS_PUBLISHED);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScoreEntries::route('/'),
            'view' => Pages\ViewScoreEntry::route('/{record}'),
        ];
    }
}
