<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
use DB;

class EquipmentRequest extends Model
{
    protected $table = "equipment_requests";    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id','requested_by','equipment_id','description','quantity','finish_date',
    ];
        
    
}
