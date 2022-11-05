<?php
    
namespace App\Http\Controllers;
    
use App\Uom;
use Illuminate\Http\Request;
    
class UomController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:uom-list|uom-create|uom-edit|uom-delete', ['only' => ['index','show']]);
         $this->middleware('permission:uom-create', ['only' => ['create','store']]);
         $this->middleware('permission:uom-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:uom-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $uoms = Uom::latest()->paginate(5);
        return view('uoms.index',compact('uoms'));
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

            $obj = Uom::where('id','<>',0);
            
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
        return view('uoms.create',[]);
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
            'name' => 'required|unique:uoms,name',
        ]);

        Uom::create($request->all());
    
        return redirect()->route('uoms.index')
                        ->with('success','Uom created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Uom  $Uom
     * @return \Illuminate\Http\Response
     */
    public function show(Uom $uom)
    {
        return view('uoms.show',compact('uom'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Uom  $Uom
     * @return \Illuminate\Http\Response
     */
    public function edit(Uom $uom)
    {        
        return view('uoms.edit',['uom'=>$uom]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Uom  $Uom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Uom $uom)
    {
         request()->validate([
            'name' => 'required|unique:uoms,name,'.$uom->id,
        ]);

        
        $uom->name = $request->name;
        
        $uom->update();
    
        return redirect()->route('uoms.index')
                        ->with('success','Uom updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Uom  $Uom
     * @return \Illuminate\Http\Response
     */
    public function destroy(Uom $uom)
    {
        $uom->delete();
    
        return redirect()->route('uoms.index')
                        ->with('success','Uom deleted successfully');
    }
}
