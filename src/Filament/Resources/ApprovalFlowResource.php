<?php

namespace XBigDaddyx\Gatekeeper\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource\Pages;
use Xbigdaddyx\Gatekeeper\Models\ApprovalFlow;

class ApprovalFlowResource extends Resource
{
    public static function isScopedToTenant(): bool
    {
        return config('gatekeeper.approval_flow.scope_to_tenant', true);
    }

    public static function getNavigationIcon(): ?string
    {
        return config('gatekeeper.approval_flow.icon');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('gatekeeper.approval_flow.should_register_navigation', true);
    }

    public static function getModel(): string
    {
        return config('gatekeeper.approval_flow.model', ApprovalFlow::class);
    }

    public static function getLabel(): string
    {
        return config('gatekeeper.approval_flow.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('gatekeeper.approval_flow.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('gatekeeper.approval_flow.sort');
    }

    public static function getPluralLabel(): string
    {
        return config('gatekeeper.approval_flow.plural_label');
    }

    public static function getCluster(): ?string
    {
        return config('gatekeeper.approval_flow.cluster', null);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('approvable_type')->required(),
                Select::make('job_title_id')
                    ->relationship('jobTitle', 'title')
                    ->nullable(),
                TextInput::make('role')->nullable(),
                TextInput::make('step_order')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                Toggle::make('is_parallel'),
                \AbdelhamidErrahmouni\FilamentMonacoEditor\MonacoEditor::make('condition')
                    ->helperText('JSON format: {"field": "amount", "operator": "<", "value": 1000}')
                    ->columnSpanFull()
                    ->disablePreview()
                    ->language('json')
                    ->theme('dracula'),
                // TextInput::make('condition')
                //     ->helperText('JSON format: {"field": "amount", "operator": "<", "value": 1000}')
                //     ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('approvable_type'),
                TextColumn::make('jobTitle.title'),
                TextColumn::make('role'),
                TextColumn::make('step_order'),
                IconColumn::make('is_parallel')->boolean(),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalFlows::route('/'),
            'create' => Pages\CreateApprovalFlow::route('/create'),
            'edit' => Pages\EditApprovalFlow::route('/{record}/edit'),
        ];
    }
}
