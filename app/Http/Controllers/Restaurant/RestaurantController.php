<?php

namespace App\Http\Controllers\Restaurant;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class RestaurantController
{
    #[OA\Get(
        path: "/restaurants",
        tags: ['Restaurants'],
        summary: "Get list of all restaurants",
        responses: [
            new OA\Response(
                response: 200,
                description: "List of restaurants",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Restaurant')
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $restaurants = Restaurant::all();

        return response()->json($restaurants);
    }

    #[OA\Get(
        path: '/restaurants/{id}',
        summary: 'Get restaurant by ID with menus',
        tags: ['Restaurants'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of restaurant to retrieve',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Restaurant details with menus',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/RestaurantWithMenus'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant not found'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $restaurant = Restaurant::with('menus')->findOrFail($id);

            return response()->json($restaurant);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }
    }

    #[OA\Get(
        path: '/api/restaurants/{id}/menu',
        summary: 'Get menus of a specific restaurant by ID',
        tags: ['Restaurants'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of the restaurant',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of menus for the restaurant',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Menu')
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant not found'
            )
        ]
    )]
    public function menus(int $id): JsonResponse
    {
        try {
            $restaurant = Restaurant::findOrFail($id);

            return response()->json($restaurant->menus);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }
    }
}
