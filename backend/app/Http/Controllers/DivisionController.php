<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisionController extends Controller
{
    public function index(): JsonResponse {
        return response()->json(
            Division::withCount([
                'users',
                'tickets',
            ])->get()
        );
    }

    public function show(Division $division): JsonResponse {
        return response()->json(
            $division->loadCount([
                'users',
                'tickets',
            ])
        );
    }

    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:divisions,name',
            ],
        ]);

        $division = Division::create($data);

        return response()->json([
            'message' => 'Division created successfully.',
            'division' => $division,
        ], 201);
    }

    public function update(Request $request, Division $division): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('divisions', 'name')
                    ->ignore($division->id),
            ],
        ]);

        $division->update($data);

        return response()->json([
            'message' => 'Division updated successfully.',
            'division' => $division,
        ]);
    }

    public function destroy(Division $division): JsonResponse {
        $division->delete();

        return response()->json([
            'message' => 'Division deleted successfully.',
        ]);
    }
}