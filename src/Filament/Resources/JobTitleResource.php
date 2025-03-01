<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource\Pages;
use Xbigdaddyx\Gatekeeper\Models\JobTitle;

class JobTitleResource extends Resource
{
    public static function isScopedToTenant(): bool
    {
        return config('gatekeeper.job_title.scope_to_tenant', true);
    }

    public static function getNavigationIcon(): ?string
    {
        return config('gatekeeper.job_title.icon');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('gatekeeper.job_title.should_register_navigation', true);
    }

    public static function getModel(): string
    {
        return config('gatekeeper.job_title.model', JobTitle::class);
    }

    public static function getLabel(): string
    {
        return config('gatekeeper.job_title.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('gatekeeper.job_title.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('gatekeeper.job_title.sort');
    }

    public static function getPluralLabel(): string
    {
        return config('gatekeeper.job_title.plural_label');
    }

    public static function getCluster(): ?string
    {
        return config('gatekeeper.job_title.cluster', null);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required()->unique(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('users_count')->counts('users'),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobTitles::route('/'),
            'create' => Pages\CreateJobTitle::route('/create'),
            'edit' => Pages\EditJobTitle::route('/{record}/edit'),
        ];
    }
}
