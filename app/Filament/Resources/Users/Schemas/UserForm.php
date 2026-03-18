<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->maxLength(255)
                    ->confirmed()
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->label('Confirm Password')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false),
                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
