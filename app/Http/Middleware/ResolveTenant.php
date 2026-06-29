<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        $slug = $this->resolveSlug($host);

        $configFile = config_path("tenants/{$slug}.php");

        if (!file_exists($configFile)) {
            abort(404, "Tenant não encontrado: {$slug}");
        }

        config(['tenant' => require $configFile]);

        return $next($request);
    }

    private function resolveSlug(string $host): string
    {
        $map = [];

        foreach (glob(config_path('tenants/*.php')) as $file) {
            $cfg = require $file;
            $map[$cfg['dominio']]              = $cfg['slug'];
            $map[$cfg['slug'] . '.test']       = $cfg['slug'];
            $map[$cfg['slug'] . '.localhost']  = $cfg['slug'];
        }

        if (in_array($host, ['localhost', '127.0.0.1']) || str_contains($host, ':')) {
            return env('TENANT_SLUG', 'box-vidros');
        }

        return $map[$host] ?? env('TENANT_SLUG', 'box-vidros');
    }
}
