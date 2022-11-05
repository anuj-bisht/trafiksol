<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Traits\Common;
use DB;




use Illuminate\Http\Request;

class HomeController extends Controller
{
    use Common;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
   public function index()
    {
        // $ticket=DB::select(DB::raw('select count(*)as total_tickets, id from tickets'));
        // echo dd($ticket);
        // die();
        // $total_tickets='';
        $dat='';
        $req_for_close='';
       

       $ticket_pauses=DB::table('ticket_pauses')->count();
       $data['ticket_pause']=$ticket_pauses;

       

        $ticket=DB::select(DB::raw('select count(*)as total_status,status from tickets group by status'));
        $close=DB::select(DB::raw('select count(*)as total_status,status from tickets where status="close" and close_time > 0.0 group by status'));
        //$overdueopen=DB::table('tickets')->where('status', 'open')->where('close_time' , '<' , -0.-0 )->count();
        $overdueopen = DB::table('tickets')->where(['status'=>'open'])->get();
        $count = 0;
        for($i=0;$i<count($overdueopen);$i++)
        {
            
            $time = $this->DateDiff($overdueopen[$i]->sla_start,$overdueopen[$i]->sla_end);
            //print_r($overdueopen);
            //echo "<br/>";
            $time = explode(":",$time);
            
          if($time[0] < 0)
            {
                $count++;
            }
        }
        
        //print($count);

        $overdueopen = $count;
        $requestclosure=DB::table('ticket_close_requests')->where('is_approved', 'submitted')->count();


        $correcttime=DB::table('tickets')->where('close_time' , '>' , 0.0 )->count();
        $overcorrecttime=DB::table('tickets')->where('close_time' , '<' , 0.0 )->count();
        $data['closeticketincorrecttime']= $correcttime;
	$data['overdueclosedtickets']= $overcorrecttime;
        $data['overdueopentickets']= $overdueopen;
	$data['closurerequest']= $requestclosure;

            
        $ticket_request_is_approved=DB::select(DB::raw('select count(*)as total_request, is_approved from ticket_close_requests group by is_approved'));
    //    echo dd($ticket_request_is_approved);
    //    die;
        //foreach($close as $list){
        // dd($ticket);
        //if($list->status=="close"){
            
            //$dat.="['".$list->status."' , ".$list->total_status."],";
        //}
        
    //}
    
    foreach($ticket_request_is_approved as $is_approved){
        $req_for_close.="['".$is_approved->is_approved."', ".$is_approved->total_request."],";
    }
    $data['ticket_request']=rtrim($req_for_close, ",");
       
        foreach($ticket as $list){
            // dd($ticket);
            if($list->status=="open"){
            $dat.="['Currently".' '.$list->status.' '."tickets' , ".$list->total_status."],";}
            // else if($list->status=="close"){
                
            //     $dat.="['".$list->status."' , ".$list->total_status."],";
            // }
            
        }
        $data['ticket_status']=rtrim($dat, ",");
        $data['sites'] = DB::table('sites')->get();
        // dd($data);

        return view('home',$data);
    }
    public function getTodayTickets()
    {
        $result = DB::table('tickets');
        $result = $result->where(DB::raw('DATE(created_at)'),date('Y-m-d'))->get();
        
        $response['status'] = 200;
        $response['data'] = $result;
        $response["recordsFiltered"] = count($result);
            $response["recordsTotal"] = count($result);
            //response["draw"] = draw;
            $response["success"] = 1;
        return response()->json($response);

    }
    public function getTotalDataCount(Request $request)
    {
        // print_r($request->all());
         $res = DB::table('tickets');
        if($request->sites != 'All')
        {
           $res =  $res->where('site_id',$request->sites);
        }
        if(isset($request->fromDate) && isset($request->toDate))
        {

          $res = $res->whereBetween('created_at', [$request->fromDate, $request->toDate]);
        }
        // else{
        //     $res = $res->whereBetween('created_at', [date('Y-m-d'), date('Y-m-d')]);
        // }
        
        $data = $res->get();

        for($i=0;$i<count($data);$i++)
        {
            $data[$i]->rem_hr  = $this->DateDiff($data[$i]->sla_start,$data[$i]->sla_end);
            $paused_ticket = DB::table("ticket_pauses")->where(["ticket_id"=>$data[$i]->id,"is_approved"=>"Y"])->get();
            $hardwareReq = DB::table("hardware_requests")->where(["ticket_id"=>$data[$i]->id,"status"=>"open"])->get();
            if(!$paused_ticket->isEmpty())
            {
                $data[$i]->ticket_pauses  = "Yes";
            }
            else{
                $data[$i]->ticket_pauses  = "No";
            }
            if(!$hardwareReq->isEmpty())
            {
                $data[$i]->hardwareReq  = "Yes";
            }
            else{
                $data[$i]->hardwareReq  = "No";
            }


        }
        $response['todayTickets'] = DB::table('tickets')->where('created_at', '>=', date('Y-m-d').' 00:00:00')->count();
        
        $response['tickets'] = $data;
        $response['status'] = 200;
        return response()->json($response);

    }
}
