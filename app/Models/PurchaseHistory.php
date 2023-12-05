<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseHistory extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Get all of the users for the PurchaseHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany {
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    /**
     * Get all of the products for the PurchaseHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
}