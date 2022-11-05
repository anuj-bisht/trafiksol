<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SiteVehicle extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id','vehicle_id','quantity',
    ];


    
}
