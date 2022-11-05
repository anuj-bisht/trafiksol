<?php

  namespace App;
  use App\Ticket;
  use Illuminate\Database\Eloquent\Model;
  use DB;

class TicketComment extends Model
{

   // protected $table = "ticket_comments";

    public function ticket(){
        return $this->belongsTo('Ticket','ticket_id','id');
    }

    public static function getCommentsByTicketId($ticket_id){
      $result = DB::table('ticket_comments')
      ->select('ticket_comments.*','users.name as username')
      ->join('tickets', 'tickets.id', '=', 'ticket_comments.ticket_id')
      ->join('users', 'users.id', '=', 'ticket_comments.comment_by')
      ->where('ticket_comments.ticket_id',$ticket_id)
      ->orderBy('ticket_comments.id','Desc')->get();
      return $result;
    }
    public static function getClosingCommentsByTicketId($id)
    {
        $result = DB::table('ticket_close_requests')
      ->select('ticket_close_requests.*','users.name as username')
      ->join('users', 'users.id', '=', 'ticket_close_requests.request_by')
      ->where('ticket_close_requests.ticket_id',$id)
      ->orderBy('ticket_close_requests.id','Desc')->get();
      return $result;
    }
}
