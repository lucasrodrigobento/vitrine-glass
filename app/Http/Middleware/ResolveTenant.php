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
        if ($this->isLocalhost($host)) {
            $slug = env('TENANT_SLUG', 'lider-vidros');
            $id   = Cache::remember("tenant:id:slug:{$slug}", 300, fn () =>
                Tenant::where('slug', $slug)->where('ativo', true)->value('id')
            );
            return $id ? Tenant::with(['services', 'features'])->find($id) : null;
        }

        $id = Cache::remember("tenant:id:domain:{$host}", 300, fn () =>
            Tenant::where('dominio', $host)->where('ativo', true)->value('id')
        );
        return $id ? Tenant::with(['services', 'features'])->find($id) : null;
    }

    private function isLocalhost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1'])
            || str_contains($host, ':')
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.localhost');
    }
}
