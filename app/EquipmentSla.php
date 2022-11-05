<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class EquipmentSla extends Model
{
    protected $table = "equipment_slas";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    
    protected $fillable = [
        'name','hours_allocated','sla_type',
    ];
  
   public static function getSlaByTypeList($type='equipment') {
     return self::where('sla_type',$type)->pluck('name','id')->sortBy('name');
   }

   public static function getSlaByType($type='equipment') {
      return self::where('sla_type',$type)->orderBy('name')->get();
    }

   public static function getSlaById($id){
     return self::where('id',$id)->first();
   }
   public static function getAllSla(){
      return self::where('sla_type',"equipment")->pluck('name','id')->sortBy('name');
   }
   
   
}
