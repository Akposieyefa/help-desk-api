<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IncidentResource;
use App\Services\IncidentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class IncidentController extends Controller
{
    public function __construct(private readonly IncidentService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $incidents = $this->service->list();

        return IncidentResource::collection($incidents)->additional([
            'message' => 'All incidents fetched successfully',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $incident = $this->service->create([
                'user_id' => $request->user()?->id ?? auth()->id(),
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'data' => $request->data ?? now(),
                'acting_officer' => $request->acting_officer,
            ]);

            return response()->json([
                'message' => 'Incident reported successfully',
                'data' => new IncidentResource($incident->load('user')),
                'success' => true,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Sorry, unable to create incident',
                'error' => $e->getMessage(),
                'success' => false,
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $incident = $this->service->show((int) $id);

        return response()->json([
            'message' => 'Incident fetched successfully',
            'data' => new IncidentResource($incident),
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'acting_officer' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $incident = $this->service->update((int) $id, $request->only([
                'user_id',
                'acting_officer',
                'type',
                'title',
                'description',
                'data',
            ]));

            return response()->json([
                'message' => 'Incident details updated successfully',
                'data' => new IncidentResource($incident),
                'success' => true,
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Sorry, unable to update incident',
                'error' => $e->getMessage(),
                'success' => false,
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete((int) $id);

            return response()->json([
                'message' => $deleted ? 'Incident deleted successfully' : 'Incident not found',
                'success' => $deleted,
            ], $deleted ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Sorry, unable to delete incident',
                'error' => $e->getMessage(),
                'success' => false,
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
