<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuditTrailController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', AuditLog::class);

        $query = AuditLog::query()->with(['organization', 'user'])->orderByDesc('created_at')->orderByDesc('id');

        if ($organizationId = $request->integer('organization_id')) {
            $query->where('organization_id', $organizationId);
        }

        if ($entityType = $request->string('entity_type')->toString()) {
            $query->where('entity_type', $entityType);
        }

        if ($userId = $request->integer('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($action = $request->string('action')->toString()) {
            $query->where('action', $action);
        }

        if ($dateFrom = $request->string('date_from')->toString()) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->string('date_to')->toString()) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $entries = $query->paginate(25)->withQueryString();

        $loggedUserIds = AuditLog::query()->distinct()->pluck('user_id');

        return view('audit-trail.index', [
            'entries' => $entries,
            'organizations' => Organization::orderBy('name')->get(),
            'entityTypes' => AuditLog::query()->distinct()->orderBy('entity_type')->pluck('entity_type'),
            'actions' => AuditLog::query()->distinct()->orderBy('action')->pluck('action'),
            'users' => User::whereIn('id', $loggedUserIds)->orderBy('name')->get(),
            'filters' => $request->only(['organization_id', 'entity_type', 'user_id', 'action', 'date_from', 'date_to']),
        ]);
    }
}
