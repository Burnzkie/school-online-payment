<?php

namespace App\Filament\Admin\Resources\FeeStructureResource\Pages;

use App\Filament\Admin\Resources\FeeStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeStructures extends ListRecords
{
    protected static string $resource = FeeStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
