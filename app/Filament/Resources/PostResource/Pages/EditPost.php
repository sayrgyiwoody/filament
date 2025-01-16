<?php

namespace App\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\UserResource\Widgets\PostHello;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


}
