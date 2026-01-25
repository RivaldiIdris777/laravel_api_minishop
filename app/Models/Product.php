<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    
    protected $fillable = ['image','name','slug','category_id','price','offer_price','short_description','long_description','sku','seo_title','seo_description','status','show_at_home'];

    function category() : BelongsTo {
        return $this->belongsTo(Category::class);
    }        
    
    public function getImageUrlAttribute()
{
    return asset('storage/product/' . $this->image);
}
}
