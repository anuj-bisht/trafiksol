<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class TypeVehicle extends Model
{
    protected $table = 'type_vehicles';
    protected $fillable = ['name'];
    //protected $hidden = ['_token'];


    public function vechicle()
    {
        return $this->hasMany('App\Vehicle');
    }

    public static function getVehicleTypeList(){
        return self::where('id','<>',0)->pluck('name','id')->sortBy('name');
    }
}
