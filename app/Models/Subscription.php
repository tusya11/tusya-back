<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'is_favourite',
    ];

    /**
     * The users that belong to the Subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the product associated with the Subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product(): HasOne {
        return $this->hasOne(Product::class, 'id', 'product_id');

    }
}