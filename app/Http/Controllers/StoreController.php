<?php
    
namespace App\Http\Controllers;
    
use App\Store;
use App\Brand;
use App\Equipment;
use App\Site;
use App\Models;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use DB;
    
class StoreController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:store-list|store-create|store-edit|store-delete', ['only' => ['index','show']]);
         $this->middleware('permission:store-create', ['only' => ['create','store']]);
         $this->middleware('permission:store-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:store-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $sites = Site::getSiteList();
        $brands = Brand::all();
        $models = Models::getAllModels();
        
        //$equipments = Equipment::latest()->paginate(5);

        
        return view('stores.index',['sites'=>$sites,'brands'=>$brands,'models'=>$models]);

            
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
            $site_id = isset($request->site_id) ? $request->site_id:0;
            $brand_id = isset($request->brand) ? $request->brand:0;
            $models_id = isset($request->models) ? $request->models:0;
            $store_type = isset($request->store_type) ? $request->store_type:0;
            
            
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
            

            $obj = DB::table('stores')
            ->select('stores.*','sites.name as site_name','brands.name as brand_name','models.model as model_name')
            ->join('sites', 'sites.id', '=', 'stores.site_id')
            ->join('brands', 'brands.id', '=', 'stores.brand_id')
            ->join('models', 'models.id', '=', 'stores.model_id')
            ->where('stores.id', '<>', 0);
                    
            
            if ($site_id) {            
                $obj = $obj->where('sites.id',$site_id);
            }
            if ($brand_id) {            
                $obj = $obj->where('brands.id',$brand_id);
            }
            if ($models_id) {            
                $obj = $obj->where('models.id',$models_id);
            }
            if ($store_type) {            
                $obj = $obj->where('stores.store_type',$store_type);
            }

            


            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('brands.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('models.model',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('stores.quantity',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('stores.item_name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('stores.item_code',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==6){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('stores.docket_no',$sort);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function returnitem()
    {
						
        return view('stores.returnitem',[]);
            
    }
    
    public function ajaxDataReturnItem(Request $request){
    
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
                        

            $obj = DB::table('stores')
            ->select('stores.*','sites.name as site_name','brands.name as brand_name','models.model as model_name')
            ->join('sites', 'sites.id', '=', 'stores.site_id')
            ->join('brands', 'brands.id', '=', 'stores.brand_id')
            ->join('models', 'models.id', '=', 'stores.model_id')
            ->where('stores.id', '<>', 0);
                    
                       
            
   
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
        $sites = Site::getSiteList();
        
        return view('stores.create',compact('sites','brand'));
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
            'quantity' => 'required',           
            'brand_id' => 'required',           
            'model_id' => 'required',           
            'site_id' => 'required',      
            'equipment_id'=>'required',     
        ]);
       
       // print_r($request->all()); die;
        $obj = new Store();
        $obj->item_name = $request->item_name;
        $obj->item_code = $request->item_code;
        $obj->docket_no = $request->docket_no;
        $obj->site_id = $request->site_id;
        $obj->brand_id = $request->brand_id;
        $obj->model_id = $request->model_id;
        $obj->equipment_id = $request->equipment_id;
        $obj->quantity = $request->quantity;
        $obj->store_type = isset($request->store_type) ? $request->store_type : "Store";
        $obj->save();
        
    
        return redirect()->route('stores.index')
                        ->with('success','Stores created successfully.');
    }
    
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {      
        
        $sites = Site::getSiteList();
        $brands = Brand::getDDBrandListByChild();
        $models = Models::getModelListByBrandId($store->brand_id);        
        $brand = Brand::getDDBrandListByPatent();
        
        $selected_main_brand = Brand::where('id',$store->brand_id)->first();
        
        $selected_main_brand_id = $selected_main_brand->parent_id; 
                

        return view('stores.edit',compact('store',
        'brand',
        'brands',
        'models',        
        'selected_main_brand_id',
        'equipment_sla_list'
        ));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        request()->validate([
            'quantity' => 'required',           
            'brand_id' => 'required',           
            'model_id' => 'required',           
            'site_id' => 'required',           
        ]);
        
    
        $store->update($request->all());
    
        return redirect()->route('stores.index')
                        ->with('success','Store updated successfully');
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
    public function destroy(Store $store)
    {
        $store->delete();
    
        return redirect()->route('stores.index')
                        ->with('success','Store deleted successfully');
    }

    

        
}
