<?php

namespace App\Filament\Resources\InactiveUsers\Pages;

use App\Filament\Resources\InactiveUsers\InactiveUsersResource;
use Filament\Resources\Pages\ListRecords;

class ListInactiveUsers extends ListRecords
{
    protected static string $resource = InactiveUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
