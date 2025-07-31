<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    
    protected $fillable = ['image','name','slug','category_id','price','offer_price','short_description','long_description','sku','seo_title','seo_description','status','show_at_home'];

    function category() : BelongsTo {
        return $this->belongsTo(Category::class);
    }    
     
     protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/product/' . $image),
        );
    }
}
