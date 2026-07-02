<?php

namespace Modules\Academics\Filament\Resources\Courses;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Academics\Filament\Resources\Courses\Pages\CreateCourse;
use Modules\Academics\Filament\Resources\Courses\Pages\EditCourse;
use Modules\Academics\Filament\Resources\Courses\Pages\ListCourses;
use Modules\Academics\Filament\Resources\Courses\Pages\ViewCourse;
use Modules\Academics\Filament\Resources\Courses\Schemas\CourseForm;
use Modules\Academics\Filament\Resources\Courses\Schemas\CourseInfolist;
use Modules\Academics\Filament\Resources\Courses\Tables\CoursesTable;
use Modules\Academics\Models\Course;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Academics';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursesTable::configure($table);
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
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view' => ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }
}
