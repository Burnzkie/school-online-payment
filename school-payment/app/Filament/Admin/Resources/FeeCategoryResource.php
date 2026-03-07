<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeeCategoryResource\Pages;
use App\Filament\Admin\Resources\FeeCategoryResource\RelationManagers;
use App\Models\FeeCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeeCategoryResource extends Resource
{
    protected static ?string $model = FeeCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeCategories::route('/'),
            'create' => Pages\CreateFeeCategory::route('/create'),
            'edit' => Pages\EditFeeCategory::route('/{record}/edit'),
        ];
    }
}
