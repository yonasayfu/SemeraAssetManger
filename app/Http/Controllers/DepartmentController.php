<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Inertia\Inertia;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Department::class);

        $search = trim((string) $request->query('search', ''));
        $perPage = $request->integer('per_page', 5);

        $query = Department::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $departments = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Setup/Department/Index', [
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
            'can' => [
                'create' => auth()->user()->can('create', Department::class),
                'edit' => auth()->user()->can('update', Department::class),
                'delete' => auth()->user()->can('delete', Department::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Department::class);
        return Inertia::render('Setup/Department/Create', [
            'departments' => Department::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Department::class);
        Department::create($request->all());

        return redirect()->route('setup.departments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        return Inertia::render('Setup/Department/Edit', [
            'department' => $department,
            'departments' => Department::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);
        $department->update($request->all());

        return redirect()->route('setup.departments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        $department->delete();

        return redirect()->route('setup.departments.index');
    }
}
