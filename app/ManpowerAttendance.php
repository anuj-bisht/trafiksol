<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  use DB;
class ManpowerAttendance extends Model
{
        
	protected $table = 'manpower_attendances';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','attendance'
    ];

    public static function getTodayManPowerAttendace($type=2,$site_id){
      $clients = DB::table('manpower_attendances')
          ->select('manpower_attendances.attendance', 'users.name as username','roles.name as role_name')
          ->join('users', 'users.id', '=', 'manpower_attendances.user_id')
          ->join('type_users', 'type_users.id', '=', 'users.type_user_id')                         
          ->join('site_users', 'site_users.user_id', '=', 'users.id')
          ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
          ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                
          ->where('site_users.site_id',$site_id)
          ->where('type_users.id',$type)
          ->where('type_users.id',$type)
          ->where(DB::raw('DATE(manpower_attendances.created_at)'),date('Y-m-d'))
          ->groupBy('manpower_attendances.id')
          ->orderBy('users.name')
          ->get();

      return $clients;
    }     
       
}
