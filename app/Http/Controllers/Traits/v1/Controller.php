<?php

namespace App\Http\Controllers\v1;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Traits\SendMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\NotifyMe;
use App\EventRating;
use Config;
use Mail;
use App\ActivityDpr;
use App\ActivityDprImage;
use App\ActivityDprTomorrow;
use App\ExpenceDprImage;
use App\ExpenceDpr;
use App\VehicleDpr;
use App\Brand;
use App\Models;
use App\Equipment;
use App\Notification;
use App\Site;
use App\Ticket;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SendMail;

    public $paging = 10;
    /**
   * 
   * Notify mail sent */
  public function sendNotificationMail($userData){
    
    //email sent code for event creator
    //$userData = User::where('email',$email)->first();
    $app = app();
    $data = $app->make('stdClass');
    
    $data->type = $userData->type;//$userData->type;
    $data->city_id = $userData->city_id;
    $data->state_id = $userData->state_id;
    $data->country_id = $userData->country_id;
    $data->country_name =  $userData->Country->name; 
    $data->state_name =  $userData->State->name; 
    $data->city_name =  $userData->City->name; 
    $notifyObj = new NotifyMe();    
    $notifyList = $notifyObj->getNotifiedDataByType($data);
    
    if($notifyList->count() > 0){
        Log::info(['add '.$data->type.' notify data found',$notifyList]);
        $mailArr = [];
        $ids = [];
        foreach($notifyList as $k=>$v){
            if(!in_array($v->mail,$mailArr)){                            
                array_push($mailArr,$this->encdesc($v->email,'decrypt'));
                array_push($ids,$v->id);
                //$ids[] = $v->id;
            }                                                   
        }                              
        $maildata['email'] = $mailArr;
        $maildata['name'] = $userData->name;
        $maildata['city_name'] = $data->city_name;
        $maildata['type'] = ucfirst($data->type);
        $maildata['subject'] = ucfirst($data->type). ' Add Notification From '.config('app.site_name');
        $maildata['supportEmail'] = config('mail.supportEmail');
        $maildata['website'] = config('app.site_url');  
        $maildata['site_name'] = config('app.site_name');  
      
        if($this->SendMail($maildata,'notify_while_add')){
            NotifyMe::whereIn('id', $ids)->update(['notified' => 1]);
            Log::info(['add user notify mail sent']);
            return true;
        } 
    }
    return true;
    
  }

  public function encdesc($stringVal,$type='encrypt'){
      
    $stringVal = str_replace("__","/",$stringVal);  
    if($type=='encrypt'){
        return openssl_encrypt($stringVal,"AES-128-ECB",'Xz!Y2zRR4567!#$!');
    }else{
        return openssl_decrypt($stringVal,"AES-128-ECB",'Xz!Y2zRR4567!#$!');
    }        
  }

  public function verifyChecksum($request){    
    return true;
    $checksum = "";  
    $json = json_encode($request->all());
    $requestJson = md5($json); 
    $checksum = $request->header("checksum");
    
    //echo $requestJson.'  '.$checksum; die;
    if($requestJson != $checksum){
        return false;
    }
    return true;
  }

  public function string_replace($repaceFrom,$replaceTo,$string){
    return str_replace($repaceFrom,$replaceTo,$string);
  }

  public function deleteDpr(Request $request){

    $user  = JWTAuth::user();  
            
            
    if(!isset($user->id)){
        return response()->json(['status'=>$status,'message'=>"user not found",'data'=>json_decode("{}")]);
    } 

    $result = ['today'=>false,'expence'=>false,'tomorrow'=>false,'vehicle'=>false];
    //echo '<pre>'; print_r($request->all()); die;

    if(isset($request->today) && count($request->today)){
      $activity = [];
      foreach($request->today as $k=>$v){
        $activity[] = $v['id'];
      }
      //print_r($activity); die;
      if(ActivityDpr::where('user_id',$user->id)->whereIn('id',$activity)->delete()){
        ActivityDprImage::whereIn('activity_dpr_id',$activity)->delete();
        $result['today'] = true;
      }
    }
    
    if(isset($request->tomorrow) && count($request->tomorrow)){
      $tomorrow = [];
      foreach($request->tomorrow as $k=>$v){
        $tomorrow[] = $v['id'];
      }
      if(ActivityDprTomorrow::where('user_id',$user->id)->whereIn('id',$tomorrow)->delete()){
        $result['tomorrow'] = true;
      }
    }

    if(isset($request->expence) && count($request->expence)){
      $expence = [];
      foreach($request->expence as $k=>$v){
        $expence[] = $v['id'];
      }
      if(ExpenceDpr::where('user_id',$user->id)->whereIn('id',$expence)->delete()){
        ExpenceDprImage::whereIn('activity_dpr_id',$activity)->delete();
        $result['expence'] = true;
      }
    }

    if(isset($request->vehicle) && count($request->vehicle)){
      $vehicle = [];
      foreach($request->vehicle as $k=>$v){
        $vehicle[] = $v['id'];
      }
      if(VehicleDpr::where('user_id',$user->id)->whereIn('id',$vehicle)->delete()){        
        $result['vehicle'] = true;
      }
    }

    return response()->json(['status'=>1,'message'=>"",'data'=>$result]);

  }

  

  public function dprSubmitAll(Request $request){

    $user  = JWTAuth::user();  
            
            
    if(!isset($user->id)){
        return response()->json(['status'=>$status,'message'=>"user not found",'data'=>json_decode("{}")]);
    } 


    $validator = Validator::make($request->all(), [          
        'site_id'=> 'required',                   
    ]);
    ////open,close,fixed,reopen,my_ticket,answered,                 
    if($validator->fails()){
        //Log::debug(['add event validation failed',$request->all()]);
        return response()->json(['status'=>$status,'message'=>'Please provide site id','data'=>json_decode("{}")]);
        
    }  
    
    $result = ['activity'=>false,'expense'=>false,'vehicle'=>false];
    //echo '<pre>'; print_r($request->all()); die;

    if(isset($request->activity) && count($request->activity)){
      // $activity = [];
      // foreach($request->activity as $k=>$v){
      //   $activity[] = $v['id'];
      // }
      //print_r($activity); die;
      $activity = $request->activity;
            
      
      if(ActivityDpr::where('user_id',$user->id)->whereIn('id',$activity)->update(['is_submit'=>'Y','status'=>'submitted'])){
        
        $result['activity'] = true;
      }
    }
    

    if(isset($request->expense) && count($request->expense)){
      // $expense = [];
      // foreach($request->expense as $k=>$v){
      //   $expense[] = $v['id'];
      // }

      $expense = $request->expense;

      if(ExpenceDpr::where('user_id',$user->id)->whereIn('id',$expense)
      ->update(['is_submit'=>'Y','status'=>'submitted'])){        
        $result['expense'] = true;
      }
    }

    if(isset($request->vehicle) && count($request->vehicle)){
      // $vehicle = [];
      // foreach($request->vehicle as $k=>$v){
      //   $vehicle[] = $v['id'];
      // }
      $vehicle = $request->vehicle;

      if(VehicleDpr::where('user_id',$user->id)->whereIn('id',$vehicle)->update(['is_submit'=>'Y','status'=>'submitted'])){        
        $result['vehicle'] = true;
      }
    }

    $this->sendMessage('dpr-submit',[],$request->site_id); 

    return response()->json(['status'=>1,'message'=>"",'data'=>$result]);

  }

  public function getBrand(Request $request){
    $status = 0;
    $message = "";
    
    $result = Brand::getBrandListByPatent();
    return response()->json(['status'=>1,'message'=>'','data'=>$result]);
  }

  public function getModelByBrand(Request $request){
    $status = 0;
    $message = "";
    $validator = Validator::make($request->all(), [     
        'brand_id'=>'required' 
    ]);
    ////open,close,fixed,reopen,my_ticket,answered,                 
    if($validator->fails()){
        //Log::debug(['add event validation failed',$request->all()]);
        return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
        
    }
     $result = Models::getModelByBrandId($request->brand_id);
     return response()->json(['status'=>1,'message'=>'','data'=>$result]);
  }

  public function getEquipmentByBrandModel(Request $request){

    $status = 0;
    $message = "";
    $validator = Validator::make($request->all(), [           
      'model_id'=>'required' 
    ]);
    ////open,close,fixed,reopen,my_ticket,answered,                 
    if($validator->fails()){
        //Log::debug(['add event validation failed',$request->all()]);
        return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
        
    }
    $result = Equipment::getEquipmentByModelId($request->model_id);
    return response()->json(['status'=>1,'message'=>'','data'=>$result]);
  }

  public function getEquipmentBySite(Request $request){

    $status = 0;
    $message = "";
    $validator = Validator::make($request->all(), [           
      'site_id'=>'required' 
    ]);
    ////open,close,fixed,reopen,my_ticket,answered,                 
    if($validator->fails()){
        //Log::debug(['add event validation failed',$request->all()]);
        return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
        
    }
    $result = Site::getEquipmentAssigned($request->site_id);
    return response()->json(['status'=>1,'message'=>'','data'=>$result]);
  }
  
  
  public function ajaxNotificationData(Request $request){
    try{
          $response = ["status"=>0,"message"=>"","ntcount"=>0,"data"=>[]];

          $user  = JWTAuth::user();  
                              
          $data = DB::table('notification_users')
          ->select('notifications.*','notification_users.created_at as created')
          ->join('notifications','notifications.id','=','notification_users.notification_id')
          ->where('notification_users.user_id',$user->id)->get();         
          $response['status'] = 1;

          $dataCount = DB::table('notification_users')
            ->select(DB::raw('count(notification_users.notification_id) as ntcount'))
            ->join('notifications','notifications.id','=','notification_users.notification_id')
            ->where('notification_users.user_id',$user->id)->first(); 

          $response['ntcount'] = $dataCount->ntcount;

          $response['data'] = $data;
          
          return response()->json($response);

      }catch(Exception $e){          
          abort(500, $e->message());
      } 
  }


  public function dashboard(Request $request){
      try{
          $response = ["status"=>0,"message"=>"","data"=>[]];

          $user  = JWTAuth::user();  

          $validator = Validator::make($request->all(), [           
            'site_id'=>'required',
            'list'=>'required'
          ]);
          ////open,close,fixed,reopen,my_ticket,answered,                 
          if($validator->fails()){
              //Log::debug(['add event validation failed',$request->all()]);
              return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
              
          }

          $site_id = $request->site_id;
          $list = $request->list;
          
          
          $response['data'] = [
            'todaysTicket'=>0,
            'monthTicket'=>0,
            'assignToMeTicket'=>0,
            'assignByMeTicket'=>0
          ];

          $todayTickets = Ticket::getTodayTicketList($site_id,$list);          
          $monthTickets = Ticket::getMonthTicketList($site_id,$list);          
          $assignToMeTickets = Ticket::getAssignToMeList($user->id,$site_id,$list);          
          $assignedByMeTickets = Ticket::getCreatedByMeList($user->id,$site_id,$list);          
          

          $response['data']['todaysTicket'] = $todayTickets;
          $response['data']['monthTicket'] = $monthTickets;
          $response['data']['assignToMeTicket'] = $assignToMeTickets;
          $response['data']['assignByMeTicket'] = $assignedByMeTickets;

          return response()->json($response);

      }catch(Exception $e){          
          abort(500, $e->message());
      } 
  }

}