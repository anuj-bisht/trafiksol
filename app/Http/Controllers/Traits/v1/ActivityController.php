<?php
    
namespace App\Http\Controllers\v1;
    
use App\Activity;
use App\ActivityCategory;
use App\ActivityDpr;
use App\ActivityDprImage;
use App\Ticket;
use App\ActivityDprTomorrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Config;  
use App\Classes\UploadFile;



class ActivityController extends Controller
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
    public function addTodaysActivityDpr(Request $request)
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
                'activity_id' => 'required',                
                'uom_id' => 'required',
                'stretch_one' => 'required',
                'description' => 'required',
                'quantity' => 'required',
                'rfi_no' => 'required',    
                'site_id'=> 'required',                   
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,                 
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $request->merge([
                'user_id' => $user->id,                
            ]); 
            
            $activity_id = $request->activity_id;
            
            $result = Activity::validateActivity($activity_id); 

            
            if (isset($result->id)){
                
                if(isset($request->id)){                    
                    $activity = ActivityDpr::find($request->id);
                    if(isset($activity->id)){
                        $id = $activity->id;
                        if($activity->is_submit=='Y'){
                            return response()->json(['status'=>$status,'message'=>"Activity is already submitted",'data'=>json_decode("{}")]);
                        }
                        if(isset($request->is_submit) && $request->is_submit == 'Y'){
                            $request->is_submit = 'Y';
                            $request->status = 'submitted';
                        }
                        $insertQuery = $activity->update($request->all());
                    }else{
                        return response()->json(['status'=>$status,
                        'message'=>"Activity does not exist",'data'=>json_decode("{}")]);
                    }
                    
                }else{
                    $request->status = 'saved';
                    $insertQuery = ActivityDpr::create($request->all());
                    $id = $insertQuery->id;
                }
                

                if(isset($_FILES['file']['name']) && count($_FILES['file']['name'])>0) {
                    for($k=0; $k < count($_FILES['file']['name']); $k++) {
                      $upload_handler = new UploadFile();
                      $path = public_path('uploads/dprs'); 
                      $data = $upload_handler->multiUpload($k,$path,'dprs');
                      $res = json_decode($data);
                      if($res->status=='ok'){
                        $newUserImg = new ActivityDprImage();
                        $newUserImg->activity_dpr_id = $id;
                        $newUserImg->type = $res->type;
                        $newUserImg->image = $res->path;
                        $newUserImg->file_path = $res->img_path;
                        $newUserImg->save();
                      }
                    }
                }

                  
                DB::commit();
                $data = [];
                            
                $status = 1;
                $message = "Dpr submitted successfully";
                
                $this->sendMessage('dpr-submit',$insertQuery->toArray(),$request->site_id);    

                return response()->json(['status'=>$status,'message'=>$message,'data'=>$insertQuery]);
            }else{
                return response()->json(['status'=>$status,
                'message'=>"no activity availabe in database",'data'=>json_decode("{}")]);    
            }
                
        }catch(Exception $e){
			DB::rollback();
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addTomorrowsActivityDpr(Request $request)
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
                'activity_id' => 'required',                
                'uom_id' => 'required',
                'stretch_one' => 'required',
                'description' => 'required',
                'quantity' => 'required',     
                'site_id'=> 'required',                   
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,                 
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  
            $Date = date('Y-m-d'); 
            $request->merge([
                'user_id' => $user->id,  
                'created_for'=>date('Y-m-d', strtotime($Date. ' + 1 days')),              
            ]); 
            
            $activity_id = $request->activity_id;
            
            $result = Activity::validateActivity($activity_id); 

            
            if (isset($result->id)){

                
                if(isset($request->id)){                    
                    $activity = ActivityDprTomorrow::find($request->id);
                    if(isset($activity->id)){
                        $id = $activity->id;                        
                        $insertQuery = $activity->update($request->all());
                    }else{
                        return response()->json(['status'=>$status,
                        'message'=>"Activity does not exist",'data'=>json_decode("{}")]);
                    }
                    
                }else{                    
                    $insertQuery = ActivityDprTomorrow::create($request->all());
                    $id = $insertQuery->id;
                }


                // if(isset($_FILES['file']['name']) && count($_FILES['file']['name'])>0) {
                //     for($k=0; $k < count($_FILES['file']['name']); $k++) {
                //       $upload_handler = new UploadFile();
                //       $path = public_path('uploads/dprs'); 
                //       $data = $upload_handler->multiUpload($k,$path,'dprs');
                //       $res = json_decode($data);
                //       if($res->status=='ok'){
                //         $newUserImg = new ActivityDprImage();
                //         $newUserImg->activity_dpr_id = $insertQuery->id;
                //         $newUserImg->type = $res->type;
                //         $newUserImg->image = $res->path;
                //         $newUserImg->file_path = $res->img_path;
                //         $newUserImg->save();
                //       }
                //     }
                // }

                  
                DB::commit();
                $data = [];
                            
                $status = 1;
                $message = "Tomorrows DPR is submitted successfully";
                return response()->json(['status'=>$status,'message'=>$message,'data'=>$insertQuery]);
            }else{
                return response()->json(['status'=>$status,'message'=>"no activity availabe in database",'data'=>json_decode("{}")]);    
            }
                
        }catch(Exception $e){
			DB::rollback();
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 
    }

    
    public function siteActivityList(Request $request){

        try{
			$status = 0;
            $message = "";

            $validator = Validator::make($request->all(), [             
                'site_id'=> 'required',                   
            ]);            
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);                
            }
            $user  = JWTAuth::user();  


            $data = Activity::siteActivityList($user->id,$request->site_id);
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

      
    public function list(Request $request){

        try{
			$status = 0;
            $message = "";

            $validator = Validator::make($request->all(), [             
                'site_id'=> 'required',                   
            ]);            
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);                
            }
            $user  = JWTAuth::user();  


            $data = ActivityDpr::getAllDprActivityByUser($user->id,$request->site_id);
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

    public function listTomorrow(Request $request){

        try{
			$status = 0;
            $message = "";
            
            $user  = JWTAuth::user();  
            $validator = Validator::make($request->all(), [             
                'site_id'=> 'required',                   
            ]);            
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);                
            }

            $data = ActivityDprTomorrow::getAllDprActivityByUser($user->id,$request->site_id);
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


    public function getActivityDPRById(Request $request){
        try{
			$status = 0;
            $message = "";
           
            
            $user  = JWTAuth::user();  

            $validator = Validator::make($request->all(), [
                'activity_dpr_id' => 'required',                                
            ]);           
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $data = ActivityDpr::getDprActivityById($request->activity_dpr_id);
            if(isset($data->id)){
                return response()->json(['status'=>1,
                'message'=>$message,                                
                'data'=>$data
                ]);
            }else{
                $message = "No record found";
                return response()->json(['status'=>0,
                'message'=>$message,                                
                'data'=>json_decode("{}")
                ]);
            }
                                    			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }   
    }


    public function getActivityTomorrowDPRById(Request $request){
        try{
			$status = 0;
            $message = "";
           
            
            $user  = JWTAuth::user();  

            $validator = Validator::make($request->all(), [
                'activity_dpr_id' => 'required',                                
            ]);           
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $data = ActivityDprTomorrow::getDprActivityById($request->activity_dpr_id);
            if(isset($data->id)){
                return response()->json(['status'=>1,
                'message'=>$message,                                
                'data'=>$data
                ]);
            }else{
                $message = "No record found";
                return response()->json(['status'=>0,
                'message'=>$message,                                
                'data'=>json_decode("{}")
                ]);
            }
                                    			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }   
    }

    public function getIssueTypeList(Request $request){

        try{
			$status = 0;
            $message = "";
           
            
            $user  = JWTAuth::user();  

            $data = TicketIssueType::getALlIssueType();
            //$data = $data->paginate(20);

            return response()->json(['status'=>1,
                'message'=>$message,                                
                'data'=>$data
            ]);
            			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }            
    }
    
    public function getTotalActivity(Request $request){
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

            $data = ActivityDpr::getTotalActivity($request->site_id);
            //$data = $data->paginate(20);

            return response()->json(['status'=>1,
                'message'=>$message,                                
                'data'=>$data
            ]);
            			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }    
    }
    
    public function getActivityCategory(Request $request){
        try{
			$status = 0;
            $message = "";
             
            $validator = Validator::make($request->all(), [
                'site_id' => 'required',                                
            ]);           
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $data = ActivityCategory::getSiteActivityList($request->site_id);
            //$data = $data->paginate(20);

            return response()->json(['status'=>1,
                'message'=>$message,                                
                'data'=>$data
            ]);
            			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }    
    }
    
}
