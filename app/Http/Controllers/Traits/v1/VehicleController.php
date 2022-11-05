<?php
    
namespace App\Http\Controllers\v1;
    
use App\Vehicle;
use App\TypeVehicle;
use App\VehicleDpr;
use App\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Config;  
use App\Classes\UploadFile;



class VehicleController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         //$this->middleware('permission:ticket-list|ticket-edit|ticket-delete', ['only' => ['index','show']]);
         //$this->middleware('permission:ticket-create', ['only' => ['create','store']]);
         //$this->middleware('permission:ticket-edit', ['only' => ['edit','update']]);
         //$this->middleware('permission:ticket-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addVehicleDpr(Request $request)
    {
        try{
			$status = 0;
			$message = "";
            $user  = JWTAuth::user();  
            
            
            if(!isset($user->id)){
                return response()->json(['status'=>$status,'message'=>"user not found",'data'=>json_decode("{}")]);
            }
            
            DB::beginTransaction(); 

            
            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required',                                
                'uom_id' => 'required',    
                'site_id'=> 'required',                   
            ]);
                           
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $request->merge([
                'user_id' => $user->id,                
            ]); 
            
            $site_id = $request->site_id;
            
            $result = Site::validateSite($site_id); 

            
            if (isset($result->id)){
                
                if(isset($request->id)){                    
                    $vehicle = VehicleDpr::find($request->id);
                    if(isset($vehicle->id)){
                        $id = $vehicle->id;
                        if($vehicle->is_submit=='Y'){
                            return response()->json(['status'=>$status,'message'=>"Vehicle is already submitted",'data'=>json_decode("{}")]);
                        }
                        if(isset($request->is_submit) && $request->is_submit == 'Y'){
                            $request->is_submit = 'Y';
                            $request->status = 'submitted';
                        }
                        
                        if(isset($_FILES['file']['name']) && count($_FILES['file']['name'])>0) {
                            for($k=0; $k < count($_FILES['file']['name']); $k++) {
                              $upload_handler = new UploadFile();
                              $path = public_path('uploads/vehicles'); 
                              $data = $upload_handler->multiUpload($k,$path,'vehicles');
                              $res = json_decode($data);
                              if($res->status=='ok'){
                                
                                $vehicle->image = $res->path;
                                $vehicle->file_path = $res->img_path;                                
                              }
                            }
                        }

                        $insertQuery = $vehicle->update($request->all());
                    }else{
                        return response()->json(['status'=>$status,
                        'message'=>"Vehicle does not exist",'data'=>json_decode("{}")]);
                    }
                    
                }else{

                    
                    
                    if(isset($_FILES['file']['name']) && count($_FILES['file']['name'])>0) {
                        for($k=0; $k < count($_FILES['file']['name']); $k++) {
                          $upload_handler = new UploadFile();
                          $path = public_path('uploads/vehicles'); 
                          $data = $upload_handler->multiUpload($k,$path,'vehicles');
                          $res = json_decode($data);
                          if($res->status=='ok'){
                            
                            $request->merge([
                                'image' => $res->path,                
                                'file_path' => $res->img_path,                
                            ]);                                
                          }
                        }
                    }

                    $request->status = 'saved';
                    $insertQuery = VehicleDpr::create($request->all());
                    $id = $insertQuery->id;
                }
                
                  
                DB::commit();
                $data = [];
                            
                $status = 1;
                $message = "Vehicle Dpr submitted successfully";
                return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
            }else{
                return response()->json(['status'=>$status,
                'message'=>"no vehicle/site availabe in database",'data'=>json_decode("{}")]);    
            }
                
        }catch(Exception $e){
			DB::rollback();
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 
    }


    public function list(Request $request){

        try{
			$status = 0;
            $message = "";
            
            $user  = JWTAuth::user();  

            $validator = Validator::make($request->all(), [
                'site_id' => 'required',                 
            ]);
                           
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }

            $data = VehicleDpr::getAllDprVehicleBySite($request->site_id)->get();
            if($data->count()){
                $status = 1;
    			$message = "";
	    		return response()->json(['status'=>$status,'message'=>$message,'data'=>$data]);
            }else{
                return response()->json(['status'=>$status,'message'=>"no record found",'data'=>json_decode("{}")]);
            }
            		            			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 

           
    }

    public function totalVehicleRunningForDayMonth(Request $request){
        try{
            
			$status = 0;
            $message = "";
           
            
            $user  = JWTAuth::user();  

            $validator = Validator::make($request->all(), [
                'site_id' => 'required',                                
            ]);           
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $dataMonth = VehicleDpr::totalVehicleRunningForMonth($request->site_id);
            $dataDay = VehicleDpr::totalVehicleRunningForDay($request->site_id);
            
            return response()->json(['status'=>1,
            'message'=>$message,                                
            'data'=>['month'=>$dataMonth,'day'=>$dataDay]
            ]);
            
                                    			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }   
    }


    public function vehicleList(Request $request){

        try{
			$status = 0;
            $message = "";
            $site_id = $request->site_id;
            if(!isset($request->site_id)){
                return response()->json(['status'=>$status,'message'=>"Site id no sent",'data'=>json_decode("{}")]);
            }
            $data = Vehicle::select('vehicles.*','type_vehicles.name as vehicle_type')
            ->join('type_vehicles','vehicles.type_vehicle_id','=','type_vehicles.id')            
            ->join('site_vehicles','site_vehicles.vehicle_id','=','vehicles.id')
            ->where('site_vehicles.site_id',$site_id)
            ->get();
            if($data->count()){
                $status = 1;
    			$message = "";
	    		return response()->json(['status'=>$status,'message'=>$message,'data'=>$data]);
            }else{
                return response()->json(['status'=>$status,'message'=>"no record found",'data'=>json_decode("{}")]);
            }
            		            			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 

           
    }
    
    
}
