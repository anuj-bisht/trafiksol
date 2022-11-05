<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class CategoryVendor extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','parent_id'
    ];

    
    public static function getCategoryList(){
        return self::where('parent_id',0)->pluck('name','id')->sortBy('name');
    }

    public static function getVendorCategoryList(){
        return self::where('parent_id',0)->orderBy('name')->get();
    }
  
    
    
}
