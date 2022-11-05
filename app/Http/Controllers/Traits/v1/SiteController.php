<?php

namespace App\Http\Controllers\v1;

use App\User;
use App\Site;
use App\EquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Config;
use App\Common\Utility;
use Mail;


class SiteController extends Controller
{
    
    public function getAllSiteInfo(Request $request)
    {
        
        $status = 0;
        $message = "";
        
        try {
            $user  = JWTAuth::user();  
            $returnData = Site::getAllSiteInfo();

        } catch (JWTException $e) {
            $message = 'could_not_create_token';
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);            
        }        
        

        return response()->json(['status'=>$status,'message'=>$message,'data'=>$returnData]);
    }     

    public function getSiteInfoBySiteId(Request $request){
        
        $status = 0;
        $message = "";
        
        try {

            $validator = Validator::make($request->all(), [
                'site_id' => 'required',                                
            ]);           
            if($validator->fails()){               
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            } 


            $user  = JWTAuth::user();  
            $returnData = Site::getSiteInfoBySiteId($request->site_id,$user->id);
            if(isset($returnData->id)){
                return response()->json(['status'=>1,'message'=>$message,'data'=>$returnData]);
            }else{
                return response()->json(['status'=>0,'message'=>'no record found','data'=>json_decode("{}")]);
            }
            

        } catch (JWTException $e) {
            $message = 'could_not_create_token';
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);            
        }        
                
    }

    public function getSiteInfoByUser(Request $request){

        $status = 0;
        $message = "";
        
        try {
            $user  = JWTAuth::user();  
            $returnData = Site::getSiteInfoByUser($user->id);
            
            return response()->json(['status'=>$status,'message'=>$message,'data'=>$returnData]);

        } catch (JWTException $e) {
            $message = 'Exception';
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);            
        }                        
    }

    public function equipmentRequest(Request $request){
        $status = 0;
        $message = "";
        
        try {


            $validator = Validator::make($request->all(), [
                'site_id' => 'required',                
                'equipment_id' => 'required',
                'description' => 'required',
                'finish_date' => 'required',
                'quantity' => 'required'                                 
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,                 
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $user  = JWTAuth::user();  
            
            $request->merge([
                'requested_by' => $user->id                
            ]); 

            $insertQuery = EquipmentRequest::create($request->all());
            
            return response()->json(['status'=>$status,'message'=>$message,'data'=>$insertQuery]);

        } catch (JWTException $e) {
            $message = 'Exception';
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);            
        }
    }

    public function getAdminUserBySite(Request $request){
        $status = 0;
        $message = "";
        
        try {

            $validator = Validator::make($request->all(), [
                'site_id' => 'required',                                                 
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,                 
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
                
            }  

            $user  = JWTAuth::user();  
            $returnData = User::getAdminUserBySite($request->site_id);
            $status = 1;
            return response()->json(['status'=>$status,'message'=>$message,'data'=>$returnData]);

        } catch (JWTException $e) {
            $message = 'Exception';
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);            
        }   
    }
}