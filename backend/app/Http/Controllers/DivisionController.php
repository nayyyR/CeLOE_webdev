<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use App\Models\Division;
use Illuminate\Http\JsonResponse;

class DivisionController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(
            data: Division::withCount(['users', 'tickets'])->get(),
            message: 'Divisions retrieved successfully.',
        );
    }

    public function show(Division $division): JsonResponse
    {
        return $this->successResponse(
            data: $division->loadCount(['users', 'tickets']),
            message: 'Division retrieved successfully.',
        );
    }

    public function store(StoreDivisionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $division = Division::create($data);

        return $this->successResponse(
            data: $division,
            message: 'Division created successfully.',
            code: 201,
        );
    }

    public function update(UpdateDivisionRequest $request, Division $division): JsonResponse
    {
        $data = $request->validated();

        $division->update($data);

        return $this->successResponse(
            data: $division,
            message: 'Division updated successfully.',
        );
    }

    public function destroy(Division $division): JsonResponse
    {
        $division->delete();

        return $this->successResponse(message: 'Division deleted successfully.');
    }
}
