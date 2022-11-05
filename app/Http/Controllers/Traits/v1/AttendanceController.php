<?php
//amary@321! amary
namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use App\Amenities;
use App\AmenityGallery;
//use App\UserRating;
use Config;

class AmenitiesController extends Controller
{
    /**
   * ge Home pate method
   * @return success or error
   * 
   * */
  public function getHomePage(Request $request){
    
    try{
      $status = 0;
      $message = "";    
      
      $validator = Validator::make($request->all(), [
        'type' => 'required|string'          
      ]);
      
      if($validator->fails()){
        return response()->json(["status"=>$status,"message"=>"Please provide type","data"=>json_decode("{}")]);
      }

      $result = Amenities::where('type',$request->type)->get();
      if($result->count() > 0){
          
          return response()->json(['status'=>1,'message'=>'','data'=>$result]);                    
      }else{
         
          return response()->json(['status'=>$status,'message'=>'NO gallery Found','data'=>json_decode("{}")]);                    
      }   
    }catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Exception Error','data'=>json_decode("{}")]);                    
    }
            
  }

   /**
   * ge Home pate method
   * @return success or error
   * 
   * */
  public function getAmenityGallery(Request $request){
    
    try{
      $status = 0;
      $message = "";
      
      $validator = Validator::make($request->all(), [
        'amenity_id' => 'required|numeric'          
      ]);
      
      //$validator->errors()
      if($validator->fails()){
        return response()->json(["status"=>$status,"message"=>"Please provide amenity id","data"=>json_decode("{}")]);
      }

      $amenity_id = $request->amenity_id;
      $result = AmenityGallery::with(['Amenities'])
      ->where('amenity_id',$amenity_id)->get();
      if($result->count() > 0){
          return response()->json(['status'=>1,'message'=>'','data'=>$result]);                    
      }else{
         
          return response()->json(['status'=>$status,'message'=>'NO gallery Found','data'=>json_decode("{}")]);                    
      }   
    }catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Exception Error','data'=>json_decode("{}")]);                    
    }
            
  }
}
