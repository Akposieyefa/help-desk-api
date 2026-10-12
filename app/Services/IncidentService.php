<?php

namespace App\Services;

use App\Models\Incident;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class IncidentService
{
    /**
     * @return LengthAwarePaginator
     */
    public function list(): LengthAwarePaginator
    {
        return Incident::with('user')->latest()->paginate(10);
    }

    public function create(array $attributes): Incident
    {
        return Incident::create([
            'user_id' => $attributes['user_id'] ?? auth()->id(),
            'type' => $attributes['type'],
            'title' => $attributes['title'],
            'description' => $attributes['description'],
            'data' => $attributes['data'] ?? now(),
            'acting_officer' => $attributes['acting_officer'] ?? null,
        ]);
    }

    public function show(int $id): Incident
    {
        return Incident::with('user')->findOrFail($id);
    }

    public function update(int $id, array $attributes): Incident
    {
        $incident = Incident::findOrFail($id);

        $incident->update([
            'user_id' => $attributes['user_id'] ?? $incident->user_id ?? auth()->id(),
            'type' => $attributes['type'] ?? $incident->type,
            'title' => $attributes['title'] ?? $incident->title,
            'description' => $attributes['description'] ?? $incident->description,
            'data' => $attributes['data'] ?? $incident->data,
            'acting_officer' => $attributes['acting_officer'] ?? $incident->acting_officer,
        ]);

        return $incident->fresh(['user']);
    }

    public function delete(int $id): bool
    {
        $incident = Incident::findOrFail($id);

        return (bool) $incident->delete();
    }
}
