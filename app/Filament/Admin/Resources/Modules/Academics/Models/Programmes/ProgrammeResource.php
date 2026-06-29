<?php

namespace App\Filament\Admin\Resources\Modules\Academics\Models\Programmes;

use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Pages\CreateProgramme;
use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Pages\EditProgramme;
use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Pages\ListProgrammes;
use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Pages\ViewProgramme;
use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Schemas\ProgrammeForm;
use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Schemas\ProgrammeInfolist;
use App\Filament\Admin\Resources\Modules\Academics\Models\Programmes\Tables\ProgrammesTable;
use App\Models\Modules\Academics\Models\Programme;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProgrammeResource extends Resource
{
    protected static ?string $model = Programme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Programme';

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
