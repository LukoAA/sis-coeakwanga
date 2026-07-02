<?php

namespace Modules\Academics\Filament\Resources\Subjects;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Academics\Filament\Resources\Subjects\Pages\CreateSubject;
use Modules\Academics\Filament\Resources\Subjects\Pages\EditSubject;
use Modules\Academics\Filament\Resources\Subjects\Pages\ListSubjects;
use Modules\Academics\Filament\Resources\Subjects\Pages\ViewSubject;
use Modules\Academics\Filament\Resources\Subjects\Schemas\SubjectForm;
use Modules\Academics\Filament\Resources\Subjects\Schemas\SubjectInfolist;
use Modules\Academics\Filament\Resources\Subjects\Tables\SubjectsTable;
use Modules\Academics\Models\Subject;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Academics';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SubjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubjectsTable::configure($table);
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
            'index' => ListSubjects::route('/'),
            'create' => CreateSubject::route('/create'),
            'view' => ViewSubject::route('/{record}'),
            'edit' => EditSubject::route('/{record}/edit'),
        ];
    }
}
