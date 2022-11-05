<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class HardwareRequestDoc extends Model
{
    protected $table = 'hardware_request_docs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hardware_request_id','image','type','file_path',
    ];


    
}
