<?php
    
namespace App\Http\Controllers;
    
use App\ActivityCategory;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class ActivityCategoryController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         parent::__construct();
          //$this->middleware('permission:activity_category-list', ['only' => ['index','show']]);
         $this->middleware('permission:activity-category-list|activity-category-create|activity-category-edit|activity-category-delete', ['only' => ['index','show']]);
         $this->middleware('permission:activity-category-create', ['only' => ['create','store']]);
         $this->middleware('permission:activity-category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:activity-category-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $categories = ActivityCategory::latest()->where('parent_id',0)->get();
        return view('activity_categories.index',compact('categories'));

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
            $obj = ActivityCategory::whereRaw('1 = 1');
                    
            
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
      
        $category = ActivityCategory::getActivityList();
                
        return view('activity_categories.create',compact('category'));
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
            'name' => 'required|unique:activity_categories,name',            
        ]);
       
    
        ActivityCategory::create($request->all());
    
        return redirect()->route('activity_categories.index')
                        ->with('success','Activity Category created successfully.');
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\activity_category  $activity_category
     * @return \Illuminate\Http\Response
     */
    public function show(ActivityCategory $activity_category)
    {
        return view('activity_categories.show',compact('activity_category'));
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\activity_category  $activity_category
     * @return \Illuminate\Http\Response
     */
    public function edit(ActivityCategory $activity_category)
    {      
        $category = ActivityCategory::getActivityList();

        return view('activity_categories.edit',compact('category','activity_category'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\activity_category  $activity_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ActivityCategory $activity_category)
    {
        
         request()->validate([
            'name' => 'required|unique:activity_categories,name,'.$activity_category->id
        ]);

                
        $activity_category->name = $request->name;        
        $activity_category->parent_id = $request->parent_id;        
    
        $activity_category->update();
    
        return redirect()->route('activity_categories.index')
                        ->with('success','Activity category updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ActivityCategory  $ActivityCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActivityCategory $activity_category)
    {
        $activity_category->delete();
    
        return redirect()->route('activity_categories.index')
                        ->with('success','Activity_category deleted successfully');
    }

    
}
