<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get tenant from header, subdomain, or request parameter
        $tenantId = $request->header('X-Tenant-ID') 
            ?? $request->input('tenant_id')
            ?? $this->getTenantFromSubdomain($request);

        if ($tenantId) {
            $tenant = is_numeric($tenantId) 
                ? Tenant::find($tenantId) 
                : Tenant::where('slug', $tenantId)->first();

            if ($tenant && $tenant->is_active) {
                // Check if user belongs to this tenant
                if ($request->user() && !$request->user()->belongsToTenant($tenant)) {
                    return response()->json([
                        'message' => 'You do not have access to this tenant.'
                    ], 403);
                }

                // Set tenant in request
                $request->attributes->set('tenant', $tenant);
                app()->instance('tenant', $tenant);
            } else {
                return response()->json([
                    'message' => 'Invalid or inactive tenant.'
                ], 404);
            }
        }

        return $next($request);
    }

    /**
     * Get tenant from subdomain.
     */
    private function getTenantFromSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // If we have a subdomain (more than 2 parts)
        if (count($parts) > 2) {
            return $parts[0]; // Return subdomain as potential tenant slug
        }

        return null;
    }
}

