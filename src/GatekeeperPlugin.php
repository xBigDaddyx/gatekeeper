<?php

namespace XBigDaddyx\Gatekeeper;

use Filament\Contracts\Plugin;
use Filament\Panel;
use XBigDaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource;
use XBigDaddyx\Gatekeeper\Filament\Resources\JobTitleResource;
use XBigDaddyx\Gatekeeper\Filament\Resources\UserResource;

class GatekeeperPlugin implements Plugin
{
    public function getId(): string
    {
        return 'gatekeeper';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ApprovalFlowResource::class,
                JobTitleResource::class,
                UserResource::class,
            ])
            ->pages([
                //
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
