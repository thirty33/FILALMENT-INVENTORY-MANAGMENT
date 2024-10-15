<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 20;

    public static function getNavigationGroup(): ?string
    {
        return __('Almacén');
    }

    public static function getLabel(): ?string
    {
        return __('Producto');
    }

    protected static ?string $navigationLabel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->label(__('Imagen'))
                    ->image()
                    ->maxSize(4096)
                    ->placeholder(__('Imagen del producto'))
                    ->columnSpanFull(),
                Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->autofocus()
                            ->required()
                            ->minLength(2)
                            ->maxLength(200)
                            ->unique(static::getModel(), 'name', ignoreRecord: true)
                            ->label(__('Nombre'))
                            ->columns(1),
                        TextInput::make('price')
                            ->required()
                            ->minLength(2)
                            ->maxLength(200)
                            ->label(__('Precio'))
                            ->columns(1),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->label(__('Categoría'))
                            ->searchable()
                            ->columns(1)

                    ])->columns(3),
                Textarea::make('description')
                    ->required()
                    ->minLength(2)
                    ->maxLength(200)
                    ->label(__('Description'))
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Imagen')),
                TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('Precio'))
                    ->sortable()
                    ->money('usd'),
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Fecha de creación'))
                    ->sortable()
                    ->date('d/m/Y H:i'),
                TextColumn::make('updated_at')
                    ->label(__('Fecha de actualización'))
                    ->sortable()
                    ->date('d/m/Y H:i')
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label(__('Categoría'))
                    // ->searchable()
            ])
            ->actions([

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('No hay productos disponibles'));;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ImageEntry::make('image')
                ->hiddenLabel()
                ->columnSpanFull(),
            Section::make()->schema([
                
            ])->columns(3)
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
