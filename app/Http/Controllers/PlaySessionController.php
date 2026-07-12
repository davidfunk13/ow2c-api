<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaySessionRequest;
use App\Http\Requests\UpdatePlaySessionRequest;
use App\Http\Resources\PlaySessionResource;
use App\Models\PlaySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaySessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sessions = $request->user()
            ->playSessions()
            ->withCount('games')
            ->orderByDesc('started_at')
            ->paginate(20);

        return PlaySessionResource::collection($sessions)->response();
    }

    public function store(StorePlaySessionRequest $request): JsonResponse
    {
        $session = $request->user()->playSessions()->create([
            'title' => $request->title,
            'notes' => $request->notes,
            'started_at' => $request->started_at ?? now(),
            'ended_at' => $request->ended_at,
        ]);

        return PlaySessionResource::make($session)->response()->setStatusCode(201);
    }

    public function show(PlaySession $playSession): JsonResponse
    {
        $playSession->load(['games' => function ($query) {
            $query->with(['map', 'gameHeroes.hero', 'gameRounds'])
                ->orderBy('played_at');
        }]);
        $playSession->loadCount('games');

        return PlaySessionResource::make($playSession)->response();
    }

    public function update(UpdatePlaySessionRequest $request, PlaySession $playSession): JsonResponse
    {
        $playSession->update($request->validated());

        return PlaySessionResource::make($playSession)->response();
    }

    public function destroy(PlaySession $playSession): JsonResponse
    {
        $playSession->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function end(PlaySession $playSession): JsonResponse
    {
        $playSession->update(['ended_at' => now()]);

        return PlaySessionResource::make($playSession)->response();
    }
}
