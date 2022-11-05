<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class ExpenceDprImage extends Model
{
    protected $table = 'expence_dpr_images';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'expence_dpr_id','image','type','file_path',
    ];

        
}
