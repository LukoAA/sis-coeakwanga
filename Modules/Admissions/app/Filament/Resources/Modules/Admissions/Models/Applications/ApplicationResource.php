<?php

namespace Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications;

use App\Models\Modules\Admissions\Models\Application;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Pages\CreateApplication;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Pages\EditApplication;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Pages\ListApplications;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Pages\ViewApplication;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Schemas\ApplicationForm;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Schemas\ApplicationInfolist;
use Modules\Admissions\Filament\Resources\Modules\Admissions\Models\Applications\Tables\ApplicationsTable;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Admissions';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'applicant_surname';

    public static function form(Schema $schema): Schema
    {
        return ApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicationsTable::configure($table);
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
            'index' => ListApplications::route('/'),
            'create' => CreateApplication::route('/create'),
            'view' => ViewApplication::route('/{record}'),
            'edit' => EditApplication::route('/{record}/edit'),
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
