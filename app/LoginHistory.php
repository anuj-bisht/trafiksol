<?php
 
namespace App;
use DB; 
use Illuminate\Database\Eloquent\Model;
 
class LoginHistory extends Model
{
     protected $table = 'login_history';
     
     public function user(){
      return $this->belongsTo('App\User');
     }

     protected $fillable = [
        'user_id','mark_datetime','geo_point','ostype','image','file_path','entry_type'
    ];
    
    public static function getAttendanceByUser($user_id){
       
         $query = DB::table('login_history')
         ->select('login_history.id','users.name',DB::raw("MIN(login_history.mark_datetime) AS first_entry, MAX(login_history.mark_datetime) AS last_entry")
         ,'login_history.ostype','login_history.geo_point',
         DB::raw("DATE_FORMAT(login_history.mark_datetime,'%d-%m-%Y') as date_column1"),
         DB::raw("CAST(login_history.mark_datetime AS DATE) as date_column"),
         DB::raw("CAST(login_history.mark_datetime AS time) as time_column"),
         'login_history.entry_type')
         ->join('users','users.id','=','login_history.user_id')
         ->groupBy(DB::raw('DATE(login_history.mark_datetime)'))
         ->where('login_history.user_id',$user_id);              
         $query = $query->orderBy('login_history.id','Desc');
         return $query;
       
       
    }

    public static function getAttendanceByUserWithDate($user_id,$date){
       
      $query = DB::table('login_history')
      ->select('login_history.id','users.name',
      'login_history.mark_datetime','login_history.ostype',
       DB::raw("DATE_FORMAT(login_history.mark_datetime,'%d-%m-%Y') as date_column"),
       DB::raw("CAST(login_history.mark_datetime AS time) as time_column"),
      'login_history.geo_point','login_history.entry_type')
      ->join('users','users.id','=','login_history.user_id')      
      ->where('login_history.user_id',$user_id)         
      ->where(DB::raw('DATE(login_history.mark_datetime)'),$date);         
      $query = $query->orderBy('login_history.id','Desc');
      return $query;
    
    
   }

}
