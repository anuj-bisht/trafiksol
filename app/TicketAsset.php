<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class TicketAsset extends Model
{
    protected $table = "ticket_assets";    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id','type','image','file_path',
    ];
        
}
