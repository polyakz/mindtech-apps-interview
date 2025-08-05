<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

/**
 * App\Models\Menu
 *
 * @property int $id
 * @property int $restaurant_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Restaurant $restaurant
 */
#[OA\Schema(
    schema: 'Menu',
    title: 'Menu',
    required: ['id', 'name', 'restaurant_id'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'restaurant_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Lunch Menu'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class Menu extends Model
{
    protected $fillable = ['name', 'restaurant_id'];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
