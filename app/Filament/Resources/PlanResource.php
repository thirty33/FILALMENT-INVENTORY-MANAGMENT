<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers\UsersRelationManager;
use App\Models\Plan;
use Faker\Provider\Text;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationIcon = 'eos-product-subscriptions';

    public static function getLabel(): ?string
    {
        return __('Plan');
    }

    public static function getNavigationLabel(): string
    {
        return __('Planes');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Nombre'))
                    ->autofocus()
                    ->required()
                    ->unique(static::getModel(), 'name', ignoreRecord: true)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set, ?string $old, ?string $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug'),
                TextInput::make('description')
                    ->required()
                    ->maxLength(200)
                    ->label(__('Descripción')),
                TextInput::make('price')
                    ->label(__('Precio'))
                    ->required()
                    ->maxLength(100)
                    ->suffix('€'),
                Checkbox::make('active')
                    ->label(__('Activo')),
                Checkbox::make('featured')
                    ->label(__('Destacado')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('Nombre'))
                    ->description(fn(Plan $plan) => $plan->description),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug')),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->money('eur')
                    ->label(__('Precio')),
                Tables\Columns\ToggleColumn::make('active')
                    ->label(__('Activo')),
                Tables\Columns\ToggleColumn::make('featured')
                    ->label(__('Destacado')),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateDescription(__('No hay planes disponibles'));
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
