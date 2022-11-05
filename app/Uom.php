<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class Uom extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','type'
    ];

    public static function getUOMList($type="dropdown"){
        
      if($type=="dropdown"){
        return self::pluck('name','id')->sortBy("name");
      }else{
        return self::where('id','<>',0)->get();
      }
        
    }


      
}
