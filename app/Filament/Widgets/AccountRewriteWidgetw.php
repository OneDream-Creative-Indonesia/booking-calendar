<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AccountRewriteWidgetw extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static string $view = 'filament.widgets.account-rewrite-widgetw';
}
