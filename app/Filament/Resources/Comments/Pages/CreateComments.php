<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Filament\Resources\Comments\CommentsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComments extends CreateRecord
{
    protected static string $resource = CommentsResource::class;
}
