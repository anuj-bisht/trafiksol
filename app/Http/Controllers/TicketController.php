<?php

namespace App\Http\Controllers;
use Carbon\Carbon;


// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Ticket;
use App\TicketComment;
use App\User;
use App\Role;
use App\Site;
use App\HardwareRequest;
use App\Exports\ReportData;
use Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use DB;
use Auth;
use PDF;
use App\TicketPause;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use App\Http\Controllers\Traits\SendMail;


class TicketController extends Controller
{
    use Common;
    use SendMail;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $tickets = Ticket::latest()->paginate(5);
        $sites = Site::getSiteListDD();
        return view('tickets.index',['sites'=>$sites])
            ->with('i', (request()->input('page', 1) - 1) * $this->paging);
    }

    public function ajaxData(Request $request){

        $draw = (isset($request->data["draw"])) ? ($request->data["draw"]) : "1";
        $response = [
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => "",
          "success" => 0,
          "msg" => ""
        ];
        try {

            $start = ($request->start) ? $request->start : 0;
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];
            $flag = 0;

            //echo '<pre>'; print_r($users); die; categoryFilter
            $obj = Ticket::getTicketList();

            //$this->DateDiff()

            if ($request->search['value'] != "") {
              $obj = $obj->where('subject','LIKE',"%".$search."%");
            }

            if(isset($request->date_from) && isset($request->date_to)){
                $obj = $obj->whereBetween(DB::raw('DATE(tickets.created_at)'),[$request->date_from,$request->date_to]);
            }

            if(isset($request->siteFilter) && $request->siteFilter>0){
                $obj = $obj->where('tickets.site_id',$request->siteFilter);
            }

            if(isset($request->statusFilter) && $request->statusFilter != '0'){
                $obj = $obj->where('tickets.status',$request->statusFilter);
            }

            if(isset($request->hourFilter) && $request->hourFilter>0){
                //$obj = $obj->where('tickets.site_id',$request->siteFilter);
                //$this->calculateRemainingHour($ticket_id)
            }
            // echo $request->order[0]['column'];
            // echo $request->order[0]['dir'];
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                if($sort == 'asc')
                {
                    $obj = $obj->orderBy('tickets.created_at',"desc");
                }
                else{
                    $obj = $obj->orderBy('tickets.created_at',"asc");
                }

            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('tickets.subject',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('ticket_issue_types.name',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('ticket_categories.name',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.name',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('equipments.title',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==6){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('tickets.stretch_point',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==7){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('tickets.priority',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==9){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('tickets.status',$sort);
            }

            // if(!$flag){
                $obj = $obj->orderBy('tickets.id','desc');
            // }

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }

            if($obj->count()){
                foreach($obj as $k=>$v){
                    $closureReq = DB::table("ticket_close_requests")->where(["ticket_id"=>$obj[$k]->id,"is_approved"=>"submitted"])->get();
                    if(!$closureReq->isEmpty())
                    {
                        $obj[$k]->status = "Closure Request";
                    }
                    // echo $v->sla_start;
                    // echo '<br>';
                    // echo $v->sla_end;
                    // echo "<br>";
                    // echo $this->DateDiff($v->sla_start,$v->sla_end);
                    // echo "<br>";
                    $obj[$k]->hours_consumed = $this->DateDiff($v->sla_start,$v->sla_end);
                }
            }

            $response["recordsFiltered"] = $total;
            $response["recordsTotal"] = $total;
            //response["draw"] = draw;
            $response["success"] = 1;
            $response["data"] = $obj;

          } catch (Exception $e) {

          }


        return response($response);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $typeVehicleList = TypeVehicle::getVehicleTypeList();


        //echo '<pre>'; print_r($optionData); die;
        return view('tickets.create',compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //echo '<pre>'; print_r($request->all()); die;
        try{
            DB::beginTransaction();

            request()->validate([
                'name' => 'required',
                'project_id' => 'required',
                'client_id' => 'required',
                'alias_name' => 'required',
                'stretch_from' => 'required',
                'stretch_to' => 'required',
                'location' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
            ]);

            $insertQuery = Ticket::create($request->all());

            $data = [];
            if(count($request->to)>0){
                foreach($request->to as $k=>$v){
                    $data[$k]['ticket_id'] = $insertQuery->id;
                    $data[$k]['user_id'] = $v;
                }
                //ticketUser::where('ticket_id', $ticket->id)->delete();
                ticketUser::insert($data);
                DB::commit();
            }

            return redirect()->route('tickets.index')
                            ->with('success','ticket created successfully.');

        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        return view('tickets.show',compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket)
    {


        return view('tickets.edit',compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        try{
            DB::beginTransaction();

            request()->validate([
                'name' => 'required',
                'client_id' => 'required',
                'alias_name' => 'required',
                'stretch_from' => 'required',
                'stretch_to' => 'required',
                'location' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
            ]);

            $ticket->update($request->all());

            $data = [];
            if(count($request->to)>0){
                foreach($request->to as $k=>$v){
                    $data[$k]['ticket_id'] = $ticket->id;
                    $data[$k]['user_id'] = $v;
                }
                ticketUser::where('ticket_id', $ticket->id)->delete();
                ticketUser::insert($data);
                DB::commit();
            }

            return redirect()->route('tickets.index')
                            ->with('success','ticket updated successfully');

        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')
                        ->with('success','Ticket deleted successfully');
    }


    public function getTicketInfo(Request $request){

        try{
			$status = 0;
            $message = "";
            $ticket_id = $request->ticket_id;
            $ticketData = Ticket::getTicketId($ticket_id);

            //$getAllSIteEnd = [];
			if(!$ticketData->count()){
				return response()->json(['status'=>$status,'message'=>'No ticket data found','data'=>json_decode("{}")]);
			}
			
            $ticketData[0]->remaining_hours = $this->DateDiff($ticketData[0]->sla_start,$ticketData[0]->sla_end) ?? 0;
            //$assignTo = Ticket::getUserByTicketId($ticket_id);
            // print_r($ticketData);die();

            //$ticketData[0]->assign_to_user = $assignTo[0]->name;

            $commentData = TicketComment::getCommentsByTicketId($ticket_id);
            $closingcommentData = TicketComment::getClosingCommentsByTicketId($ticket_id);

            $getAllSIteEnd = User::getAllSiteEngineer();

            $data = ['ticket'=>$ticketData,'comments'=>$commentData,'siteuser'=>$getAllSIteEnd,"closure_request"=>$closingcommentData];
            if(count($ticketData)){
                $status = 1;
			    $message = "";
			    return response()->json(['status'=>$status,'message'=>$message,'data'=>$data]);
            }else{
			    $message = "No comment";
			    return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
            }

        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }

    }

    public function addcomments(Request $request){

        try{
			$status = 0;
            $message = "";
            $comment = $request->comment;
            $comment_by = Auth::user()->id;
            $hidden_ticket_id = $request->hidden_ticket_id;
            $obj = Ticket::findOrFail($hidden_ticket_id);
            $obj->status = $request->status;
            $obj->reason = $request->closing;
            // $obj->created_at = date('Y-m-d H:i:s');
            $obj->updated_at = date('Y-m-d H:i:s');

            if($request->status == 'close')
            {
                DB::table("ticket_close_requests")->where("ticket_id",$hidden_ticket_id)->update(["is_approved"=>"approved"]);
                DB::table("tickets")->where("id",$hidden_ticket_id)->update(["close_time"=>$request->closeTime]);
            }
            if(isset($request->siteuser)){
                $ticketData = Ticket::getTicketId($hidden_ticket_id);
                if($obj->assign_to != $request->siteuser){
                    DB::table("tickets")->where("id",$hidden_ticket_id)->update(["assign_to"=>$request->siteuser]);
                    // DB::insert('insert into ticket_history (ticket_id,assign_to, assign_by) values (?, ?, ?)', [$hidden_ticket_id, $request->siteuser,$request->siteuser]);
                    $obj->assign_to = $request->siteuser;

                }
            }

            $obj->save();

            $result = DB::table('ticket_comments')->insert(
                ['comment_by' => $comment_by, 'comment' => $comment,'ticket_id'=>$hidden_ticket_id,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]
            );
            if($result){
                $status = 1;
			    $message = "";
			    return response()->json(['status'=>$status,'message'=>$message,'data'=>$result]);
            }else{
			    $message = "No comment";
			    return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
            }

        }catch(Exception $e){

            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);
        }
    }

    public function hwrequest()
    {
        $sites = Site::getSiteListDD();
        return view('tickets.hwrequest',['sites'=>$sites]);
    }

    public function hwrequestAjax(Request $request)
    {

        $draw = (isset($request->data["draw"])) ? ($request->data["draw"]) : "1";
        $response = [
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => "",
          "success" => 0,
          "msg" => ""
        ];
        try {

            $start = ($request->start) ? $request->start : 0;
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];

            //echo '<pre>'; print_r($users); die; categoryFilter
            //$obj = HardwareRequest::getHWList();
            
            $obj = HardwareRequest::select('hardware_requests.*','users.name as username','tickets.subject', 'hardware_request_docs.file_path as path')            
                ->join('users', 'hardware_requests.user_id', '=', 'users.id')
                                            
               ->join('tickets', 'tickets.id', '=', 'hardware_requests.ticket_id')
                 ->leftjoin('hardware_request_docs', 'hardware_request_docs.hardware_request_id', '=', 'hardware_requests.id');   
               //, 'hardware_request_docs.file_path as path'
                          //$this->DateDiff()
            if ($request->search['value'] != "") {
              $obj = $obj->where('hardware_requests.quantity','LIKE',"%".$search."%");
              $obj = $obj->orWhere('users.name','LIKE',"%".$search."%");
              $obj = $obj->orWhere('hardware_requests.reason','LIKE',"%".$search."%");
              $obj = $obj->orWhere('hardware_requests.ref_no','LIKE',"%".$search."%");
            }


            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('tickets.subject',$sort);
            }

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }

            if($obj->count()){
                // foreach($obj as $k=>$v){
                //     $obj[$k]->hours_consumed = $this->DateDiff($v->sla_start,$v->sla_end);
                // }
            }

            $response["recordsFiltered"] = $total;
            $response["recordsTotal"] = $total;
            //response["draw"] = draw;
            $response["success"] = 1;
            $response["data"] = $obj;

          } catch (Exception $e) {

          }


        return response($response);
    }

    public function pause()
    {
        $sites = Site::getSiteListDD();
        return view('tickets.pause',['sites'=>$sites]);
    }

    public function pauseAjax(Request $request)
    {
        $draw = (isset($request->data["draw"])) ? ($request->data["draw"]) : "1";
        $response = [
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => "",
          "success" => 0,
          "msg" => ""
        ];
        try {

            $start = ($request->start) ? $request->start : 0;
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];
            $site_id = 0;
            //echo '<pre>'; print_r($users); die; categoryFilter
            $obj = TicketPause::getPauseList($site_id);

            //$this->DateDiff()
            if(isset($request->siteFilter) && $request->siteFilter!=0){
                $obj = $obj->where('tickets.site_id',$request->siteFilter);
            }
            if ($request->search['value'] != "") {
              $obj = $obj->where('ticket_pauses.pause_from','LIKE',"%".$search."%");
              $obj = $obj->orWhere('users.name','LIKE',"%".$search."%");
              $obj = $obj->orWhere('ticket_pauses.reason','LIKE',"%".$search."%");
              $obj = $obj->orWhere('ticket_pauses.pause_to','LIKE',"%".$search."%");
            }


            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('tickets.subject',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('users.username',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('ticket_pauses.pause_from',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('ticket_pauses.pause_to',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('ticket_pauses.reason',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('ticket_pauses.is_approved',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==6){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('ticket_pauses.created_at',$sort);
            }

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }

            if($obj->count()){
                // foreach($obj as $k=>$v){
                //     $obj[$k]->hours_consumed = $this->DateDiff($v->sla_start,$v->sla_end);
                // }
            }

            $response["recordsFiltered"] = $total;
            $response["recordsTotal"] = $total;
            //response["draw"] = draw;
            $response["success"] = 1;
            $response["data"] = $obj;

          } catch (Exception $e) {

          }


        return response($response);
    }

    public function approveRejectPause(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            if(isset($request->type)){
              $status = ($request->type=='1') ? 'Y' : 'R';
              $result = TicketPause::whereIn('id',$request->ids)->where('is_approved','N')->update(['is_approved'=>$status]);

              if($result){
                $response['status'] = '1';
                $response['data'] = [];
              }else{
                $response['message'] = "No record to update";
              }

              return response()->json($response);
            }

        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }
    }
    
    public function approveRejectHWRequest(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            if(isset($request->type)){
              $status = ($request->type=='1') ? 'approved' : 'rejected';
              $result = HardwareRequest::whereIn('id',$request->ids)->where('status','open')->update(['status'=>$status]);
              
              $hwData = [];
              $storeDetails = HardwareRequest::whereIn('id',$request->ids)->get();
              if($storeDetails->count()){
				
				foreach($storeDetails as $k=>$v){
					$hwData[$k]['equipment_required'] = $v->equipment_required;
					$hwData[$k]['quantity'] = $v->quantity;
					 
				}
			  }
              
			  $storeManager = User::getAllStoreManager();
			  $emails = [];
			  if($storeManager->count()){
				foreach($storeManager as $k=>$v){
						$emails[] = $v->email;
				}
			  }
			  
			  
              if($result){
				 
				$data = [];
				$data['to_email'] = $emails;
				$data['from'] = 'Support@trafiksol.com';
				$data['subject'] = 'Harware Request Approved!';
				$data['name'] = 'Store incharge';
				$data['message1'] = 'New hardware request is approved by admin, Please login to portal and check it';
				$data['hwData'] = $hwData;				
				
			    $this->SendMail($data,'admin_hardware_request'); 
                $response['status'] = '1';
                $response['data'] = [];
              }else{
                $response['message'] = "No record to update";
              }

              return response()->json($response);
            }

        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }
    }

    public function ticketPauseStartStop(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->id;
            $type = $request->type;

            $result = TicketPause::where('id',$id)->first();

            if(isset($result->id)){
                if($type=="start" && $result->is_approved=='Y'){
                    $result->pause_from = date('Y-m-d H:i:s');
                    $result->start = 'Y';
                    if($result->save()){
                        $response['status'] = 1;
                    }else{
                        $response['message'] = 'unable to start pause';
                    }

                    return response()->json($response);
                }
                if($type=="stop" && $result->is_approved=='Y' && $result->start='Y'){
                    $result->pause_to = date('Y-m-d H:i:s');
                    $result->end = 'Y';
                    if($result->save()){
                        $response['status'] = 1;
                    }else{
                        $response['message'] = 'unable to stop pause';
                    }

                    return response()->json($response);
                }
            }else{
                $response['message'] = 'No data found';
                return response()->json($response);
            }

        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    function SiteReport(){
     
        
        $data['sites'] = DB::table('sites')
        ->select('sites.name as sitename','sites.alias_name', 'sites.id as id')->get();
             
      return view('tickets.sitereport', $data);

    }

  
    function GetData(Request $request){

        $date=date("Y-m-d");
        $last_sunday = date('Y-m-d', strtotime($date.'last sunday'));
		
        if($request->datefilter=='Weekly'){
        $data=DB::table('tickets')
        ->select('clients.image as client_image','clients.name as client_name')
        ->join('sites', 'tickets.site_id','=', 'sites.id')
        ->join('clients', 'clients.id','=', 'sites.client_id')
        ->join('users', 'tickets.created_by','=', 'users.id')
        ->join('equipments', 'tickets.equipment_id','=', 'equipments.id')
        ->join('ticket_categories', 'tickets.ticket_category_id','=', 'ticket_categories.id')        
        ->join('ticket_issue_types', 'tickets.issue_type_id','=', 'ticket_issue_types.id')
        ->select('tickets.*','users.name as created',DB::raw('(CASE WHEN tickets.close_time IS NULL THEN "Open Ticket" 
        WHEN tickets.close_time < 0 THEN "Overdue Closed" 
        ELSE "Closed Ticket" END) AS timestatus') , 'equipments.title as equipment', 'ticket_categories.name as ticket_categories', 'ticket_issue_types.name as issue','clients.image as client_image','clients.name as client_name')
        ->where('site_id', $request->id)
        //->where('tickets.created_at','>=',Carbon::now()->subdays(7)) 
        ->where(DB::raw('DATE(tickets.created_at)'),'>=',$last_sunday) 

        
        ->groupBy('tickets.id')
        ->get();
        
        return  $data; 
    }
        elseif($request->datefilter=='Monthly'){
        $data=DB::table('tickets')
      
        ->join('sites', 'tickets.site_id','=', 'sites.id')
        ->join('clients', 'clients.id','=', 'sites.client_id')
        ->join('users', 'tickets.created_by','=', 'users.id')
        ->join('equipments', 'tickets.equipment_id','=', 'equipments.id')        
        ->join('ticket_categories', 'tickets.ticket_category_id','=', 'ticket_categories.id')
        ->join('ticket_issue_types', 'tickets.issue_type_id','=', 'ticket_issue_types.id')
        ->select( 'tickets.*',
        DB::raw('(CASE WHEN tickets.close_time IS NULL THEN "Open Ticket" 
        WHEN tickets.close_time < 0 THEN "Overdue Closed" 
        ELSE "Closed Ticket" END) AS timestatus') ,
        'users.name as created', 
        'equipments.title as equipment', 'ticket_categories.name as ticket_categories', 
        'ticket_issue_types.name as issue','clients.image as client_image',
        'clients.name as client_name')
        ->where('site_id', $request->id)
        //->where('tickets.created_at','>=',Carbon::now()->subdays(30)) 
        ->where(DB::raw('DATE(tickets.created_at)'),'>=',date('Y-m-01')) 
        
        ->groupBy('tickets.id')
        ->get();
        
        return  $data; 
    }

        //  return Excel::download(new DataExport, 'reportdata.xlsx');     
        //  orWhereDateBetween('created_at',(new Carbon)->subDays(10)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString() )->get(); 
    }

function GetTicketData(Request $request){

    $date=date("Y-m-d");
    $last_sunday = date('Y-m-d', strtotime($date.'last sunday'));


    if($request->datefilter=="Monthly"){
    $ticketdata = DB::table('tickets')
                 ->select(
                     DB::raw('(CASE WHEN tickets.close_time IS NULL THEN "Open Ticket" 
                 WHEN tickets.close_time < 0 THEN "Overdue Closed" 
                 ELSE "Closed Ticket" END) AS status2'), 
                 DB::raw('count(*) as total'))
                 ->groupBy('status2')
                 //->where('tickets.created_at','>=',Carbon::now()->subdays(30)) 
                 ->where(DB::raw('DATE(tickets.created_at)'),'>=',date('Y-m-01')) 
                //  ->where('tickets.status','close')
                 ->where('site_id', $request->id)
                 ->get();
                

                 return $ticketdata;
    }
    elseif($request->datefilter=="Weekly"){
        $ticketdata = DB::table('tickets')
                     ->select(DB::raw('(CASE WHEN tickets.close_time IS NULL THEN "Open Ticket" 
                     WHEN tickets.close_time < 0 THEN "Overdue Closed" 
                     ELSE "Closed Ticket" END) AS status2'), 
                     DB::raw('count(*) as total'))
                     ->groupBy('status2')
                     //->where('tickets.created_at','>=',Carbon::now()->subdays(7)) 
                     ->where(DB::raw('DATE(tickets.created_at)'),'>=',$last_sunday) 
                    //  ->where('tickets.status','close')
                     ->where('site_id', $request->id)
                     ->get();
                     
                     return $ticketdata;
                     die();
                     
        }
}

function GetHistogramData(Request $request){

    $date=date("Y-m-d");
    $last_sunday = date('Y-m-d', strtotime($date.'last sunday'));

    if($request->datefilter=="Monthly"){
    $ticketdata = DB::table('tickets')
                 ->select('status', DB::raw('count(*) as total'))
                 ->groupBy('status')
                 //->where('tickets.created_at','>=',Carbon::now()->subdays(30)) 
                 ->where(DB::raw('DATE(tickets.created_at)'),'>=',date('Y-m-01')) 
                //  ->where('tickets.status','close')
                 ->where('site_id', $request->id)
                 ->get();
                 return $ticketdata;
    }
    elseif($request->datefilter=="Weekly"){
        $ticketdata = DB::table('tickets')
                     ->select('status', DB::raw('count(*) as total'))
                     ->groupBy('status')
                     //->where('tickets.created_at','>=',Carbon::now()->subdays(7)) 
                     ->where(DB::raw('DATE(tickets.created_at)'),'>=',$last_sunday) 
                    //  ->where('tickets.status','close')
                     ->where('site_id', $request->id)
                     ->get();
                     return $ticketdata;
        }
}
public function exportIntoExcel()
{
    return Excel::download(new ReportData,'reportdata.xlsx');
}
public function exportIntoCSV(Request $request)
{
    return Excel::download(new ReportData($request->id),'reportdata.csv');
} 

public function reportpdf(Request $request){

    $date=date("Y-m-d");
    $last_sunday = date('Y-m-d', strtotime($date.'last sunday'));
    
    if($request->datefilter=='Weekly'){
        $data=DB::table('tickets')
        ->select('tickets.*','clients.image as client_image','clients.name as client_name')
        ->join('users', 'tickets.created_by','=', 'users.id')
        ->join('equipments', 'tickets.equipment_id','=', 'equipments.id')
        ->join('sites', 'tickets.site_id','=', 'sites.id')
        ->join('clients', 'clients.id','=', 'sites.client_id')
        ->join('ticket_categories', 'tickets.ticket_category_id','=', 'ticket_categories.id')
        ->join('ticket_issue_types', 'tickets.issue_type_id','=', 'ticket_issue_types.id')
        ->select('tickets.*','users.name as created', 'equipments.title as equipment', 'ticket_categories.name as ticket_categories', 'ticket_issue_types.name as issue')
        ->where('site_id', $request->id)
        //->where('tickets.created_at','>=',Carbon::now()->subdays(7)) 
        ->where(DB::raw('DATE(tickets.created_at)'),'>=',$last_sunday) 
        ->get();
        $pdf = PDF::loadView('tickets/pdf', $data);
  
        return $pdf->download('report.pdf');
        
       
    }
        if($request->datefilter=='Monthly'){
        $data=DB::table('tickets')
        ->select('tickets.*','clients.image as client_image','clients.name as client_name')
        ->join('users', 'tickets.created_by','=', 'users.id')
        ->join('equipments', 'tickets.equipment_id','=', 'equipments.id')
        ->join('sites', 'tickets.site_id','=', 'sites.id')
        ->join('clients', 'clients.id','=', 'sites.client_id')
        ->join('ticket_categories', 'tickets.ticket_category_id','=', 'ticket_categories.id')
        ->join('ticket_issue_types', 'tickets.issue_type_id','=', 'ticket_issue_types.id')
        ->select( 'tickets.*','users.name as created', 'equipments.title as equipment', 'ticket_categories.name as ticket_categories', 'ticket_issue_types.name as issue')
        ->where('site_id', $request->id)
        //->where('tickets.created_at','>=',Carbon::now()->subdays(30)) 
        ->where(DB::raw('DATE(tickets.created_at)'),'>=',date('Y-m-01')) 
        ->get();
        }
         
        return  $data;
       
       
    }
        

}



