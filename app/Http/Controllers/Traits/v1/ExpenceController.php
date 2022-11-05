<?php
    
namespace App\Http\Controllers\v1;
    
use App\ExpenceDpr;
use App\CategoryExpence;
use App\ExpenceDprImage;
use App\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Config;  
use App\Classes\UploadFile;



class ExpenceController extends Controller
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
    public function addExpenceDpr(Request $request)
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
                'description' => 'required',
                'category_expence_id'=>'required',
                'quantity' => 'required',                
                'site_id'=> 'required',                   
                'rate'=>'required',
                'amount'=>'required'
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,                 
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
                    $expence = ExpenceDpr::find($request->id);
                    if(isset($expence->id)){
                        $id = $expence->id;
                        if($expence->is_submit=='Y'){
                            return response()->json(['status'=>$status,'message'=>"Expence is already submitted",'data'=>json_decode("{}")]);
                        }
                        if(isset($request->is_submit) && $request->is_submit == 'Y'){
                            $request->is_submit = 'Y';
                            $request->status = 'submitted';
                        }
                        $insertQuery = $expence->update($request->all());
                    }else{
                        return response()->json(['status'=>$status,
                        'message'=>"Activity does not exist",'data'=>json_decode("{}")]);
                    }
                    
                }else{
                    $request->status = 'saved';
                    $insertQuery = ExpenceDpr::create($request->all());
                    $id = $insertQuery->id;
                }
                

                if(isset($_FILES['file']['name']) && count($_FILES['file']['name'])>0) {
                    for($k=0; $k < count($_FILES['file']['name']); $k++) {
                      $upload_handler = new UploadFile();
                      $path = public_path('uploads/expences'); 
                      $data = $upload_handler->multiUpload($k,$path,'expences');

                      $res = json_decode($data);
                      if($res->status=='ok'){
                        $newUserImg = new ExpenceDprImage();
                        $newUserImg->expence_dpr_id = $id;
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
                $message = "Expence Dpr submitted successfully";
                $ntype = (isset($request->id)) ? 'dpr-submit': 'dpr-submit';
                $this->sendMessage($ntype,$insertQuery->toArray(),$request->site_id);    

                return response()->json(['status'=>$status,'message'=>$message,'data'=>$insertQuery]);
            }else{
                return response()->json(['status'=>$status,
                'message'=>"no site details availabe in database",'data'=>json_decode("{}")]);    
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
                'site_id'=> 'required',                                   
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,                 
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  


            $data = ExpenceDpr::getTodayDprExpenceBySite($request->site_id)->get();
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

    public function getExpenceDPRById(Request $request){
        try{
			$status = 0;
            $message = "";
           
            
            $user  = JWTAuth::user();  

            $validator = Validator::make($request->all(), [
                'expence_dpr_id' => 'required',                                
            ]);           
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $data = ExpenceDpr::getDprExpenceById($request->expence_dpr_id);
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


    public function totalExpenceForDayMonth(Request $request){
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

            $dataMonth = ExpenceDpr::totalExpenceForMonth($request->site_id);
            $dataDay = ExpenceDpr::totalExpenceForDay($request->site_id);
            $advance = ExpenceDpr::advanceTaken($request->site_id);
            
            return response()->json(['status'=>1,
            'message'=>$message,                                
            'data'=>['month'=>$dataMonth,'day'=>$dataDay,'advance'=>$advance]
            ]);
            
                                    			
        }catch(Exception $e){
			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }   
    }
    
    public function getExpenceCategory(Request $request){
        try{
			$status = 0;
            $message = "";
             

            $data = CategoryExpence::getExpenceCategoryList();
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
