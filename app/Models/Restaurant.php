<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

/**
 * App\Models\Restaurant
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Menu[] $menus
 * @property-read int|null $menus_count
 */
#[OA\Schema(
    schema: 'Restaurant',
    title: 'Restaurant',
    description: 'Restaurant model',
    required: ['id', 'name', 'address'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Pizza House'),
        new OA\Property(property: 'address', type: 'string', example: '123 Main St, Berlin'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'RestaurantWithMenus',
    title: 'Restaurant with menus',
    required: ['id', 'name', 'address', 'menus'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Pizza House'),
        new OA\Property(property: 'address', type: 'string', example: '123 Main St, Berlin'),
        new OA\Property(property: 'menus', type: 'array', items: new OA\Items(ref: '#/components/schemas/Menu')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class Restaurant extends Model
{
    protected $fillable = ['name', 'address'];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
