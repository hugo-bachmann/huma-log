<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\Filament\StaffPanelProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    AppPanelProvider::class,
    StaffPanelProvider::class,
    FortifyServiceProvider::class,
];
