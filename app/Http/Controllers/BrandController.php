<?php
    
namespace App\Http\Controllers;
    
use App\Brand;
use App\Vendor;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class BrandController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:brand-list|brand-create|brand-edit|brand-delete', ['only' => ['index','show']]);
         $this->middleware('permission:brand-create', ['only' => ['create','store']]);
         $this->middleware('permission:brand-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:brand-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $brands = Brand::where('parent_id',0)->get();
        $vendors = Vendor::getVendors();
        return view('brands.index',compact('brands','vendors'));

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
            $obj = Brand::select('brands.*','vendors.name as vendor_name')            
            ->join('vendors','vendors.id','=','brands.vendor_id')
            ->where('brands.parent_id',0);
            

            if (isset($request->vendor) && $request->vendor != "") {            
              $obj = $obj->where('vendors.id',$request->vendor);
            } 


            if ($request->search['value'] != "") {            
              $obj = $obj->where('brands.name','LIKE',"%".$search."%");
            } 

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('brands.name',$sort);
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
        //$mainBrand = Brand::getBrandListByPatent();

        $vendors = Vendor::getVendors();
        
        return view('brands.create',compact('vendors'));
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
            'name' => 'required|unique:brands,name',   
            'vendor_id'=>'required',         
        ]);
       
    
        Brand::create($request->all());
    
        return redirect()->route('brands.index')
                        ->with('success','Brand created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Brand  $Brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        return view('brands.show',compact('brand'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Brand  $Brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {      
        //$mainBrand = Brand::getBrandListByPatent();

        $vendors = Vendor::getVendors();

        return view('brands.edit',compact('brand','vendors'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Brand  $Brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
         request()->validate([
            'name' => 'required|unique:brands,name,'.$brand->id,
            'vendor_id'=>'required',
        ]);

        
        $brand->name = $request->name;        
        $brand->vendor_id = $request->vendor_id;        
    
        $brand->update();
    
        return redirect()->route('brands.index')
                        ->with('success','Brand updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Brand  $Brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
    
        $brand->delete();
    
        return redirect()->route('brands.index')
                        ->with('success','Brand deleted successfully');
    }

    public function ajaxGetChildBrand(Request $request){
      $id = $request->id;
      $result = Brand::getBrandById($id);

      $this->ajaxResponse = ['success'=>true,'msg'=>"","data"=>$result];
      return response()->json($this->ajaxResponse);

    }

    public function ajaxGetBrandByVendor(Request $request){
      $id = $request->id;
      $result = Brand::ajaxGetBrandByVendor($id);

      $this->ajaxResponse = ['success'=>true,'msg'=>"","data"=>$result];
      return response()->json($this->ajaxResponse);
    }
}
