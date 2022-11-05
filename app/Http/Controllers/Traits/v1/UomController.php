<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use Config;
use App\Uom;

class UomController extends Controller
{
    /**
     * Show the form for get user lsit.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUomList(Request $request){
        try{
                        
            $status = 0;
            $message = "";                      
            $result = Uom::getUOMList('result');  
            //print_r($cateoryList); die;                      
            if($result->count() >0){
                $status = 1;
                return response()->json(['status'=>$status,'message'=>$message,'data'=>$result]); 
            }else{
                return response()->json(['status'=>$status,'message'=>"unable to get Uom",'data'=>[]]); 
            }                        
        }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>'Uom exception','data'=>json_decode('{}')]); 
        }               
    }
}
