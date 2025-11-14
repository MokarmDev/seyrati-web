<?php

namespace Botble\BotbleSitemap;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::disableForeignKeyConstraints();

        // Add cleanup logic here if needed (drop tables, etc.)

        Schema::enableForeignKeyConstraints();
    }
}
