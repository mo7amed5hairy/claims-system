<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Claims\Models\ClaimEntity;
use App\Modules\Claims\Services\EntityService;
use Illuminate\Http\Request;

/**
 * Claim Entity Controller
 * 
 * Handles management of claim entities (Insurance companies, ministries, etc)
 */
class ClaimEntityController extends Controller
{
    protected $entityService;

    public function __construct(EntityService $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * Display list of claim entities
     */
    public function index()
    {
        $this->authorize('viewAny', ClaimEntity::class);
        $entities = ClaimEntity::all();
        return view('claims::entities.index', compact('entities'));
    }

    /**
     * Show create entity form
     */
    public function create()
    {
        return view('claims::entities.create');
    }

    /**
     * Store new entity
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:claim_entities,name',
            'type' => 'nullable|string|max:255',
        ]);

        $this->entityService->createEntity($data);

        return redirect()->route('entities.index')
            ->with('success', trans('messages.entity_created_successfully'));
    }

    /**
     * Show edit entity form
     */
    public function edit(ClaimEntity $entity)
    {
        return view('claims::entities.edit', compact('entity'));
    }

    /**
     * Update entity
     */
    public function update(Request $request, ClaimEntity $entity)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:claim_entities,name,' . $entity->id,
            'type' => 'nullable|string|max:255',
        ]);

        $this->entityService->updateEntity($entity->id, $data);

        return redirect()->route('entities.index')
            ->with('success', trans('messages.entity_updated_successfully'));
    }

    /**
     * Delete entity
     */
    public function destroy(ClaimEntity $entity)
    {
        $this->entityService->deleteEntity($entity->id);

        return redirect()->route('entities.index')
            ->with('success', trans('messages.entity_deleted_successfully'));
    }
}
