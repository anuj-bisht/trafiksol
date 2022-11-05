<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SiteActivity extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id','activity_id','quantity',
    ];


    
}
