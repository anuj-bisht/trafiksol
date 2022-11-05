<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class TicketCategory extends Model
{
        

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','parent_id'
    ];

    public static function getTopTicketCategoryList(){
        return self::where('parent_id',0)->pluck('name','id')->sortBy('name');
    }

    public static function getTicketList(){
        return self::where('parent_id',0)->orderBy('name')->get();
    }
    
    public static function getAllTicketCat(){
        return self::where('id','<>',0)->orderBy('name')->get();
    }
        
}
