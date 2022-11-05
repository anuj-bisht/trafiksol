<?php

namespace App\Http\Controllers\Traits;
use Mail;
use Config;
use App\Notification;
use App\SiteAdvance;
use App\User;
use DB;

trait SendMail {
    protected function SendMail($data,$template='message') {
        try{
            Mail::send($template,$data, function($newObj) use($data)
            {

               $newObj->from($data['from'], $data['name']);
                $newObj->subject($data['subject']);
                $newObj->to($data['to_email']);

                if(isset($data['cc'])){
                  $newObj->cc($data['cc']);
                }
                if(isset($data['bcc'])){
                  $newObj->bcc($data['bcc']);
                }

                if(isset($data['file'])){
                  $newObj->attach($data['file'], [
                    'as' => $data['file'],
                    'mime' => 'application/pdf',
                  ]);
                }
            });
            return true;
          }catch(Exception $e){
            return false;
          }
    }

    protected function SendSms($phone,$message) {
        try{
            $url = "https://api.msg91.com/api/sendhttp.php?mobiles=$phone&authkey=298624AWJzQa0Z8n5da2dd16&route=4&sender=TRFKSL&message=$message&country=91";
            if(file_get_contents($url)){
              return true;
            }
          }catch(Exception $e){
            return false;
          }
    }

    protected function SendMsgNotification($notification_id,$users,$dataArray) {
        try{
           if(isset($notification_id) && count($users)){
                foreach($users as $k=>$v){
                    $data[$k]['notification_id'] = $notification_id;
                    $data[$k]['user_id'] = $v;
                    $data[$k]['obj_data'] = serialize($dataArray);
                }

                DB::table('notification_users')->insert($data);
           }
            return true;
          }catch(Exception $e){
            return false;
          }
    }


//Request to pause ticket SLA initiated <ticket name>-<sitename>- request added by <username>
    public function sendPauseMessage($typee,$dataArrayy,$ticket_id){
 try{
               
             $ticket_name=DB::table('ticket_pauses')
             ->join('tickets', 'ticket_pauses.ticket_id', '=', 'tickets.id')
              ->where('tickets.id', $ticket_id)
             ->select('tickets.id','tickets.ticket_id','ticket_pauses.user_id', 'tickets.site_id', 'tickets.site_id')->get();
              
               $sitename=DB::table('sites')->where('id',$ticket_name['0']->site_id )->get();
            // echo $sitename[0]->name;  chutmulpur site name

             $result = Notification::getDataByItemCode($typee,$ticket_name['0']->site_id);
             $userDetails = User::find($dataArrayy['user_id']);
             $result = Notification::getDataByItemCode($typee,$ticket_name[0]->site_id);
                 //echo $userDetails['name'] ;  jitender chaudhary
         
                $subject =  str_replace('<sitename>',$sitename[0]->name,$result[0]->subject);

                $subject =  str_replace('<username>',$userDetails['name'],$subject);
                $subject =  str_replace('<ticket name>',$ticket_name[0]->ticket_id,$subject);


        
          if($result->count()){
//$datecurrent=date('Y-m-d H:i:s');
            $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
            $email_message=str_replace('<ticketname>',$ticket_name[0]->ticket_id, $email_messag);
            $email_message=str_replace('<sitename>',$sitename[0]->name, $email_message);
            $email_message=str_replace('<username>',$userDetails['name'], $email_message);
           
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$sitename[0]->name;
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArrayy;


                $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArrayy);
            }
        }                  
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }
}


    public function sendTicketCloseRequest($type,$dataArray,$ticket_id){
  
try{
               $ticket_name=DB::table('ticket_close_requests')
             ->join('tickets', 'ticket_close_requests.ticket_id', '=', 'tickets.id')
              ->where('tickets.id', $ticket_id)
             ->select('tickets.id','tickets.description','tickets.ticket_id', 'tickets.site_id')->get();
    
               $sitename=DB::table('sites')->where('id',$ticket_name['0']->site_id )->get();
            // echo $sitename[0]->name;  chutmulpur site name

             $result = Notification::getDataByItemCode($type,$ticket_name['0']->site_id);
             $userDetails = User::find($dataArray['user_id']);
                  $result = Notification::getDataByItemCode($type,$ticket_name[0]->site_id);
                 //echo $userDetails['name'] ;  jitender chaudhary
                    $subject =  str_replace('<Site Name>',$sitename[0]->name,$result[0]->subject);
               $subject =  str_replace('<ticketname>', $ticket_name[0]->ticket_id, $subject);
                $subject =  str_replace('<user name>',$userDetails['name'],$subject);
                
        
          if($result->count()){
//$datecurrent=date('Y-m-d H:i:s');
            $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
            $email_message=str_replace('<ticketname>',$ticket_name[0]->ticket_id, $email_messag);
            $email_message=str_replace('<Site Name>',$sitename[0]->name, $email_message);
            $email_message=str_replace('<user name>',$userDetails['name'], $email_message);
            $email_message=str_replace('<ticket description>',$ticket_name[0]->description, $email_message);

           
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$sitename[0]->name;
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArray;
                   $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArray);
            }
        }


                   
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }




}

   public function createSite($type,$dataArray, $project_id){

try{
                  $project_name=DB::table('projects')->where('id',$project_id)->get();
                  $user_name=DB::table('users')->where('id',$dataArray['user_id'])->get();
		  //print_r($user_name[0]->name); ---->username
                 //print_r($dataArray['name']);  ->>>>>new  site name

              //print_r($project_name[0]->name);  ->>>>>project name

           
            
               
             
                  $result = Notification::getDataByItemCode($type,6);
                 //echo $userDetails['name'] ;  jitender chaudhary
         
                $subject =  str_replace('project',$project_name[0]->name,$result[0]->subject);
               $subject =  str_replace('name',$user_name[0]->name,$subject);
		$subject =  str_replace('site',$dataArray['name'] ,$subject);
    


                

        
          if($result->count()){
//$datecurrent=date('Y-m-d H:i:s');
            $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
    
            $email_message=str_replace('Projectname',$project_name[0]->name, $email_messag);
            $email_message=str_replace('site',$dataArray['name'], $email_message);
            $email_message=str_replace('userrr',$user_name[0]->name, $email_message);
    
           
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$dataArray['name'];
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArray;
                   $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArray);
            }
        }


                   
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }

                          
    



}

