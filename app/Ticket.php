<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\User;
use App\TicketComment;


class Ticket extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
      'subject','issue_type_id','created_by','assign_to','ticket_category_id','site_id','equipment_id',
      'stretch_point','priority','description','sla_start','sla_end','is_paused'
    ];

    public function site(){
      return $this->belongsTo('App\Site');
    }

    public function equipment(){
      return $this->belongsTo('App\Equipment');
    }

    public function ticket_issue_type(){
      return $this->belongsTo('App\TicketIssueType','issue_type_id','id');
    }

    public function ticket_category(){
      return $this->belongsTo('App\TicketCategory','ticket_category_id','id');
    }

    public function ticket_comment(){

      return $this->hasMany('TicketComment','ticket_id');
    }


    public static function getTicketList(){
        $result = DB::table('tickets')
            ->select('tickets.*','ticket_issue_types.name as issue_type_name',
            'equipments.title as equipment_title','ticket_categories.name as ticket_category_name',
            'sites.name as site_name'
            )
            ->join('ticket_issue_types', 'tickets.issue_type_id', '=', 'ticket_issue_types.id')
            ->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->join('sites', 'tickets.site_id', '=', 'sites.id')
            ->join('equipments', 'tickets.equipment_id', '=', 'equipments.id');
            //->groupBy('clients.id')

        return $result;
    }

    public static function getTodayTicketList($site_id,$list){
        $result = DB::table('tickets')
        ->where('site_id',$site_id);
        if($list){
          $result = $result->where(DB::raw('DATE(created_at)'),date('Y-m-d'))->get();
        }else{
          $result = $result->where(DB::raw('DATE(created_at)'),date('Y-m-d'))->count();
        }
        return $result;
    }

    public static function getMonthTicketList($site_id,$list){
        $result = DB::table('tickets')
        ->where('site_id',$site_id);
        if($list){
          $result = $result->where(DB::raw('MONTH(created_at)'),date('m'))->get();
        }else{
          $result = $result->where(DB::raw('MONTH(created_at)'),date('m'))->count();
        }

        return $result;
    }

    public static function getAssignToMeList($user_id,$site_id,$list){
        $result = DB::table('tickets')
        ->where('site_id',$site_id);
        if($list){
          $result = $result->where('assign_to',$user_id)->get();
        }else{
          $result = $result->where('assign_to',$user_id)->count();
        }

        return $result;
    }

    public static function getCreatedByMeList($user_id,$site_id,$list){
        $result = DB::table('tickets')
        ->where('site_id',$site_id);
        if($list){
          $result = $result->where('created_by',$user_id)->get();
        }else{
          $result = $result->where('created_by',$user_id)->count();
        }

        return $result;
    }

    public static function assignTicketToUser($site_id){
      $result = DB::table('users')
      ->select('users.*',DB::raw('count(tickets.assign_to) as users_count'))
      ->leftJoin('tickets', 'tickets.assign_to', '=', 'users.id')
      ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
      ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
      ->join('site_users', 'site_users.user_id', '=', 'users.id')
      ->groupBy('users.id')
      ->where('model_has_roles.role_id',4)
      ->where('site_users.site_id',$site_id)
      ->groupBy('users.id')
      ->orderBy('tickets.assign_to')->first();
      return $result;


    }

    public static function getTicketId($ticket_id){
      $result = DB::table('tickets')
      ->select('tickets.*','ticket_issue_types.name as issue_type_name',
            'equipments.title as equipment_title','ticket_categories.name as ticket_category_name',
            'sites.name as site_name','users.name as username','u1.name as assig_user','ticket_assets.image as t_image'
      )
      ->leftJoin('ticket_issue_types', 'tickets.issue_type_id', '=', 'ticket_issue_types.id')
      ->leftJoin('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
      ->leftJoin('sites', 'tickets.site_id', '=', 'sites.id')
      ->leftJoin('ticket_assets', 'ticket_assets.ticket_id', '=', 'tickets.id')
      ->leftJoin('equipments', 'tickets.equipment_id', '=', 'equipments.id')
      ->leftJoin('users','users.id','=','tickets.created_by')
      ->leftJoin('users as u1','u1.id','=','tickets.assign_to')
      ->where('tickets.id',$ticket_id)
      ->orderBy('tickets.id')->get();

      return $result;
    }

    public static function getUserByTicketId($ticket_id){
      $result = DB::table('tickets')
      ->select('users.*')
      ->join('users', 'users.id', '=', 'tickets.assign_to')
      ->where('tickets.id',$ticket_id)->get();
      return $result;
    }

    public static function getTicketListByType($type="",$search="",$user_id,$site_id){

      $query = self::select('tickets.*','ticket_issue_types.name as issue_type_name',
      'equipments.title as equipment_title','ticket_categories.name as ticket_category_name',
      'sites.name as site_name','users.name as username','u1.name as assig_user'
      )
      ->join('ticket_issue_types', 'tickets.issue_type_id', '=', 'ticket_issue_types.id')
      ->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
      ->join('sites', 'tickets.site_id', '=', 'sites.id')
      ->join('equipments', 'tickets.equipment_id', '=', 'equipments.id')
      ->join('users','users.id','=','tickets.created_by')
      ->join('users as u1','u1.id','=','tickets.assign_to')
      ->where('tickets.site_id',$site_id);

      if($type=="my_ticket" && $type != "all"){
        $query = $query->orWhere('tickets.created_by',$user_id);
        $query = $query->orWhere('tickets.assign_to',$user_id);
      }

      if($type != "all" ){
        $query = $query->where('tickets.status',$type);
      }

      return $query;
      //->paginate(20);
    }
    public static function getTicketWithComment($site_id,$ticket_id){
      $query = TicketComment::select('ticket_comments.*','users.name')
      ->join('users','users.id','=','ticket_comments.comment_by')
      ->where('ticket_id',$ticket_id)
      ->get();

      return $query;
    }

    public static function answredTicket($user_id,$site_id){
      $query = self::select('tickets.*','ticket_issue_types.name as issue_type_name',
      'equipments.title as equipment_title','ticket_categories.name as ticket_category_name',
      'sites.name as site_name','users.name as username','u1.name as assig_user'
      )
      ->join('ticket_issue_types', 'tickets.issue_type_id', '=', 'ticket_issue_types.id')
      ->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
      ->join('sites', 'tickets.site_id', '=', 'sites.id')
      ->join('equipments', 'tickets.equipment_id', '=', 'equipments.id')
      ->join('users','users.id','=','tickets.created_by')
      ->join('users as u1','u1.id','=','tickets.assign_to')
      ->join('ticket_comments','ticket_comments.ticket_id','=','tickets.id')
      ->where('tickets.site_id',$site_id)
      ->where('ticket_comments.comment_by','<>',$user_id);

      return $query;
    }

    public static function getTicketByRemainingTime($site_id,$percentage=0){

      $where = "";
      if($percentage==25){
        $where = "BETWEEN 0 and $percentage";
      }else if($percentage==50){
        $where = "BETWEEN 25 and $percentage";
      }else if($percentage==100){
        $where = "BETWEEN 50 and $percentage";
      }else{
        $where = " <= $percentage";
      }

      $result = DB::select("SELECT * FROM
      (SELECT *, 100-ROUND((TIMEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d %H:%i:%s'),sla_start) / TIMEDIFF(sla_end,sla_start))*100,2) AS remaining
      FROM tickets) AS a WHERE remaining $where and site_id = $site_id");
      return $result;
    }

    public static function getTicketRemaining($site_id,$ticket_id){


      $result = DB::select("SELECT
      IF(pause_total > 0 ,
        SEC_TO_TIME(TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(DATE_ADD(sla_start, INTERVAL total SECOND),INTERVAL pause_total SECOND))),
       SEC_TO_TIME(TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(sla_start, INTERVAL total SECOND)))
      ) AS remaining_time
     FROM (
       SELECT tickets.*,
       DATE_FORMAT(NOW(),'%Y-%m-%d %H:%i:%s'),
       TIME_TO_SEC(TIMEDIFF(tickets.sla_end,tickets.sla_start)) AS total,
       SUM(TIME_TO_SEC(TIMEDIFF(ticket_pauses.pause_to,ticket_pauses.pause_from))) AS pause_total
       FROM tickets
       LEFT JOIN ticket_pauses ON ticket_pauses.ticket_id = tickets.id
       GROUP BY ticket_pauses.ticket_id
     ) AS a where a.id=$ticket_id and a.site_id = $site_id
     ");
      return $result;


    }
}
