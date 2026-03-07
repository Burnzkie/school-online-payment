<?php

namespace App\Filament\Admin\Resources\FeeCategoryResource\Pages;

use App\Filament\Admin\Resources\FeeCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeCategory extends EditRecord
{
    protected static string $resource = FeeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