public function editSite($type,$dataArray, $project_id){
 try{
                  $project_name=DB::table('projects')->where('id',$project_id)->get();
                  $user_name=DB::table('users')->where('id',$dataArray['user_id'])->get();
		  //print_r($user_name[0]->name); ---->username
                 //print_r($dataArray['name']);  ->>>>>new  site name

              //print_r($project_name[0]->name);  ->>>>>project name

           
            
               
             
                  $result = Notification::getDataByItemCode($type,6);
                 //echo $userDetails['name'] ;  jitender chaudhary
         
                $subject =  str_replace('Project',$project_name[0]->name,$result[0]->subject);
   
               $subject =  str_replace('user',$user_name[0]->name,$subject);
		$subject =  str_replace('sitename',$dataArray['name'] ,$subject);
    

                

        
          if($result->count()){
//$datecurrent=date('Y-m-d H:i:s');
            $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
    
            $email_message=str_replace('Projectname',$project_name[0]->name, $email_messag);
            $email_message=str_replace('site',$dataArray['name'], $email_message);
            $email_message=str_replace('user',$user_name[0]->name, $email_message);
              
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$dataArray['name'];
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArray;
                   $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArray);
            }
        }


                   
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }

                          
    



}

public function addEquipment($type,$dataArray, $site_id){

 try{
                  $site_name=DB::table('sites')->where('id',$site_id)->get();
                  $user_name=DB::table('users')->where('id',$dataArray['user_id'])->get();
                  $description= $dataArray['equipment_chainage'];
		  //print_r($user_name[0]->name); ---->username
                 //print_r($dataArray['name']);  ->>>>>new  site name

              //print_r($site_name[0]->name);  ->>>>>project name

           
            
               
             
                  $result = Notification::getDataByItemCode($type,$site_id);
                 //echo $userDetails['name'] ;  jitender chaudhary
         
                $subject =  str_replace('Sitename',$site_name[0]->name,$result[0]->subject);
  
        
          if($result->count()){
//$datecurrent=date('Y-m-d H:i:s');
            $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
    
            $email_message=str_replace('Sitename',$site_name[0]->name, $email_messag);
            $email_message=str_replace('Equipmentdescription',$dataArray['equipment_chainage'], $email_message);
            $email_message=str_replace('user',$user_name[0]->name, $email_message);
             
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$site_name[0]->name;
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArray;
                   $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArray);
            }
        }


                   
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }
}



