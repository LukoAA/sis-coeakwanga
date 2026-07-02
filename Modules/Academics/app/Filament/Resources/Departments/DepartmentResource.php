<?php

namespace Modules\Academics\Filament\Resources\Departments;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Academics\Filament\Resources\Departments\Pages\CreateDepartment;
use Modules\Academics\Filament\Resources\Departments\Pages\EditDepartment;
use Modules\Academics\Filament\Resources\Departments\Pages\ListDepartments;
use Modules\Academics\Filament\Resources\Departments\Pages\ViewDepartment;
use Modules\Academics\Filament\Resources\Departments\Schemas\DepartmentForm;
use Modules\Academics\Filament\Resources\Departments\Schemas\DepartmentInfolist;
use Modules\Academics\Filament\Resources\Departments\Tables\DepartmentsTable;
use Modules\Academics\Models\Department;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Academics';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepartmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
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
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'view' => ViewDepartment::route('/{record}'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }
}
