<?php
    
namespace App\Http\Controllers;
    
use App\Brand;
use App\Models;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class ModelController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:model-list|model-create|model-edit|model-delete', ['only' => ['index','show']]);
         $this->middleware('permission:model-create', ['only' => ['create','store']]);
         $this->middleware('permission:model-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:model-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Models::with('Brand')->latest()->paginate(5);
        //echo '<pre>';print_r($models[0]); die;
        return view('models.index',compact('models'))
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

            $obj = Models::select('models.*')->with('Brand')
            ->join('brands','brands.id','=','models.brand_id')
            ->where('models.id','<>',0);
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('models.model','LIKE',"%".$search."%");
            } 

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('brands.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('models.model',$sort);
            }

            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('models.build',$sort);
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
        $brand = Brand::getBrandList();
        return view('models.create',compact('brand'));
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
            'model' => 'required|unique:models,model',            
            'build' => 'required',           
        ]);
       
    
        Models::create($request->all());
    
        return redirect()->route('models.index')
                        ->with('success','Model created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Model  $model
     * @return \Illuminate\Http\Response
     */
    public function show(Models $model)
    {
        return view('models.show',compact('model'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model  $model
     * @return \Illuminate\Http\Response
     */
    public function edit(Models $model)
    {      
        $brand = Brand::getBrandList();

        return view('models.edit',compact('model','brand'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model  $model
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Models $model)
    {
         request()->validate([
            'model' => 'required|unique:models,model,'.$model->id,            
            'build' => 'required',
        ]);

        $model->brand_id = $request->brand_id;
        $model->model = $request->model;        
        $model->make = $request->make;
        $model->build = $request->build;
    
        $model->update();
    
        return redirect()->route('models.index')
                        ->with('success','Model updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model  $model
     * @return \Illuminate\Http\Response
     */
    public function destroy(Models $model)
    {
        $model->delete();
    
        return redirect()->route('models.index')
                        ->with('success','Model deleted successfully');
    }

    /**
     * get ajax model list by brand id
     *
     * @param  \App\Model  $model
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetModelByBrand(Request $request){
        $id = $request->id;
        $result = Models::getModelByBrandId($id);

        $this->ajaxResponse = ['success'=>true,'msg'=>"","data"=>$result];
        return response()->json($this->ajaxResponse);
    }
}
