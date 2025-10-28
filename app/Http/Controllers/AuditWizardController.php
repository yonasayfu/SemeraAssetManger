<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Http\Requests\AuditWizardStoreRequest;
use App\Services\AuditService;

class AuditWizardController extends Controller
{
    public function __construct(protected AuditService $auditService)
    {
    }

    public function create()
    {
        $this->authorize('create', Audit::class);
        return Inertia::render('Audits/Wizard', [
            'steps' => [
                ['id' => 'info', 'name' => 'Audit Information'],
                ['id' => 'assets', 'name' => 'Select Assets'],
                ['id' => 'review', 'name' => 'Review and Start'],
            ],
            'initialData' => [], // You can pass initial data if needed
        ]);
    }

    public function store(AuditWizardStoreRequest $request)
    {
        $this->authorize('create', Audit::class);
        $audit = $this->auditService->start($request->validated());

        return redirect()->route('audits.scan', ['audit' => $audit->id]);
    }
}