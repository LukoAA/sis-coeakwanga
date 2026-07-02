<?php

namespace Modules\People\Filament\Resources\Enrolments;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\People\Filament\Resources\Enrolments\Pages\CreateEnrolment;
use Modules\People\Filament\Resources\Enrolments\Pages\EditEnrolment;
use Modules\People\Filament\Resources\Enrolments\Pages\ListEnrolments;
use Modules\People\Filament\Resources\Enrolments\Schemas\EnrolmentForm;
use Modules\People\Filament\Resources\Enrolments\Tables\EnrolmentsTable;
use Modules\People\Models\Enrolment;

class EnrolmentResource extends Resource
{
    protected static ?string $model = Enrolment::class;
    protected static string | \UnitEnum | null $navigationGroup = 'People';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EnrolmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnrolmentsTable::configure($table);
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
            'index' => ListEnrolments::route('/'),
            'create' => CreateEnrolment::route('/create'),
            'edit' => EditEnrolment::route('/{record}/edit'),
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
