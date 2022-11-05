<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
  protected $table = 'vehicles';
  
  public function type_vehicle(){
      return $this->belongsTo('App\TypeVehicle');
  }

  /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'type_vehicle_id','name','vehicle_number'
    ];

    public static function getVehicleByType($id){
      return self::where('type_vehicle_id',$id)->orderBy('name')->get();
    }
}