public function editEquipment($type,$dataArray, $site_id){

 try{
                  $site_name=DB::table('sites')->where('id',$site_id)->get();
                  $user_name=DB::table('users')->where('id',$dataArray['user_id'])->get();
                  $description= $dataArray['equipment_chainage'];
		  //print_r($user_name[0]->name); ---->username
                 //print_r($dataArray['name']);  ->>>>>new  site name

              //print_r($site_name[0]->name);  ->>>>>project name

           
            
               
             
                  $result = Notification::getDataByItemCode($type,$site_id);
                 //echo $userDetails['name'] ;  jitender chaudhary
         
                $subject =  str_replace('Sitename',$site_name[0]->name,$result[0]->subject);
  
        
          if($result->count()){
//$datecurrent=date('Y-m-d H:i:s');
            $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
    
            $email_message=str_replace('Sitename',$site_name[0]->name, $email_messag);
            
            $email_message=str_replace('user',$user_name[0]->name, $email_message);
             
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$site_name[0]->name;
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArray;
                   $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArray);
            }
        }


                   
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }
}















    public function sendCommentAdd($typeee,$dataArrayyy,$ticket_id){
             try{
               $ticket_name=DB::table('ticket_comments')
             ->join('tickets', 'ticket_comments.ticket_id', '=', 'tickets.id')
              ->where('tickets.id', $ticket_id)
             ->select('tickets.id','tickets.description','tickets.ticket_id','ticket_comments.comment_by', 'tickets.site_id')->get();
    
               $sitename=DB::table('sites')->where('id',$ticket_name['0']->site_id )->get();
            // echo $sitename[0]->name;  chutmulpur site name

             $result = Notification::getDataByItemCode($typeee,$ticket_name['0']->site_id);
             $userDetails = User::find($dataArrayyy['user_id']);
                  $result = Notification::getDataByItemCode($typeee,$ticket_name[0]->site_id);
                 //echo $userDetails['name'] ;  jitender chaudhary
         
                $subject =  str_replace('<sitename>',$sitename[0]->name,$result[0]->subject);

                $subject =  str_replace('<username>',$userDetails['name'],$subject);
                $subject =  str_replace('<ticket name>',$ticket_name[0]->ticket_id,$subject);


        
          if($result->count()){
//$datecurrent=date('Y-m-d H:i:s');
            $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
            $email_message=str_replace('<ticketname>',$ticket_name[0]->ticket_id, $email_messag);
            $email_message=str_replace('<sitename>',$sitename[0]->name, $email_message);
            $email_message=str_replace('<username>',$userDetails['name'], $email_message);
            $email_message=str_replace('<ticket description>',$ticket_name[0]->description, $email_message);

           
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$sitename[0]->name;
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArrayyy;

                   $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArrayyy);
            }
        }


                   
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }
}








//"Ticket created for Site  - ".$sitename." - ".$username." - <ticket description>"
//$this->sendMessage('ticket-create' , $request->all(),$request->site_id,$result->id);
    public function sendMessage($type,$dataArray,$site_id,$user_id=""){
        $siteModel= new SiteAdvance();

      try{
        $result = Notification::getDataByItemCode($type,$site_id);
        $siteDetails = $siteModel->getsiteDetailsById($site_id);
        $userDetails = User::getUserAllInfoByUserId($user_id);
    
        // print_r($userDetails);die();
        $sitename = $siteDetails[0]->name;
        $username = $userDetails[0]->username;
        // Ticket created for Site  <Ticket created> - <Site Name> - <User Name>-<ticket description>

        $subject =  str_replace('<sitename>',$sitename,$result[0]->subject);

        $subject =  str_replace('<username>',$username,$subject);
        $subject =  str_replace('<ticket description>',$dataArray['description'],$subject);
        $subject =  str_replace('<Ticket created>',$dataArray['ticket_id'],$subject);
          
// print_r($dataArray);die();
        if($result->count()){
$datecurrent=date('Y-m-d H:i:s');
                      $notification_id = $result[0]->id;
            $email_subject = $subject;
            $email_messag = $result[0]->message;
            $email_message=str_replace('<date>',$datecurrent, $email_messag);
            $email_message=str_replace('<sitename>',$sitename, $email_message);
            $email_message=str_replace('<username>',$username, $email_message);
            $email_message=str_replace('<ticket description>',$dataArray['description'], $email_message);
            
            $mobile_message = $result[0]->mobile_message;
            $mobile_message = $result[0]->mobile_message;
            $notification_message = $result[0]->notification_message;
            $phoneArr = [];
            $emailArr = [];
            $idArr = [];
            $nameArr = [];
            foreach($result as $k=>$v){
              if(!empty($v->phone) && $v->sms_notification=='1'){
                $phoneArr[] = $v->phone;
              }
              if(!empty($v->email) && $v->email_notification=='1'){
                $emailArr[] = $v->email;
              }
              if(!empty($v->user_id)){
                $idArr[] = $v->user_id;
              }
            }


            if(Config('app.email_message')){

                $data = [];
                $data['sitename']=$sitename;
                $data['name'] = 'Member';
                $data['to_email'] = $emailArr;

                //print_r($data['to_email']); die;
                //$data['cc'] = 'hemant.gupta@techconfer.com';
                $data['from'] = config('app.from_email');
                $data['subject'] = $email_subject;
                $data['message1'] = $email_message;
                $data['info_data'] = $dataArray;



                $this->SendMail($data,'message');
            }
            if(config('app.mobile_message')){
              $this->SendSms(implode(",",$phoneArr),$mobile_message);
            }
            if(config('app.notification_message')){
               $this->SendMsgNotification($notification_id,$idArr,$dataArray);
            }
        }
      }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
      }

  }
}

