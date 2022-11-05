<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class ActivityDprImage extends Model
{
    protected $table = 'activity_dpr_images';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity_dpr_id','image','type','file_path',
    ];

        
}
