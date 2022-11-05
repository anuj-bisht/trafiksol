<?php

namespace App\Http\Controllers\v1;

use App\Store;
use App\Brand;
use App\Equipment;
use App\Site;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class StoreController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addStore(Request $request)
    {
        try{
			$status = 0;
			$message = "";
            $user  = JWTAuth::user();


            if(!isset($user->id)){
                return response()->json(['status'=>$status,'message'=>"user not found",'data'=>json_decode("{}")]);
            }

            $validator = Validator::make($request->all(), [
                'site_id' => 'required',
                'item_name'=>'required',
                'item_code'=>'required',
                'docket_no'=>'required',
                'equipment_id'=>'required',
                'quantity' => 'required',
                'model_id'=> 'required',
                'brand_id'=>'required',                
            ]);

                // 'type'=>'required'

            ////open,close,fixed,reopen,my_ticket,answered,
            //required|email|unique:users,email
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);

            }


            $site = Site::validateSite($request->site_id);
            if(!isset($site->id)){
				return response()->json(['status'=>$status,
                'message'=>"no site availabe in database with given site id",'data'=>json_decode("{}")]);
			}
            $equipment = Equipment::validateEquipment($request->equipment_id);
            if(!isset($equipment->id)){
				return response()->json(['status'=>$status,
                'message'=>"no equipment availabe in database with given equipment id",'data'=>json_decode("{}")]);
			}
            $brand = Brand::validateBrand($request->brand_id);
            if(!isset($brand->id)){
				return response()->json(['status'=>$status,
                'message'=>"no brand availabe in database with given brand id",'data'=>json_decode("{}")]);
			}
            $model = Models::validateModel($request->model_id);
            if(!isset($model->id)){
				return response()->json(['status'=>$status,
                'message'=>"no model availabe in database with given model id",'data'=>json_decode("{}")]);
            }
            
            
            $store_type = isset($request->store_type) ? $request->store_type : 'Store';   
            
			$store = new Store();
            $store->brand_id = $request->brand_id;
            $store->equipment_id = $request->equipment_id;
            $store->model_id = $request->model_id;
            $store->site_id = $request->site_id;
            $store->quantity = $request->quantity;
            $store->item_name = $request->item_name;
            $store->item_code = $request->item_code;
            $store->docket_no = $request->docket_no;
            $store->type = "1";
            $store->store_type = $store_type;

            if($store->save()){
				return response()->json(['status'=>1,'message'=>'Record added successfully','data'=>json_decode("{}")]);
			}else{
				return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);
			}

        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function list(Request $request){
         try{
			$status = 0;
            $message = "";

            $user  = JWTAuth::user();

            $validator = Validator::make($request->all(), [
                'site_id'=> 'required'
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,
            if($validator->fails()){

                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set11','data'=>json_decode("{}")]);

            }

            $store_type = isset($request->store_type) ? $request->store_type : "Store";
          
           // $data = Store::getStoreBySite($request->site_id,$request->type,$store_type);
             $data=DB::table('stores')->where('site_id', $request->site_id)->orWhere('type',$store_type)->get();

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
