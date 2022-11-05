<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class Equipment extends Model
{
    protected $table = "equipments";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function brand(){
        return $this->belongsTo('App\Brand');
    }
    
    public function models(){
        return $this->belongsTo('App\Models','model_id','id');
    }

    
    protected $fillable = [
        'title', 'brand_id','model_id','uom_id',
    ];
  
    public static function getEquipmentByModelId($model_id){
        return self::where('model_id',$model_id)->get();
    }

    public static function validateEquipment($id){
        return self::where('id',$id)->first();
    }
}
