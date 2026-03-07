<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->required(),
            Textarea::make('notes')->columnSpanFull(),
            // ... other fields
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('invoice.student.name'),
            TextColumn::make('amount')->money('PHP'),
            TextColumn::make('payment_method'),
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'warning',
                }),
            TextColumn::make('proof.file_path')
                ->label('Proof')
                ->formatStateUsing(fn ($state) => $state ? 'View' : '-')
                ->url(fn ($state) => $state ? Storage::url($state) : null),
        ])
        ->actions([
            Action::make('approve')
                ->action(function (Payment $record) {
                    $record->update(['status' => 'approved', 'verified_by' => auth()->id()]);
                    Notification::make()->success()->title('Approved!')->send();
                })
                ->requiresConfirmation()
                ->visible(fn (Payment $record) => $record->status === 'pending'),
            Action::make('reject')
                ->form([
                    Textarea::make('notes')->required(),
                ])
                ->action(function (array $data, Payment $record) {
                    $record->update(['status' => 'rejected', 'notes' => $data['notes']]);
                })
                ->requiresConfirmation(),
        ])
        ->bulkActions([
            // ...
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
