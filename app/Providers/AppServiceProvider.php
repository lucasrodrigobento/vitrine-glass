<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Blade::directive('active', function ($expression) {
            return "<?php
                \$__activeArgs   = [{$expression}];
                \$__activeName   = \$__activeArgs[0];
                \$__activeParams = \$__activeArgs[1] ?? [];
                \$__activeMatch  = request()->routeIs(\$__activeName)
                    && (empty(\$__activeParams) || collect(\$__activeParams)->every(
                        fn(\$v, \$k) => (string) request()->route(\$k) === (string) \$v
                    ));
                echo \$__activeMatch ? 'active' : '';
                unset(\$__activeArgs, \$__activeName, \$__activeParams, \$__activeMatch);
            ?>";
        });
    }
}
