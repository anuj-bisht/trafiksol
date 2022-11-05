<?php

namespace App\Http\Controllers\v1;

use App\Ticket;
use App\TicketCategory;
use App\EquipmentSla;
use App\TicketComment;
use App\TicketAsset;
use App\HardwareRequest;
use App\HardwareRequestDoc;
use App\TicketPause;
use App\TicketIssueType;
use App\Store;
use App\EquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Config;
use App\Classes\UploadFile;
use App\Http\Controllers\Traits\Common;


class TicketController extends Controller
{
    use Common;
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

    public function create(Request $request)
    {
        // print_r('hii');
        // die();
		//print_r($request->all());die;
        try{
			$status = 0;
			$message = "";
            $user  = JWTAuth::user();


            if(!isset($user->id)){
                return response()->json(['status'=>$status,'message'=>"user not found",'data'=>json_decode("{}")]);
            }

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'subject' => 'required',
                'issue_type_id' => 'required',
                'ticket_category_id' => 'required',
                'site_id' => 'required',
                'equipment_id' => 'required',
                'stretch_point' => 'required',
                'priority' => 'required',
                'description' => 'required',
            ]);
            if($validator->fails()){
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);
            }
            $result = Ticket::assignTicketToUser($request->site_id);

            $alias = DB::table('sites')->select('alias_name','sla_id')->where('id',$request->site_id)->first();
			
            $slaData = EquipmentSla::getSlaById($alias->sla_id);
			//print_r($slaData);die();
             
            if (isset($result->id)){
                // $slaData = EquipmentSla::getSlaById($request->equipment_id);
                $request->merge([
                    'created_by' => $user->id,
                    'assign_to'=> $result->id,
                ]);

                if(isset($slaData->id)){

                    $addedTime = time() + ($slaData->hours_allocated * 60 * 60);
                    $newDate = date('Y-m-d H:i:s', $addedTime);

                    $request->merge([
                        'sla_start' => date('Y-m-d H:i:s'),
                        'sla_end'=> $newDate
                    ]);
                }
                else{
                    $addedTime = time() + ($slaData->hours_allocated * 60 * 60);
                    $newDate = date('Y-m-d H:i:s', $addedTime);

                    $request->merge([
                        'sla_start' => date('Y-m-d H:i:s'),
                        'sla_end'=> $newDate
                    ]);
                }
                // print_r($request->sla_end);
                // die();
                $lastTicketId = DB::table('tickets')->select('id')->orderBy('id','DESC')->get();
                if(!$lastTicketId->isEmpty())
                {
                    $lastTicketId = $lastTicketId[0]->id+1;
                }
                else{
                    $lastTicketId = 1;
                }
                if(strlen($lastTicketId) >5)
                {
                    $lastTicketId = $lastTicketId;
                }
                else if(strlen($lastTicketId) == 4)
                {
                    $lastTicketId = '0'.$lastTicketId;
                }
                else if(strlen($lastTicketId) == 3)
                {
                    $lastTicketId = '00'.$lastTicketId;
                }
                else if(strlen($lastTicketId) == 2)
                {
                    $lastTicketId = '000'.$lastTicketId;
                }
                else if(strlen($lastTicketId) == 1)
                {
                    $lastTicketId = '0000'.$lastTicketId;
                }
                // print_r(strlen($lastTicketId));die();
                // print_r($lastTicketId);
                $request->merge([
                    'ticket_id'=> str_replace(' ','',$alias->alias_name).'/'.date('dmY').'/'.$lastTicketId,

                ]);
              
                // print_r($request->all()); die;

                $insertQuery = DB::table('tickets')->insert(['created_at'=>date('Y-m-d H:i:s'),'sla_start' => date('Y-m-d H:i:s'),'sla_end'=> $request->sla_end,'assign_to'=>$result->id,'subject'=>$request->subject,'ticket_category_id'=>$request->ticket_category_id,'stretch_point'=>$request->stretch_point,'issue_type_id' => $request->issue_type_id,'equipment_id' => $request->equipment_id,'priority' => $request->priority,'description' => $request->description,'site_id' => $request->site_id,'created_by' => $request->created_by,'assign_to' => $request->assign_to,'ticket_id' => $request->ticket_id,]);
                // $insertQuery = Ticket::create($request->all());

               if(isset($_FILES['file']['name']) && count([$_FILES['file']['name']])>0) {
                    for($k=0; $k < count([$_FILES['file']['name']]); $k++) {
                      $upload_handler = new UploadFile();
                      $path = public_path('uploads/tickets');
                      $data = $upload_handler->multiUpload($k,$path,'tickets');
                      $res = json_decode($data);
                      if($res->status=='ok'){
                        $newUserImg = new TicketAsset();
                        $newUserImg->ticket_id = $lastTicketId;
                       $newUserImg->type = $res->type;
                       $newUserImg->image = $res->path;
                        $newUserImg->file_path = $res->img_path;
                       $newUserImg->created_at = date('Y-m-d H:i:s');
                       $newUserImg->save();
                      }
                   }
                }
 
                // print_r($insertQuery->id);die();
                DB::commit();
                $data = [];

                $status = 1;
                $message = "Ticket created successfully";

               $this->sendMessage('ticket-create' , $request->all(),$request->site_id,$result->id);
                
                 //$data=['name'=>$request->created_by, 'message1'=>$request->subject];
                 //$template='';
                 //$this->SendMail($data,$template);

                return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
            }else{
                return response()->json(['status'=>$status,'message'=>"no users availabe to assign ticket",'data'=>json_decode("{}")]);
            }

        }catch(Exception $e){
			DB::rollback();
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function category(Request $request)
    {
        try{
			$status = 0;
			$message = "";
            $data = TicketCategory::getAllTicketCat();

			$status = 1;
			$message = "";
			return response()->json(['status'=>$status,'message'=>$message,'data'=>$data]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }


    public function addComment(Request $request){

        try{
			$status = 0;
            $message = "";
            $image = "";
            $user  = JWTAuth::user();

            request()->validate([
                'ticket_id' => 'required',
                'comment' => 'required',
            ]);
            $request->merge([
                'user_id' => $user->id,
            ]);


            if(isset($_FILES['file']['name']) && count($_FILES['file']['name'])>0) {
                for($k=0; $k < count($_FILES['file']['name']); $k++) {
                  $upload_handler = new UploadFile();
                  $path = public_path('uploads/ticket_comments');
                  $data = $upload_handler->multiUpload($k,$path,'ticket_comments');
                  $res = json_decode($data);
                  if($res->status=='ok'){
                    $image .= $res->path.",";
                  }
                }
                $image = substr($image,0,-1);
            }


            $obj = new TicketComment();
            $obj->ticket_id = $request->ticket_id;
            $obj->comment = $request->comment;
            $obj->comment_by = $user->id;
            $obj->image = $image;
            $obj->created_at = date('Y-m-d H:i:s');
            $obj->updated_at = date('Y-m-d H:i:s');
            $obj->save();
            //TicketComment::insert($request->all());


			$status = 1;
			$message = "";
         $this->sendCommentAdd('ticket-comment' , $request->all(),$request->ticket_id);
			return response()->json(['status'=>$status,'message'=>$message,'data'=>$obj]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }


    }


    public function hardwareRequest(Request $request){

        try{
			$status = 0;
            $message = "";



            $user  = JWTAuth::user();

            request()->validate([
                'ticket_id' => 'required',
                'quantity' => 'required',
                'ref_no' => 'required',
                'reason' => 'required',
            ]);

            $request->merge([
                'user_id' => $user->id,
            ]);
             $userData = HardwareRequest::create($request->all());
//return count(array($_FILES['file']['name']));
            // return $request->all();
  if($request->hasfile('hardware_image')){

            $image = $request->file('hardware_image');
            // dd($image);
            $name = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $path=$image->move('uploads/hardwares', $filename);
            $newUserImg = new HardwareRequestDoc();
            $newUserImg->hardware_request_id = $userData->id;
            $newUserImg->type = $extension;
            $newUserImg->image = $filename;
            $newUserImg->file_path = $path;
            $newUserImg->save();

}

			$status = 1;
			$message = "";
			return response()->json(['status'=>$status,'message'=>$message,'data'=>$userData]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function ticketPauseRequest(Request $request){

        try{
			$status = 0;
            $message = "";



            $user  = JWTAuth::user();

            request()->validate([
                'ticket_id' => 'required',
                'reason' => 'required',
            ]);

            $request->merge([
                'user_id' => $user->id,
            ]);

            $userData = TicketPause::create($request->all());

			$status = 1;
			$message = "";
			
                   $this->sendPauseMessage('ticket-sla-pause-request' , $request->all(), $request->ticket_id);
return response()->json(['status'=>$status,'message'=>$message,'data'=>$userData]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }


    public function getTicketByType(Request $request){

        try{
			$status = 0;
            $message = "";


            $user  = JWTAuth::user();



            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'site_id'=>'required'
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);

            }

            $data = Ticket::getTicketListByType($request->type,$request->search,$user->id,$request->site_id);
            $data = $data->paginate(20);

            return response()->json(['status'=>1,
                'message'=>$message,
                'total_record'=>$data->total(),
                'last_page'=>$data->lastPage(),
                'current_page'=>$data->currentPage(),
                'data'=>$data
            ]);

        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }


    public function answredTicket(Request $request){

        try{
			$status = 0;
            $message = "";


            $user  = JWTAuth::user();

            $validator = Validator::make($request->all(), [
                'site_id'=>'required'
            ]);
            ////open,close,fixed,reopen,my_ticket,answered,
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);

            }

            $data = Ticket::answredTicket($user->id,$request->site_id)->paginate(20);
            //print_r($data); die;
            //$data = $data->paginate(20);

            return response()->json(['status'=>1,
                'message'=>$message,
                'total_record'=>$data->total(),
                'last_page'=>$data->lastPage(),
                'current_page'=>$data->currentPage(),
                'data'=>$data
            ]);

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

    public function getTicketWithComment(Request $request){

        $status = 0;
        $message = "";

        try{
            $validator = Validator::make($request->all(), [
                'site_id' => 'required',
                'ticket_id'  => 'required'
            ]);
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);

            }
            $ticket = Ticket::getTicketId($request->ticket_id);
            //print_r($ticket); die;

            $remaining_hours = Ticket::getTicketRemaining($request->site_id,$request->ticket_id);
            $ticket[0]->remaining_hours = isset($remaining_hours[0]->remaining_time) ? $remaining_hours[0]->remaining_time: '';
            //echo $remaining_hours; die;
            $status = 1;
            $data = Ticket::getTicketWithComment($request->site_id,$request->ticket_id);
            $res = ['ticket'=>$ticket,'comments'=>$data];
            return response()->json(['status'=>$status,'message'=>'','data'=>$res]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }

    }

    public function gethwrlist(Request $request){
        $status = 0;
        $message = "";

        try{
            $validator = Validator::make($request->all(), [
                'site_id' => 'required'
            ]);
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'Site id not available','data'=>json_decode("{}")]);

            }
            $result = HardwareRequest::getHWListApi($request->site_id);

            $status = 1;
            return response()->json(['status'=>$status,'message'=>'','data'=>$result]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function assignHardware(Request $request){

        $status = 0;
        $message = "";

        try{
            $validator = Validator::make($request->all(), [
                'id'=>'required',
                'user_id'=>'required',
            ]);
            if($validator->fails()){
                return response()->json(['status'=>$status,'message'=>'invalid data set','data'=>json_decode("{}")]);

            }

            $res = HardwareRequest::select('hardware_requests.*','tickets.site_id','tickets.equipment_id')
            ->join('tickets','tickets.id','=','hardware_requests.ticket_id')
            ->where('hardware_requests.id',$request->id)->first();
            //dd($res->equipment_id);
            if(!isset($res->site_id)){
                return response()->json(['status'=>$status,'message'=>'record not found','data'=>json_decode("{}")]);
            }
            //echo $res->site_id;$res->equipment_id;die();
            $storeData = Store::where(['site_id'=>$res->site_id,'equipment_id'=>$res->equipment_id])
            // ->where('equipment_id',$res->equipment_id)->first();
            ->where('quantity','<>','0')->first();
            //dd($storeData);

            if(!isset($storeData->id)){
                return response()->json(['status'=>$status,'message'=>'Equipment not available in store','data'=>json_decode("{}")]);
            }else{
                $storeData->quantity = ($storeData->quantity - 1);
                $storeData->save();
            }
            //->where('status','approved')
            $res->assign_to = $request->user_id;
            $res->save();
            $status = 1;
            return response()->json(['status'=>$status,'message'=>'','data'=>$res]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function getTicketByRemainingTime(Request $request){
        $status = 0;
        $message = "";

        try{
            $validator = Validator::make($request->all(), [
                'site_id' => 'required'
            ]);
            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'Invalid Data','data'=>json_decode("{}")]);

            }
            $percentage = isset($request->percentage) ? $request->percentage : 0;

            $result = Ticket::getTicketByRemainingTime($request->site_id,$percentage);
            $status = 1;
            return response()->json(['status'=>$status,'message'=>'','data'=>$result]);
        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function ticketCloseRequest(Request $request){
        $status = 0;
        $message = "";
        $user  = JWTAuth::user();
         


        try{
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'site_id' => 'required',
                'description' => 'required'
            ]);
            $request->merge([
                'user_id' => $user->id,
            ]);

            if($validator->fails()){
                //Log::debug(['add event validation failed',$request->all()]);
                return response()->json(['status'=>$status,'message'=>'Invalid Data','data'=>json_decode("{}")]);

            }
            $tickets = Ticket::getTicketId($request->ticket_id);
            if($tickets->count() > 0){

                DB::table('ticket_close_requests')
                ->where('ticket_id', $request->ticket_id)
                ->where('is_approved', 'submitted')
                ->delete();

                DB::table('ticket_close_requests')->insert(
                    ["ticket_id"=>$request->ticket_id,
                    "description"=>$request->description,
                    "request_by" => $user->id]
                );
                $status = 1;
                $this->sendTicketCloseRequest('ticket-status-change' , $request->all(), $request->ticket_id);
                return response()->json(['status'=>$status,'message'=>'','data'=>json_decode("{}")]);

            }else{
                return response()->json(['status'=>$status,'message'=>'Ticket does not exist','data'=>json_decode("{}")]);
            }

        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function fetchRequest(Request $request){
        $status = 0;
        $message = "";
        $user  = JWTAuth::user();
       // $result = json_decode("{}");
        try{
            $type = 'hardware';
            if(isset($request->type)){
                $type = $request->type;
            }
            if($type=='hardware'){
                $result = HardwareRequest::select('hardware_requests.*',
                'tickets.id as ticket_id','tickets.subject','users.name as username','equipments.title as equipment_title')
                ->leftJoin('tickets','tickets.id','=','hardware_requests.ticket_id')
                ->leftJoin('users','users.id','=','hardware_requests.user_id')
                ->leftJoin('equipments','equipments.id','=','hardware_requests.equipment_id')
                ->get();
            }
            if($type=='equipment'){
                $site_id = 1;
                if(isset($request->site_id)){
                    $site_id = $request->site_id;
                }
                $result = EquipmentRequest::select('equipment_requests.*',
                'sites.id as site_id','sites.name as site_name')
                ->leftJoin('sites','sites.id','=','equipment_requests.site_id')
                ->where('equipment_requests.site_id',$site_id)
                ->get();
            }
            if($result->count() > 0){
                $status = 1;
                return response()->json(['status'=>$status,'message'=>'','data'=>$result]);
            }else{
                return response()->json(['status'=>$status,'message'=>'record does not exist','data'=>json_decode("{}")]);
            }
        }catch(Exception $e){
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }
} 
