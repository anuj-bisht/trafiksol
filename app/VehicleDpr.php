<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use DB;

class VehicleDpr extends Model
{
  protected $table = 'vehicle_dprs';
  
  public function vehicle(){
      return $this->belongsTo('App\Vehicle');
  }

  /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'vehicle_id','site_id','user_id','distance','description',
      'fuel_filled','distance','uom_id','fuel_type','is_submit',
      'status','amount','rate','image','file_path'
    ];
	
	public static function getAllDprVehicleByUser($user_id){
		 $result = DB::table('vehicle_dprs')
            ->select('vehicle_dprs.*','users.name as username',
            'sites.name as site_name','vehicles.name as vehicle_name','vehicles.vehicle_number','uoms.name as uom_name')
            ->join('users', 'users.id', '=', 'vehicle_dprs.user_id')
            ->join('sites', 'sites.id', '=', 'vehicle_dprs.site_id')                
            ->join('uoms', 'uoms.id', '=', 'vehicle_dprs.uom_id')                       
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_dprs.vehicle_id')    
            ->groupBy('vehicle_dprs.id')
            ->orderBy('vehicle_dprs.id','Desc')            
            ->get();

        return $result;
	}   
	
	
	
	public static function getAllDprVehicle($params=[]){
		 $result = DB::table('vehicle_dprs')
            ->select('vehicle_dprs.*','vehicles.name as vehicle_name','vehicles.vehicle_number','type_vehicles.name as vechicle_type',
            'users.name as username','sites.name as site_name','uoms.name as uom_name')        
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_dprs.vehicle_id')
            ->join('type_vehicles', 'type_vehicles.id', '=', 'vehicles.type_vehicle_id')            
            ->join('uoms', 'uoms.id', '=', 'vehicle_dprs.uom_id')
            ->join('users', 'users.id', '=', 'vehicle_dprs.user_id')
            ->join('sites', 'sites.id', '=', 'vehicle_dprs.site_id');

        return $result;
	}    
	
	
	public static function getDprVehicleById($id){
		 $result = DB::table('vehicle_dprs')
            ->select('vehicle_dprs.*','vehicles.name as vehicle_name','vehicles.vehicle_number','type_vehicles.name as vechicle_type',
            'users.name as username','sites.name as site_name','uoms.name as uom_name')           
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_dprs.vehicle_id')
            ->join('type_vehicles', 'type_vehicles.id', '=', 'vehicles.type_vehicle_id')            
            ->join('users', 'users.id', '=', 'vehicle_dprs.user_id')
            ->join('uoms', 'uoms.id', '=', 'vehicle_dprs.uom_id')
            ->join('sites', 'sites.id', '=', 'vehicle_dprs.site_id')                     
            ->where('vehicle_dprs.id',$id)               
            ->orderBy('vehicle_dprs.id','Desc')->first();

        return $result;
	}   
  
  public static function totalVehicleRunningForMonth($site_id){
      $month = date('m');
      $result = DB::table('vehicle_dprs')
          ->select(DB::raw('SUM(distance) as total_for_month'),DB::raw('SUM(fuel_filled) as total_fuel_for_month'))                                    
          ->where(DB::raw('MONTH(created_at)'),$month)
          ->where('site_id',$site_id)
          ->where('status','approved')
          ->get();

      return $result;
  }

  public static function totalVehicleRunningForDay($site_id){
      $day = date('d');
      $result = DB::table('vehicle_dprs')
          ->select(DB::raw('SUM(distance) as total_for_day'),DB::raw('SUM(fuel_filled) as total_fuel_for_day'))                                    
          ->where(DB::raw('DAY(created_at)'),$day)
          ->where('site_id',$site_id)
          ->where('status','approved')
          ->get();

      return $result;
  }

  public static function dieselForMonth($site_id){
        $month = date('m');
        $result = DB::table('vehicle_dprs')
            ->select(DB::raw('SUM(fuel_type) as diesel_for_month'))                                    
            ->where(DB::raw('MONTH(created_at)'),$month)
            ->where('site_id',$site_id)
            ->where('status','approved')
            ->where('fuel_type','diesel')
            ->get();

        return $result;
  }

  public static function dieselForDay($site_id){
        $day = date('d');
        $result = DB::table('vehicle_dprs')
            ->select(DB::raw('SUM(fuel_type) as diesel_for_day'))                                    
            ->where(DB::raw('DAY(created_at)'),$day)
            ->where('site_id',$site_id)            
            ->where('fuel_type','diesel')
            ->get();

        return $result;
  }

  public static function getAllDprVehicleBySite($site_id){
        $result = DB::table('vehicle_dprs')
        ->select('vehicle_dprs.*','vehicles.name as vehicle_name','vehicles.vehicle_number','type_vehicles.name as vechicle_type',
        'users.name as username','sites.id as site_id','sites.name as site_name','uoms.name as uom_name')        
        ->join('vehicles', 'vehicles.id', '=', 'vehicle_dprs.vehicle_id')
        ->join('type_vehicles', 'type_vehicles.id', '=', 'vehicles.type_vehicle_id')            
        ->join('uoms', 'uoms.id', '=', 'vehicle_dprs.uom_id')
        ->join('users', 'users.id', '=', 'vehicle_dprs.user_id')
        ->join('sites', 'sites.id', '=', 'vehicle_dprs.site_id')       
        ->where('vehicle_dprs.site_id',$site_id)                 
        ->where(DB::raw('DATE(vehicle_dprs.created_at)'),date('Y-m-d'))                 
        ->groupBy('vehicle_dprs.id');

    return $result;
  }    
    
}
