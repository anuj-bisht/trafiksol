<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Site extends Model
{

    public function country(){
        return $this->belongsTo('App\Country');
    }

    public function state(){
        return $this->belongsTo('App\State');
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','project_id','client_id','alias_name','location','address1','address2',
        'city','sla_id','stretch_from','stretch_to','state_id','zip','country_id',
    ];

    public function client(){
      return $this->belongsTo('App\Client');
    }
    
    public function project(){
      return $this->belongsTo('App\Project');
    }    
    
    public static function getSiteList(){
		return self::where('id','<>',0)->pluck('name','id')->sortBy('id');
    }

    public static function getSiteListDD(){
		return self::select('id','name')->where('id','<>',0)->get();
    }
    
    public static function getAllSiteData(){
        return self::select('sites.*','projects.name as project_name','clients.name as client_name',
        'clients.image as client_image'
        )
        ->join('projects', 'projects.id', '=', 'sites.project_id')
        ->join('clients', 'clients.id', '=', 'sites.client_id');
	}
	
    //DB::raw('group_concat(phases.name) as phase_name')
    public static function getVehicleAssigned($site_id){
      $result = DB::table('sites')
            ->select('site_vehicles.quantity','sites.name as site_name', 
            'projects.name as project_name','vehicles.name as vehicle_name',
            'vehicles.vehicle_number','type_vehicles.name as vehicle_type_name','site_vehicles.id as site_vehicle_id')
            ->join('projects', 'projects.id', '=', 'sites.project_id')
            ->join('site_vehicles', 'site_vehicles.site_id', '=', 'sites.id')            
            ->join('vehicles', 'vehicles.id', '=', 'site_vehicles.vehicle_id')            
            ->join('type_vehicles', 'type_vehicles.id', '=', 'vehicles.type_vehicle_id')            
            ->where('sites.id',$site_id)
            ->orderBy('site_vehicles.id')
            ->get();

        return $result;
    }

    public static function getActivityAssigned($site_id){
      $result = DB::table('sites')
            ->select('site_activities.quantity','sites.name as site_name', 
            'projects.name as project_name','activities.name as activity_name',
            'activity_categories.name as activity_category_name','site_activities.id as site_activity_id')
            ->join('projects', 'projects.id', '=', 'sites.project_id')
            ->join('site_activities', 'site_activities.site_id',
             '=', 'sites.id')            
            ->join('activities', 'activities.id', '=', 'site_activities.activity_id')            
            ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id')            
            ->where('sites.id',$site_id)
            ->orderBy('site_activities.id')
            ->get();

        return $result;
    }

    public static function getEquipmentAssigned($site_id){
        $result = DB::table('sites')
            ->select('brands.name as brand_name','models.model as model_name',
            'models.make','models.build','site_equipments.chainage','site_equipments.location as equipment_location','site_equipments.id as site_equipment_id',
            'sites.name as site_name', 'projects.name as project_name',
            'equipments.title as equipment_name','equipments.id as equipment_id',
            'equipment_slas.name as sla_name')
            ->join('projects', 'projects.id', '=', 'sites.project_id')
            ->join('site_equipments', 'site_equipments.site_id', '=', 'sites.id')  
            ->leftJoin('equipment_slas', 'equipment_slas.id', '=', 'site_equipments.sla_id')                        
            ->join('equipments', 'equipments.id', '=', 'site_equipments.equipment_id')            
            ->join('brands', 'brands.id', '=', 'equipments.brand_id')            
            ->join('models', 'models.id', '=', 'equipments.model_id')            
            ->where('sites.id',$site_id)
            ->orderBy('site_equipments.id')
            ->get();

        return $result;
    }

    public static function getAllSiteInfo(){
      $result = DB::table('sites')
            ->select('sites.*','projects.name as project_name',
            'projects.alias_name as project_alias',
            'activities.name as activity_name',
            'equipments.title as equipment_name','equipments.chainage as equipment_chainage'
            )            
            ->leftJoin('site_users', 'sites.id', '=', 'site_users.user_id')            
            ->leftJoin('clients', 'clients.id', '=', 'sites.client_id')            
            ->leftJoin('site_activities', 'site_activities.site_id', '=', 'sites.id')            
            ->leftJoin('activities', 'site_activities.activity_id', '=', 'activities.id')            
            
            ->leftJoin('site_equipments', 'site_equipments.site_id', '=', 'sites.id')            
            ->leftJoin('equipments', 'site_equipments.equipment_id', '=', 'equipments.id')            

            ->leftJoin('site_vehicles', 'site_vehicles.site_id', '=', 'sites.id')            
            ->leftJoin('vehicles', 'site_vehicles.vehicle_id', '=', 'vehicles.id')            


            ->leftJoin('projects', 'projects.id', '=', 'sites.project_id')            
            //->groupBy('sites.id')
            ->orderBy('sites.name')
            ->get();

        return $result;
    }

    public static function getSiteInfoByUser($user_id){
      $result = DB::table('sites')
            ->select('sites.*',DB::raw("CONCAT(sites.address1,' ',sites.address2,',',sites.city,',',sites.zip,',',states.name,',',countries.name) as site_address"),
            'users.name as username','users.email as useremail')            
            ->join('site_users', 'sites.id', '=', 'site_users.site_id')                                            
            ->join('users', 'users.id', '=', 'site_users.user_id')                                            
            ->join('states', 'states.id', '=', 'sites.state_id')                                            
            ->join('countries', 'countries.id', '=', 'sites.country_id')                                            
            ->where('users.id',$user_id)
            ->orderBy('sites.name')
            ->get();

        return $result;
    }
    
    public static function getSiteInfoBySiteId($site_id,$user_id){
		  $result = DB::table('sites')
            ->select('sites.*','users.name as username','clients.name as client_name',
            'projects.name as project_name','clients.image as client_logo')            
            ->leftJoin('site_users', 'sites.id', '=', 'site_users.site_id')      
            ->join('clients', 'sites.client_id', '=', 'clients.id')                                                                                  
            ->join('projects', 'sites.project_id', '=', 'projects.id')                                                                                  
            ->leftJoin('users', 'users.id', '=', 'site_users.user_id')                                                        
            ->where('sites.id',$site_id)
            ->where('site_users.user_id',$user_id)
            ->orderBy('sites.name')
            ->first();

        return $result;
    }
    
    public static function validateSite($site_id){
		  return self::where('id',$site_id)->first();
    }
    

    public static function totalAdvanceForMonth($site_id){
        $month = date('m');
        $result = DB::table('site_advances')
            ->select(DB::raw('SUM(amount) as total_for_month'))                                    
            ->where(DB::raw('MONTH(created_at)'),$month)
            ->where('site_id',$site_id)            
            ->first();

        return $result;
    }

    public static function getAllSiteInfoBySite($site_id){
        $result = DB::table('sites')
              ->select('sites.*','users.name as username','clients.name as client_name',
              'projects.name as project_name','clients.image as client_image'
              )            
              ->join('clients', 'clients.id', '=', 'sites.client_id')   
              ->join('projects', 'projects.id', '=', 'sites.project_id')   
              ->join('site_users', 'sites.id', '=', 'site_users.site_id')                                            
              ->join('users', 'users.id', '=', 'site_users.user_id')                                            
              ->where('sites.id',$site_id)
              ->orderBy('sites.name')
              ->first();
  
          return $result;
    }

    public static function getSiteUsers($site_id){
        
        $result = DB::table('site_users')
                ->select('users.*',DB::raw('CONCAT(roles.name) as role_name'))            
                ->join('users', 'site_users.user_id', '=', 'users.id')                                            
                ->join('sites', 'sites.id', '=', 'site_users.site_id')      
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
                 ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                                         
                ->where('site_users.site_id',$site_id)
                ->orderBy('users.name')
                ->get();
    
        return $result;
          
    }

    
}
