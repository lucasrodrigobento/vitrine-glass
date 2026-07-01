<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        $tenant = $this->resolveTenant($host);

        if (!$tenant) {
            abort(404, "Tenant não encontrado para: {$host}");
        }

        app()->instance('tenant', $tenant);
        config(['tenant' => $tenant->toConfigArray()]);

        return $next($request);
    }

    private function resolveTenant(string $host): ?Tenant
    {
        // Em desenvolvimento local, usa variável de ambiente
        if ($this->isLocalhost($host)) {
            $slug = env('TENANT_SLUG', 'lider-vidros');
            return Cache::remember("tenant:slug:{$slug}", 300, fn() =>
                Tenant::with(['services', 'features'])->where('slug', $slug)->where('ativo', true)->first()
            );
        }

        return Cache::remember("tenant:{$host}", 300, fn() =>
            Tenant::with(['services', 'features'])->where('dominio', $host)->where('ativo', true)->first()
        );
    }

    private function isLocalhost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1'])
            || str_contains($host, ':')
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.localhost');
    }
}
