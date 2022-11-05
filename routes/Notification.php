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
        'subject','message','mobile_message','notification_message','role_id'
    ];
    
    public static function getSubjectList(){
      return self::where('id','<>',0)->pluck('subject','id')->sortBy('subject');
    }
    
    public static function getDataByItemCode($itemcode){
        $result = DB::table('notifications')
            ->select('notifications.*','users.id as user_id','users.name as username','users.email','users.phone')                        
            ->join('roles', 'notifications.role_id', '=', 'roles.id')                                            
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'roles.id')      
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')      
            ->where('notifications.itemcode',$itemcode)                           
            ->get();

        return $result;
    }
    
}
