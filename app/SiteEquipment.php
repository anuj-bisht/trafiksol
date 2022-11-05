<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SiteEquipment extends Model
{
    protected $table = "site_equipments"; 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id','equipment_id','location','sla_id','chainage',
    ];


    
}
