<?php
  
namespace App;
  
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

use DB;
  
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
  
    public function type_user(){
        return $this->belongsTo('App\TypeUser');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gender','type_user_id','email', 'password','phone','sms_notification','email_notification'
    ];
  
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
  
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public static function getUserAllInfoByUserId($user_id){
        $result = DB::table('users')
              ->select('sites.*','projects.name as project_name',
              'projects.alias_name as project_alias','users.name as username')
              ->join('site_users', 'users.id', '=', 'site_users.user_id')
              ->join('sites', 'sites.id', '=', 'site_users.site_id')            
              ->join('clients', 'clients.id', '=', 'sites.client_id')            
              ->join('projects', 'projects.id', '=', 'sites.project_id')            
              ->groupBy('sites.id')
              ->where('users.id',$user_id)
              ->orderBy('sites.name')
              ->get();
  
          return $result;
    }

    public static function getAllSiteEngineer(){
        $result = DB::table('users')
        ->select('users.*','roles.name as rolename')        
        ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                
        ->groupBy('users.id')
        ->where('model_has_roles.role_id',4)        
        ->orderBy('users.name')->get();
        return $result;

    }
    
    public static function getAllStoreManager(){
        $result = DB::table('users')
        ->select('users.*','roles.name as rolename')        
        ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                
        ->groupBy('users.id')
        ->where('model_has_roles.role_id',7)        
        ->orderBy('users.name')->get();
        return $result;

    }

    public static function getManPower($type='',$site_id){
        $result = DB::table('users')
        ->select('users.id','users.name','users.email','type_users.name as user_type','sites.id as site_id','sites.name as site_name')        
        ->join('type_users', 'type_users.id', '=', 'users.type_user_id')
        ->join('site_users','site_users.user_id','=','users.id')
        ->join('sites','sites.id','=','site_users.site_id')
        ->groupBy('users.id')
        ->where('sites.id',$site_id);        

         if($type=='vendor'){
            $result = $result->where('type_users.id',2);        
         }else if($type=='trafiksol'){
            $result = $result->where('type_users.id',3);        
         }
         
        $result = $result->orderBy('users.id','desc');
        return $result;

    }

    public static function getUsersList(){
        return self::pluck('name','id')->sortBy('name');
    }

    public static function getUserByType(){
        return self::pluck('name','id')->sortBy('name');
    }
    
    public static function getClientUser(){
        $result = DB::table('users')
        ->select('users.*','roles.name as rolename')        
        ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                
        ->groupBy('users.id')
        ->where('users.type_user_id',1)        
        ->orderBy('users.name')->get();
        return $result;
    }

    public static function getUserByClient($client_id){
        $result = DB::table('client_users')->select('user_id')->where('client_id', $client_id)->get();
        $data = [];
        foreach($result as $k=>$v){
            $data[] = $v->user_id;
        }
        return $data;
    }

    public static function getAdminUser($user_id){
        
        $result = DB::table('users')
                ->select('users.name','users.email','site_users.site_id')                                                        
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
                 ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                                         
                 ->join('site_users', 'site_users.user_id', '=', 'users.id')    
                 ->where('site_users.user_id',$user_id)                                                                      
                ->where('roles.name','Admin')
                ->orderBy('users.name')
                ->first();
    
        return $result;
          
    }

    public static function getAdminUserBySite($site_id){
        
        $result = DB::table('site_users')
                ->select('users.name','users.email','site_users.site_id')                                                        
                 ->join('users', 'users.id', '=', 'site_users.user_id')      
                 ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
                 ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                                         
                 ->where('site_users.site_id',$site_id)                                                                      
                ->where('roles.name','Admin')
                ->orderBy('users.name')
                ->get();
    
        return $result;
          
    }

    public static function getManPowerAttendance($user_id,$site_id,$date="",$type){
        
        $result = DB::table('manpower_attendances')
        ->select('manpower_attendances.*','users.name','users.email',
        'type_users.name as user_type',
        'sites.id as site_id','sites.name as site_name')        
        ->join('users','users.id','=','manpower_attendances.user_id')
        ->join('type_users', 'type_users.id', '=', 'users.type_user_id')
        ->join('site_users','site_users.user_id','=','manpower_attendances.user_id')
        ->join('sites','sites.id','=','site_users.site_id')        
        ->where('sites.id',$site_id);        

        //  if($type=='vendor'){
        //     $result = $result->where('type_users.id',2);        
        //  }else if($type=='trafiksol'){
        //     $result = $result->where('type_users.id',3);        
        //  }

         if($date==""){
            $date = date('Y-m-d');
         }
         $result = $result->where(DB::raw('DATE(manpower_attendances.created_at)'),$date);        

         if($type!=""){
            $result = $result->where('type_users.name',$type);        
         }
         
                 
        return $result;
          
    }
}
