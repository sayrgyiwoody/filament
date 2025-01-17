<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use App\Filament\Exports\UserExporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\UserResource\Pages;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Filament\Actions\Exports\Enums\ExportFormat;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->placeholder('John Doe'),
                TextInput::make('email')
                    ->required()
                    ->placeholder('johndoe@example.com'),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->placeholder('Enter password'),
                Select::make('role')
                    ->options(User::ROLES),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->toggleable(),
                TextColumn::make('name')
                    ->sortable()->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->sortable()->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')
                    ->color(function (string $state): string {
                        return match ($state) {
                            'ADMIN' => 'danger',
                            'EDITOR' => 'info',
                            'USER' => 'success',
                        };
                    })
                    ->badge()->sortable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(UserExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
