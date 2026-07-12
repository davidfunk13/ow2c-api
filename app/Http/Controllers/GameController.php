<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $games = $request->user()->games()
            ->with(['map', 'gameHeroes.hero'])
            ->when($request->filled('result'), fn ($query) => $query->where('result', $request->query('result')))
            ->when($request->filled('role'), fn ($query) => $query->where('role_played', $request->query('role')))
            ->when($request->filled('queue_type'), fn ($query) => $query->where('queue_type', $request->query('queue_type')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('map_id'), fn ($query) => $query->where('map_id', $request->query('map_id')))
            ->when($request->filled('hero_id'), fn ($query) => $query->whereHas(
                'gameHeroes',
                fn ($heroQuery) => $heroQuery->where('hero_id', $request->query('hero_id'))
            ))
            ->when($request->filled('search'), fn ($query) => $query->where('notes', 'ilike', '%'.$request->query('search').'%'))
            ->orderByDesc('played_at')
            ->paginate(20)
            ->withQueryString();

        return GameResource::collection($games)->response();
    }

    public function store(StoreGameRequest $request): JsonResponse
    {
        $game = DB::transaction(function () use ($request) {
            $game = $request->user()->games()->create(
                $request->safe()->except(['heroes', 'primary_hero_id'])
            );
            $this->syncHeroes($game, $request);

            return $game;
        });

        return GameResource::make($game->load(['map', 'gameHeroes.hero']))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Game $game): JsonResponse
    {
        $game->load(Game::DETAIL_RELATIONS);

        return GameResource::make($game)->response();
    }

    public function update(UpdateGameRequest $request, Game $game): JsonResponse
    {
        DB::transaction(function () use ($request, $game) {
            $game->update($request->safe()->except(['heroes', 'primary_hero_id']));

            if ($request->has('heroes')) {
                $this->syncHeroes($game, $request);
            }
        });

        return GameResource::make($game->fresh(['map', 'gameHeroes.hero']))->response();
    }

    public function destroy(Game $game): JsonResponse
    {
        $game->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function syncHeroes(Game $game, FormRequest $request): void
    {
        $heroIds = $request->validated('heroes');

        if ($heroIds === null) {
            return;
        }

        $primary = $request->validated('primary_hero_id') ?? ($heroIds[0] ?? null);

        $game->gameHeroes()->delete();

        foreach ($heroIds as $heroId) {
            $game->gameHeroes()->create([
                'hero_id' => $heroId,
                'is_primary' => $heroId === $primary,
            ]);
        }
    }
}
