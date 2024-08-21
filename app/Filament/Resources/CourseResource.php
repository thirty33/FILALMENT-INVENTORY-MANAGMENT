<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'eos-machine-learning-o';

    protected static ?int $navigationSort = 30;

    public static function getLabel(): ?string
    {
        return __('Curso');
    }

    public static function getNavigationLabel(): string
    {
        return __('Cursos');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(__('Datos del curso'))
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label(__('Imagen del curso'))
                                ->image()
                                ->required()
                                ->directory('courses')
                                ->columnSpanFull(),
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Select::make('user_id')
                                        ->label(__('Profesor'))
                                        ->required()
                                        ->options(
                                            User::teachers()
                                                ->active()
                                                ->get()
                                                ->pluck('name', 'id'),
                                        ),
                                    TextInput::make('name')
                                        ->label(__('Nombre'))
                                        ->autofocus()
                                        ->required()
                                        ->minLength(6)
                                        ->maxLength(200)
                                        ->unique(static::getModel(), 'name', ignoreRecord: true)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function (Set $set, ?string $old, ?string $state) {
                                            $set('slug', Str::slug($state));
                                        }),
                                    TextInput::make('slug'),
                                ]),
                            Forms\Components\RichEditor::make('description')
                                ->toolbarButtons([
                                    'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'h2',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'undo',
                                ])
                                ->label(__('Descripción'))
                                ->required()
                                ->minLength(10)
                                ->maxLength(2000)
                                ->columnSpanFull(),
                        ]),
                    Forms\Components\Wizard\Step::make(__('Configuración'))
                        ->schema([
                            Forms\Components\Checkbox::make('published')
                                ->label(__('Publicado')),
                            Forms\Components\Checkbox::make('featured')
                                ->label(__('Destacado')),
                        ]),
                    Forms\Components\Wizard\Step::make(__('Unidades'))
                        ->schema([
                            Forms\Components\Repeater::make('units')
                                ->relationship()
                                ->label(__('Unidades'))
                                ->addActionLabel(__('Añadir unidad'))
                                ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                ->reorderableWithButtons()
                                ->collapsible()
                                ->cloneable()
                                ->orderColumn()
                                ->schema([
                                    Forms\Components\Grid::make()
                                        ->schema([
                                            TextInput::make('name')
                                                ->label(__('Nombre'))
                                                ->autofocus()
                                                ->required()
                                                ->minLength(6)
                                                ->maxLength(200)
                                                ->unique(static::getModel(), 'name', ignoreRecord: true)
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(function (Set $set, ?string $old, ?string $state) {
                                                    $set('slug', Str::slug($state));
                                                }),
                                            TextInput::make('slug'),
                                        ]),
                                    Forms\Components\RichEditor::make('content')
                                        ->toolbarButtons([
                                            'attachFiles',
                                            'blockquote',
                                            'bold',
                                            'bulletList',
                                            'codeBlock',
                                            'h2',
                                            'h3',
                                            'italic',
                                            'link',
                                            'orderedList',
                                            'redo',
                                            'strike',
                                            'undo',
                                        ])
                                        ->label(__('Contenido de la unidad'))
                                        ->required()
                                        ->maxLength(2000)
                                        ->columnSpanFull(),
                                    Forms\Components\Checkbox::make('published')
                                        ->label(__('Publicado')),
                                    Forms\Components\Checkbox::make('free')
                                        ->label(__('Gratuito')),
                                ])
                        ])
                ])
                    ->columnSpanFull()
                    ->persistStepInQueryString('course-wizard-step'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('Imagen')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('Profesor'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('published')
                    ->label(__('Publicado')),
                Tables\Columns\ToggleColumn::make('featured')
                    ->label(__('Destacado')),
                Tables\Columns\TextColumn::make('units_count')
                    ->label(__('Unidades'))
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->counts('units'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creado'))
                    ->sortable()
                    ->date('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
