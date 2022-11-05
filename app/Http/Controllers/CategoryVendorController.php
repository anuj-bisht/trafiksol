<?php
    
namespace App\Http\Controllers;
    
use App\CategoryVendor;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use PDF;
    
class CategoryVendorController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
          //$this->middleware('permission:category_expence-list', ['only' => ['index','show']]);
         //$this->middleware('permission:category-expence-list|category-expence-create|category-expence-edit|category-expence-delete', ['only' => ['index','show']]);
         //$this->middleware('permission:category-expence-create', ['only' => ['create','store']]);
         //$this->middleware('permission:category-expence-edit', ['only' => ['edit','update']]);
         //$this->middleware('permission:category-expence-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $categories = CategoryVendor::latest()->where('parent_id',0)->get();
        return view('category_vendors.index',compact('categories'));

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
            $obj = CategoryVendor::whereRaw('1 = 1');
                    
            
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
      
        $category = CategoryVendor::getVendorCategoryList();
        //print_r($category); die;
                
        return view('category_vendors.create',compact('category'));
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
            'name' => 'required|unique:category_vendors,name',            
        ]);
       
    
        CategoryVendor::create($request->all());
    
        return redirect()->route('category_vendors.index')
                        ->with('success','Vendor Category created successfully.');
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\category_vendors  $category_vendors
     * @return \Illuminate\Http\Response
     */
    public function show(CategoryVendor $category_vendor)
    {
        return view('category_vendors.show',compact('category_vendor'));
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CategoryVendor  $category_vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryVendor $category_vendor)
    {      
        $category = CategoryVendor::getVendorCategoryList();

        return view('category_vendors.edit',compact('category','category_vendor'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CategoryVendor  $getVendorCategoryList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoryVendor $category_vendor)
    {
        
         request()->validate([
            'name' => 'required|unique:category_vendors,name,'.$category_vendor->id
        ]);

                
        $category_vendor->name = $request->name;        
    
        $category_vendor->update();
    
        return redirect()->route('category_vendors.index')
                        ->with('success','Category vendor updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryVendor  $CategoryVendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoryVendor $category_vendor)
    {
      echo 'dddd'; die;
        $category_vendor->delete();
    
        return redirect()->route('category_vendors.index')
                        ->with('success','Category Vendor deleted successfully');
    }

        
}
