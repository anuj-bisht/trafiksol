<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class SiteSla extends Model
{
    protected $table = "site_slas";    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id','sla_id',
    ];
            
}
