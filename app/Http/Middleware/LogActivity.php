<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;

class LogActivity
{
    /**
     * Handle an incoming request.
     * Automatically log important activities
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only log for authenticated users
        if (!Auth::check()) {
            return $response;
        }
        
        // Only log POST, PUT, PATCH, DELETE requests (state-changing operations)
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }
        
        // Skip logging for certain routes
        $skipRoutes = [
            'logout',
            'login',
        ];
        
        if ($request->routeIs($skipRoutes)) {
            return $response;
        }
        
        // Determine action type based on HTTP method
        $actionType = match($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'action',
        };
        
        // Get route name or path
        $routeName = $request->route()?->getName() ?? $request->path();
        
        // Get model from route parameters if exists
        $model = null;
        $modelId = null;
        
        $routeParams = $request->route()?->parameters() ?? [];
        foreach ($routeParams as $key => $value) {
            if (is_object($value) && method_exists($value, 'getKey')) {
                $model = get_class($value);
                $modelId = $value->getKey();
                break;
            }
        }
        
        // Create activity log
        try {
            ActivityLog::create([
                'userId' => Auth::id(),
                'action' => $actionType,
                'description' => $this->generateDescription($request, $actionType, $routeName),
                'loggableType' => $model,
                'loggableId' => $modelId,
                'ipAddress' => $request->ip(),
                'userAgent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the request if logging fails
            Log::error('Activity logging failed: ' . $e->getMessage());
        }
        
        return $response;
    }
    
    /**
     * Generate human-readable description
     */
    private function generateDescription(Request $request, string $actionType, string $routeName): string
    {
        $user = Auth::user();
        $userName = $user->name ?? $user->email;
        
        // Try to create meaningful description from route name
        $parts = explode('.', $routeName);
        $resource = ucfirst($parts[0] ?? 'resource');
        
        return match($actionType) {
            'create' => "{$userName} created a new {$resource}",
            'update' => "{$userName} updated {$resource}",
            'delete' => "{$userName} deleted {$resource}",
            default => "{$userName} performed action on {$resource}",
        };
    }
}
