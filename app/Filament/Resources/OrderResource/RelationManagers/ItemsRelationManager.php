<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return 'Line Items';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('PKR'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('line_total')
                    ->money('PKR'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
