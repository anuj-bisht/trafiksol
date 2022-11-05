<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
use DB;  
class Notification extends Model
{
    

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject','message','mobile_message','notification_message'
    ];
    
    public static function getSubjectList(){
      return self::where('id','<>',0)->pluck('subject','id')->sortBy('subject');
    }
    
    public static function getDataByItemCode($itemcode,$site_id){
        $result = DB::table('role_notifications')
            ->select('notifications.*','users.id as user_id','users.name as username',
            'users.email','users.phone','users.email_notification','users.sms_notification')                        
            ->join('notifications','notifications.id','=','role_notifications.notification_id')
            ->join('roles', 'role_notifications.role_id', '=', 'roles.id')                                            
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'roles.id')      
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')      
            ->join('site_users', 'site_users.user_id', '=', 'users.id') 
            ->join('sites', 'sites.id', '=', 'site_users.site_id') 
            ->groupBy('users.id')
            ->where('notifications.itemcode',$itemcode)                           
            ->where('sites.id',$site_id)
            ->get();

        return $result;


        // $result = DB::table('notifications')
        //     ->select('notifications.*','users.id as user_id',
        //     'users.name as username','users.email','users.phone','users.email_notification',
        //     'users.sms_notification')                                    
        //     ->join('site_users', 'site_users.site_id', '=', 'users.id') 
        //     ->join('sites', 'sites.id', '=', 'site_users.site_id') 
        //     ->where('sites.id',$site_id)                           
        //     ->where('notifications.itemcode',$itemcode)                           
        //     ->get();

        // return $result;


    }
    
}
