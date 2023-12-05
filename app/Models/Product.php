<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    /**
     * The Product that belong to the Subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscriptions(): BelongsToMany {
        return $this->belongsToMany(Subscription::class);
    }
}