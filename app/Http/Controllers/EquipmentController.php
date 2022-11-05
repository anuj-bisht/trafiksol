<?php
    
namespace App\Http\Controllers;
    
use App\Models;
use App\EquipmentSla;
use App\Brand;
use App\Equipment;
use App\Project;
use App\Vendor;
use App\Uom;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use DB;
    
class EquipmentController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:equipment-list|equipment-create|equipment-edit|equipment-delete', ['only' => ['index','show']]);
         $this->middleware('permission:equipment-create', ['only' => ['create','store']]);
         $this->middleware('permission:equipment-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:equipment-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //$projects = Project::getAllProject();
        $vendors = Vendor::getVendorDD();
        $models = Models::getAllModels();
        $brands = Brand::where('parent_id',0)->get();   
        //print_r($brands); die;
        $equipments = Equipment::latest()->paginate(5);

        
        return view('equipments.index',compact('equipments','brands','models'))
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
            $brand_id = isset($request->brand) ? $request->brand:0;
            $models_id = isset($request->models) ? $request->models:0;
            
            // $obj = Equipment::with(['Project'=>function($q) use ($project_id) {                
            //     if($project_id){
            //         $q->where('id', '=', $project_id);
            //     }
            // },'brand'=>function($b) use ($brand_id) {
            //     if($brand_id){
            //         $b->where('id', '=', $brand_id);
            //     }
            // },'models'=>function($m) use ($models_id){
            //     if($models_id){
            //         $m->where('id', '=', $models_id);
            //     }
            // }])->whereRaw('1 = 1');
            

            $obj = DB::table('equipments')
            ->select('equipments.*','brands.name as brand_name','models.model as model_name')            
            ->join('brands', 'brands.id', '=', 'equipments.brand_id')
            ->join('models', 'models.id', '=', 'equipments.model_id')
            ->where('equipments.id', '<>', 0);
                    
                        
            if ($brand_id) {            
                $obj = $obj->where('brands.id',$brand_id);
            }
            if ($models_id) {            
                $obj = $obj->where('models.id',$models_id);
            }

            if ($request->search['value'] != "") {            
              $obj = $obj->where('title','LIKE',"%".$search."%");
            } 

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('equipments.title',$sort);
            }


            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('brands.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('models.model',$sort);
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
        $brand = Brand::getDDBrandListByPatent();    
        $vendors = Vendor::getVendorDD();    
        //$equipment_sla_list = EquipmentSla::getSlaByTypeList('equipment');
        $uom = Uom::getUOMList();
        return view('equipments.create',compact('brand','uom','vendors'));
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
            'title' => 'required',           
        ]);
       
    
        Equipment::create($request->all());
    
        return redirect()->route('equipments.index')
                        ->with('success','Equipment created successfully.');
    }
    
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function edit(Equipment $equipment)
    {      
        //echo "<pre>"; print_r($equipment->project->stretch_from); die;
        //$c_from = $equipment->project->stretch_from;
        //$c_to = $equipment->project->stretch_to;
       // $equipment_sla_list = EquipmentSla::getSlaByTypeList('equipment');
       // $projects = Project::getProjectList();
        $brands = Brand::getDDBrandListByChild();
        $models = Models::getModelListByBrandId($equipment->brand_id);
        $uom = Uom::getUOMList();
        $brand = Brand::getDDBrandListByPatent();
        $vendors = Vendor::getVendorDD();
        
        $selected_main_brand = Brand::select('parent_id','vendor_id')->where('id',$equipment->brand_id)->first();
        
        $selected_main_brand_id = $selected_main_brand->parent_id; 
        $selected_vendor_id = $selected_main_brand->vendor_id; 
        
        //$chainage = $this->getChainage($c_from,$c_to);

        return view('equipments.edit',compact('equipment',
        'brand',
        'brands',
        'models',        
        'uom',        
        'selected_main_brand_id','vendors','selected_vendor_id'        
        ));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Equipment $equipment)
    {
         request()->validate([
            'title' => 'required',
        ]);

        
        // $equipment->title = $request->title;        
        // $equipment->project_id = $request->project_id;        
        // $equipment->title = $request->title;        
        // $equipment->title = $request->title;        
        // $equipment->title = $request->title;        
        
    
        $equipment->update($request->all());
    
        return redirect()->route('equipments.index')
                        ->with('success','Equipment updated successfully');
    }
    
    public function show(Equipment $equipment)
    {
     
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
    
        return redirect()->route('equipments.index')
                        ->with('success','Equipment deleted successfully');
    }

    public function getEquipmentByModelId(Request $request){
      try{
          $response = ["status"=>0,"message"=>"","data"=>[]];
          $id = $request->model_id;
          if($id=="" || $id==0){
              $response['message']="No record found!!";
              return response()->json($response);             
          }
        
          $data = Equipment::getEquipmentByModelId($id);          
          $response['status'] = 1;
          $response['data'] = $data;
          return response()->json($response);
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      }
    }

        
}
