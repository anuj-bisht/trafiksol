<?php
    
namespace App\Http\Controllers;
    
use App\TicketCategory;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class TicketCategoryController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
          //$this->middleware('permission:ticket_category-list', ['only' => ['index','show']]);
         //$this->middleware('permission:ticket-category-list|ticket-category-create|ticket-category-edit|ticket-category-delete', ['only' => ['index','show']]);
         //$this->middleware('permission:ticket-category-create', ['only' => ['create','store']]);
         //$this->middleware('permission:ticket-category-edit', ['only' => ['edit','update']]);
         //$this->middleware('permission:ticket-category-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $categories = TicketCategory::latest()->where('parent_id',0)->get();
        return view('ticket_categories.index',compact('categories'));

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
            $obj = TicketCategory::whereRaw('1 = 1');
                    
            
            if ($request->parent != "") {            
                $obj = $obj->where('parent_id',$request->parent);
            }

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
      
        $category = TicketCategory::getTicketList();
                
        return view('ticket_categories.create',compact('category'));
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
            'name' => 'required|unique:ticket_categories,name',            
        ]);
       
    
        TicketCategory::create($request->all());
    
        return redirect()->route('ticket_categories.index')
                        ->with('success','Ticket Category created successfully.');
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\ticket_category  $ticket_category
     * @return \Illuminate\Http\Response
     */
    public function show(TicketCategory $ticket_category)
    {
        return view('ticket_categories.show',compact('ticket_category'));
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ticket_category  $ticket_category
     * @return \Illuminate\Http\Response
     */
    public function edit(TicketCategory $ticket_category)
    {      
        $category = TicketCategory::getTicketList();
        //print_r($category); die;

        return view('ticket_categories.edit',compact('category','ticket_category'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ticket_category  $ticket_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TicketCategory $ticket_category)
    {
        
         request()->validate([
            'name' => 'required|unique:ticket_categories,name,'.$ticket_category->id
        ]);

                
        $ticket_category->name = $request->name;        
        $ticket_category->parent_id = $request->parent_id;        
    
        $ticket_category->update();
    
        return redirect()->route('ticket_categories.index')
                        ->with('success','Ticket category updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ticketCategory  $ticketCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketCategory $ticket_category)
    {
        $ticket_category->delete();
    
        return redirect()->route('ticket_categories.index')
                        ->with('success','Ticket category deleted successfully');
    }

    
}
