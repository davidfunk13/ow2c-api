<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncGameSnapshotsRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GameSnapshotController extends Controller
{
    public function update(SyncGameSnapshotsRequest $request, Game $game): JsonResponse
    {
        $userId = $request->user()->id;

        DB::transaction(function () use ($request, $game, $userId) {
            if ($request->has('ranks')) {
                $game->rankSnapshots()->delete();
                foreach ($request->validated('ranks') ?? [] as $rank) {
                    $game->rankSnapshots()->create([
                        ...$rank,
                        'user_id' => $userId,
                        'recorded_at' => now(),
                    ]);
                }
            }

            if ($request->has('hero_srs')) {
                $game->heroSrSnapshots()->delete();
                foreach ($request->validated('hero_srs') ?? [] as $heroSr) {
                    $game->heroSrSnapshots()->create([
                        ...$heroSr,
                        'user_id' => $userId,
                        'recorded_at' => now(),
                    ]);
                }
            }
        });

        $game->load(Game::DETAIL_RELATIONS);

        return GameResource::make($game)->response();
    }
}
