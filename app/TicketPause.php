<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
use DB;

class TicketPause extends Model
{
    protected $table = "ticket_pauses";    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id','user_id','pause_from','pause_to','reason','is_approved',
    ];
        
    public static function getPauseList($site_id=0){
      $result = DB::table('ticket_pauses')
            ->select('ticket_pauses.*','users.name as username','tickets.subject')
            ->join('tickets', 'tickets.id', '=', 'ticket_pauses.ticket_id')                                    
            ->join('users', 'users.id', '=', 'ticket_pauses.user_id');
            if($site_id){
              $result = $result->where('tickets.site_id',$site_id);
            }                                    
            
        return $result;
    }
}
