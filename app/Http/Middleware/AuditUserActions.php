<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditUserActions
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $routeName = $request->route()?->getName();

        if ($request->user() && $response->getStatusCode() === 403) {
            AuditLog::record(
                'authorization_failed',
                'Access was denied to a protected application route',
                $request->user(),
                [
                    'response_status' => 403,
                    'route_parameters' => collect($request->route()?->parameters() ?? [])
                        ->map(fn ($value) => $value instanceof Model ? $value->getKey() : $value)
                        ->all(),
                ]
            );

            return $response;
        }

        if ($request->user()
            && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && ! in_array($routeName, ['login', 'logout', 'register'], true)) {
            $action = str_replace('.', '_', $routeName ?: 'user_action');

            AuditLog::record(
                $action,
                $this->description($routeName, $request->method()),
                $request->user(),
                [
                    'response_status' => $response->getStatusCode(),
                    'route_parameters' => collect($request->route()?->parameters() ?? [])
                        ->map(fn ($value) => $value instanceof Model ? $value->getKey() : $value)
                        ->all(),
                ]
            );
        }

        return $response;
    }

    private function description(?string $routeName, string $method): string
    {
        return match ($routeName) {
            'admin.loan.contract.send' => 'Sent a loan contract to a client',
            'admin.loan.approve' => 'Final-approved a signed loan contract',
            'admin.loan.reject' => 'Rejected a loan request',
            'admin.payment.approve' => 'Approved a payment',
            'admin.payment.reject' => 'Rejected a payment',
            'admin.verifications.approve' => 'Approved a client verification',
            'admin.verifications.reject' => 'Rejected a client verification',
            'admin.access.admins.store' => 'Created an administrator account',
            'admin.access.admins.update' => 'Updated administrator account information',
            'admin.access.admins.status' => 'Changed administrator account access',
            'admin.access.users.update' => 'Updated a managed user account',
            'admin.access.users.status' => 'Changed a managed user account access',
            'admin.settings.profile.update' => 'Updated administrator profile settings',
            'admin.settings.password.update' => 'Changed administrator account password',
            'admin.settings.motion.update' => 'Updated administrator motion preference',
            'two-factor.enable' => 'Started two-factor authentication setup',
            'two-factor.setup.verify' => 'Enabled two-factor authentication',
            'two-factor.disable' => 'Disabled two-factor authentication',
            'loan.contract.sign' => 'Digitally signed and submitted a loan contract',
            'loan.store' => 'Submitted a loan request',
            default => ucfirst(strtolower($method)).' request to '.($routeName ?: 'an application route'),
        };
    }
}
