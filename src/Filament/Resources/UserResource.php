<?php

namespace XBigDaddyx\Gatekeeper\Filament\Resources;

use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use XBigDaddyx\Gatekeeper\Filament\Resources\UserResource\Pages;
use XBigDaddyx\Gatekeeper\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    public static function isScopedToTenant(): bool
    {
        return config('gatekeeper.user.scope_to_tenant', true);
    }

    public static function getNavigationIcon(): ?string
    {
        return config('gatekeeper.user.icon');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('gatekeeper.user.should_register_navigation', true);
    }

    public static function getModel(): string
    {
        return config('gatekeeper.user.model', \App\Models\User::class);
    }

    public static function getLabel(): string
    {
        return config('gatekeeper.user.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('gatekeeper.user.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('gatekeeper.user.sort');
    }

    public static function getPluralLabel(): string
    {
        return config('gatekeeper.user.plural_label');
    }

    public static function getCluster(): ?string
    {
        return config('gatekeeper.user.cluster', null);
    }

    public static function getTenantOwnershipRelationshipName(): string
    {
        return config('gatekeeper.user.tenant_ownership_name');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\Select::make('job_title_id')
                    ->relationship(name: 'jobTitle', titleAttribute: 'title')
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('roles')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
                    })
                    ->multiple()
                    ->preload()
                    ->searchable(),
                // Forms\Components\MorphToSelect::make('roles')
                //     ->multiple()
                //     ->relationship('roles', 'name')
                //     ->searchable()
                //     ->preload()
                //     ->allowHtml()
                //     ->getOptionLabelFromRecordUsing(function (\Illuminate\Database\Eloquent\Model $record) {
                //         return "<span class='text-primary-500 font-bold'>{$record->name}</span><br>{$record->description}";
                //     }),
                // Forms\Components\TextInput::make('password')

                //     ->password()
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->defaultImageUrl('/images/avatar/default-avatar.jpg')
                    ->label('')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('email')
                    ->color('primary')
                    ->icon('fluentui-mail-16-o')
                    ->sortable()
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->state(fn ($record) => (bool) $record->email_verified_at)
                    ->tooltip(function (Model $record) {
                        if ((bool) $record->email_verified_at) {
                            return __('tooltip.verified');
                        }

                        return __('tooltip.unverified');
                    })
                    ->boolean()
                    ->trueIcon('fluentui-mail-checkmark-16-o')
                    ->falseIcon('fluentui-mail-dismiss-16-o')
                    ->sortable()
                    ->label('Verified')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roles.name')
                    ->icon('fluentui-ribbon-star-24-o')
                    ->color('danger'),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->label('Verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('unverified')
                    ->label('Unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_activities')
                    ->label('Activities')
                    ->icon('heroicon-m-bolt')
                    ->color('purple')
                    ->url(fn ($record) => UserResource::getUrl('activities', ['record' => $record])),
                Tables\Actions\Action::make('assignRole')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(Role::query()->pluck('name', 'name'))
                            ->required(),
                    ])
                    ->action(function (array $data, Model $record): void {
                        $record->assignRole($data['role']);
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RolesRelationManager::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewUser::class,
            Pages\EditUser::class,
            Pages\ManageUserRoles::class,
            Pages\ViewUserActivities::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'roles' => Pages\ManageUserRoles::route('/{record}/roles'),
            'activities' => Pages\ViewUserActivities::route('/{record}/activities'),
        ];
    }
}
