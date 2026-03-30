<?php

namespace App\Filament\Resources\InactiveUsers\Schemas;

use Filament\Schemas\Schema;

class InactiveUsersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
