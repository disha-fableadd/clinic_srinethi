<?php

namespace App\Http\Middleware;

use App\Models\Modules;
use App\Models\UserPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        if ((string) $user->role_id === '2') {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($this->shouldSkipPermissionCheck($request, $routeName)) {
            return $next($request);
        }

        $module = $this->resolveModule($request, $routeName);

        if (!$module) {
            return $next($request);
        }

        $action = $this->resolveAction($request, $routeName);

        $hasPermission = UserPermission::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where($action, 1)
            ->exists();

        if (!$hasPermission) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page');
        }

        return $next($request);
    }

    protected function shouldSkipPermissionCheck(Request $request, ?string $routeName): bool
    {
        $skipRouteNames = [
            'dashboard',
            'profile',
            'profile.edit',
            'clinic.settings.update',
            'notificationView',
            'notification.update',
            'hide.today.appointments',
        ];

        return in_array($routeName, $skipRouteNames, true)
            || strtolower((string) $request->segment(1)) === 'dashboard';
    }

    protected function resolveModule(Request $request, ?string $routeName): ?Modules
    {
        $candidates = $this->buildModuleCandidates($request, $routeName);

        if (empty($candidates)) {
            return null;
        }

        return Modules::query()->get()->first(function ($module) use ($candidates) {
            return in_array($this->normalizeModuleKey($module->name), $candidates, true);
        });
    }

    protected function buildModuleCandidates(Request $request, ?string $routeName): array
    {
        $firstSegment = $request->segment(1) ?? '';
        $routePrefix = $routeName ? explode('.', $routeName)[0] : '';

        $aliases = [
            'appointment' => 'appointments',
            'appointments' => 'appointments',
            'assigntherapy' => 'assignedtherapy',
            'knowledge' => 'knowledgebase',
            'medicine' => 'medicines',
            'medicines' => 'medicines',
            'opd' => 'opdvisit',
            'ot' => 'otprocedure',
            'otproceduresall' => 'otprocedure',
            'patient' => 'patients',
            'patients' => 'patients',
            'pathologyreports' => 'pathologyreport',
            'radiologyreports' => 'radiologyreport',
            'radiologytests' => 'radiologytest',
            'report' => 'medicalreport',
            'service' => 'services',
            'services' => 'services',
            'treatment' => 'treatments',
            'treatments' => 'treatments',
        ];

        $rawCandidates = array_filter([$firstSegment, $routePrefix]);
        $candidates = [];

        foreach ($rawCandidates as $candidate) {
            $normalized = $this->normalizeModuleKey($candidate);

            if ($normalized === '') {
                continue;
            }

            $candidates[] = $normalized;
            $candidates[] = Str::singular($normalized);

            if (isset($aliases[$normalized])) {
                $candidates[] = $aliases[$normalized];
            }
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    protected function normalizeModuleKey(string $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', Str::lower($value));
    }

    protected function resolveAction(Request $request, ?string $routeName): string
    {
        if (
            $request->isMethod('delete')
            || Str::contains((string) $routeName, ['.delete', '.destroy'])
        ) {
            return 'delete';
        }

        if (
            $request->isMethod('put')
            || $request->isMethod('patch')
            || $request->is('*edit*')
            || Str::contains((string) $routeName, '.edit')
        ) {
            return 'update';
        }

        if (
            $request->isMethod('post')
            || $request->is('*create*')
            || Str::contains((string) $routeName, '.create')
        ) {
            return 'create';
        }

        return 'view';
    }
}
