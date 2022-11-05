<?php
    
namespace App\Http\Controllers;
    
use App\CategoryExpence;
use Illuminate\Http\Request;
use App\Classes\UploadFile;

    
class CategoryExpenceController extends Controller
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
      
        $categories = CategoryExpence::latest()->where('parent_id',0)->get();
        return view('category_expences.index',compact('categories'));

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
            
            $start = (isset($request->start)) ? $request->start : 0;
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];
            
            //echo '<pre>'; print_r($users); die; categoryFilter
            $obj = CategoryExpence::whereRaw('1 = 1');
                    
            
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
      
        $category = CategoryExpence::getExpenceCategoryList();
                
        return view('category_expences.create',compact('category'));
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
            'name' => 'required|unique:category_expences,name',            
        ]);
       
    
        CategoryExpence::create($request->all());
    
        return redirect()->route('category_expences.index')
                        ->with('success','Expence Category created successfully.');
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function show(CategoryExpence $category_expence)
    {
        return view('category_expences.show',compact('category_expence'));
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryExpence $category_expence)
    {      
        $category = CategoryExpence::getExpenceCategoryList();

        return view('category_expences.edit',compact('category','category_expence'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoryExpence $category_expence)
    {
        
         request()->validate([
            'name' => 'required|unique:category_expences,name,'.$category_expence->id
        ]);

                
        $category_expence->name = $request->name;        
    
        $category_expence->update();
    
        return redirect()->route('category_expences.index')
                        ->with('success','Category expence updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryExpence  $CategoryExpence
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoryExpence $category_expence)
    {
        $category_expence->delete();
    
        return redirect()->route('category_expences.index')
                        ->with('success','Category expence deleted successfully');
    }

    
}
