<?php
    
namespace App\Http\Controllers;
    
use App\Vendor;
use App\CategoryVendor;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class VendorController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:vendor-list|vendor-create|vendor-edit|vendor-delete', ['only' => ['index','show']]);
         $this->middleware('permission:vendor-create', ['only' => ['create','store']]);
         $this->middleware('permission:vendor-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:vendor-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendors = Vendor::latest()->paginate(5);
        return view('vendors.index',compact('vendors'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
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

            $obj = Vendor::select('vendors.*','category_vendors.name as cvname')
            ->join('category_vendors','vendors.category_vendor_id','category_vendors.id')
            ->where('vendors.id','<>',0);
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('name','LIKE',"%".$search."%");
            } 

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('category_vendors.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('vendors.name',$sort);
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
        $category = CategoryVendor::getCategoryList();

        return view('vendors.create',compact('category'));
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
            'category_vendor_id' => 'required',
            'name' => 'required|unique:vendors,name',            
        ]);

        Vendor::create($request->all());
    
        return redirect()->route('vendors.index')
                        ->with('success','Vendor created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Vendor  $Vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        return view('vendors.show',compact('vendor'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vendor  $Vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        $category = CategoryVendor::getCategoryList();

        return view('vendors.edit',['category'=>$category,'vendor'=>$vendor]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vendor  $Vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
         request()->validate([
            'name' => 'required|unique:vendors,name,'.$vendor->id,
            'category_vendor_id' => 'required',           
        ]);
        
        $vendor->update($request->all());
    
        return redirect()->route('vendors.index')
                        ->with('success','Vendor updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vendor  $Vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
    
        return redirect()->route('vendors.index')
                        ->with('success','Vendor deleted successfully');
    }
}
