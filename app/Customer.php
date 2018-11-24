<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'address',
        'contacts',
        'tel',
    ];

    /**
     * Get the requirements for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requirements()
    {
        return $this->hasMany(CustomerRequirement::Class);
    }

    /**
     * Get the products for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'customer_product', 'customer_id', 'product_id');
    }
}
