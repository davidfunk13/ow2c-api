<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRoundRequest;
use App\Http\Requests\UpdateGameRoundRequest;
use App\Http\Resources\GameRoundResource;
use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GameRoundController extends Controller
{
    public function store(StoreGameRoundRequest $request, Game $game): JsonResponse
    {
        $round = DB::transaction(function () use ($request, $game) {
            $round = $game->gameRounds()->create([
                ...$request->safe()->except(['heroes']),
                'round_number' => ($game->gameRounds()->max('round_number') ?? 0) + 1,
            ]);
            $this->syncHeroes($round, $request);

            return $round;
        });

        return GameRoundResource::make($round->load(['mapSubmap', 'roundHeroes.hero']))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateGameRoundRequest $request, GameRound $round): JsonResponse
    {
        DB::transaction(function () use ($request, $round) {
            $round->update($request->safe()->except(['heroes']));

            if ($request->has('heroes')) {
                $this->syncHeroes($round, $request);
            }
        });

        return GameRoundResource::make($round->fresh(['mapSubmap', 'roundHeroes.hero']))->response();
    }

    public function destroy(GameRound $round): JsonResponse
    {
        $round->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function syncHeroes(GameRound $round, FormRequest $request): void
    {
        $heroIds = $request->validated('heroes');

        if ($heroIds === null) {
            return;
        }

        $round->roundHeroes()->delete();

        foreach ($heroIds as $heroId) {
            $round->roundHeroes()->create(['hero_id' => $heroId]);
        }
    }
}
