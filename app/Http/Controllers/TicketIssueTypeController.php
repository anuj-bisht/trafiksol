<?php
    
namespace App\Http\Controllers;
    
use App\TicketIssueType;
use App\Site;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class TicketIssueTypeController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         //$this->middleware('permission:phase-list|phase-create|phase-edit|phase-delete', ['only' => ['index','show']]);
         //$this->middleware('permission:phase-create', ['only' => ['create','store']]);
         ///$this->middleware('permission:phase-edit', ['only' => ['edit','update']]);
         ///$this->middleware('permission:phase-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tit = TicketIssueType::latest()->paginate(5);
        return view('ticket_issue_types.index',compact('tit'))
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
            
            //echo '<pre>'; print_r($users); die; categoryFilter
            $obj = TicketIssueType::whereRaw('1 = 1');
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('name','LIKE',"%".$search."%");
            } 
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('name',$sort);
            }
            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
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
        return view('ticket_issue_types.create',[]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',                        
        ]);

        TicketIssueType::create($request->all());

        return redirect()->route('ticket_issue_types.index')
                        ->with('success','Ticket Issue Type created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $Client
     * @return \Illuminate\Http\Response
     */
    public function show(TicketIssueType $ticket_issue_type)
    {
        return view('ticket_issue_types.show',compact('ticket_issue_type'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Phase  $Phase
     * @return \Illuminate\Http\Response
     */
    public function edit(TicketIssueType $ticket_issue_type)
    {
        return view('ticket_issue_types.edit',compact('ticket_issue_type'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\  $
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TicketIssueType $ticket_issue_type)
    {
         request()->validate([
            'name' => 'required',                        
        ]);

        $ticket_issue_type->update($request->all());
    
        return redirect()->route('ticket_issue_types.index')
                        ->with('success','Ticket Issue Type updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\  $
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketIssueType $ticket_issue_type)
    {
        $ticket_issue_type->delete();
    
        return redirect()->route('ticket_issue_types.index')
                        ->with('success','Ticket issue types deleted successfully');
    }
    
}
