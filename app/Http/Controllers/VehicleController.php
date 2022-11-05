<?php
    
namespace App\Http\Controllers;
    
use App\Vehicle;
use App\TypeVehicle;
use App\VehicleDpr;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use DB;
    
class VehicleController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:vehicle-list|vehicle-create|vehicle-edit|vehicle-delete', ['only' => ['index','show']]);
         $this->middleware('permission:vehicle-create', ['only' => ['create','store']]);
         $this->middleware('permission:vehicle-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:vehicle-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $vehicles = Vehicle::latest()->where('id','<>',0)->get();
        return view('vehicles.index',compact('vehicles'));

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
            $flag = 0;
            //echo '<pre>'; print_r($users); die; categoryFilter
            $obj = Vehicle::whereRaw('1 = 1')
            ->select('vehicles.*','type_vehicles.name as type_name')
            ->join('type_vehicles','type_vehicles.id','=','vehicles.type_vehicle_id')
            ;
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('name','LIKE',"%".$search."%");
            } 

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('type_vehicles.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('vehicles.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('vehicles.vehicle_number',$sort);
            }

            if(!$flag){
              $obj = $obj->orderBy('vehicles.id','desc');
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
        
        $vehicleTypes = TypeVehicle::getVehicleTypeList();
        
        return view('vehicles.create',compact('vehicleTypes'));
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
            'vehicle_number' => 'required|unique:vehicles,vehicle_number',            
        ]);
       
    
        Vehicle::create($request->all());
    
        return redirect()->route('vehicles.index')
                        ->with('success','Vehicle created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Vehicle  $Vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        return view('vehicles.show',compact('vehicle'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vehicle  $Vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {      
        $vehicleTypes = TypeVehicle::getVehicleTypeList();

        return view('vehicles.edit',compact('vehicle','vehicleTypes'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vehicle  $Vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
         request()->validate([
            'vehicle_number' => 'required|unique:vehicles,vehicle_number,'.$vehicle->id
        ]);

    
        $vehicle->update($request->all());
    
        return redirect()->route('vehicles.index')
                        ->with('success','Vehicle updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vehicle  $Vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
    
        return redirect()->route('vehicles.index')
                        ->with('success','Vehicle deleted successfully');
    }

    public function ajaxGetVehicleByType(Request $request){
      try{
          $response = ["status"=>0,"message"=>"","data"=>[]];
          $id = $request->id;
          if($id=="" || $id==0){
              $response['message']="No record found!!";
              return response()->json($response);
              //echo json_encode($response); die;
          }
         
          $data = Vehicle::getVehicleByType($id);          
          $response['status'] = 1;
          $response['data'] = $data;
          return response()->json($response);
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      }
  }



  public function dprvehicle(Request $request){
    try{
                          
        $params = [];
        //$data = ActivityDpr::getAllDprActivity($params);                
        return view('vehicles.dprvehicle',[]);
        
    }catch(Exception $e){
        DB::rollBack();
        abort(500, $e->message());
    } 
  }

  public function dprshow(Request $request,VehicleDpr $vehicle_dpr){
    try{             
        $data = new \StdClass();               
        $params = [];
        $expence_dpr = VehicleDpr::find($request->id);
        if(isset($expence_dpr->id)){
          $data = VehicleDpr::getDprVehicleById($request->id);       
          
        }
        //echo '<pre>';print_r($activity_dpr->id); die;          
        //$data = ActivityDpr::getAllDprActivity($params);                
        return view('vehicles.dprshow',compact('data'));
        
    }catch(Exception $e){
        DB::rollBack();
        abort(500, $e->message());
    }
  }

  public function ajaxDprData(Request $request){
  
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
        $flag = 0;                 
        $obj = VehicleDpr::getAllDprVehicle($cond);   

        if(isset($request->date_from) && isset($request->date_to)){
          $obj = $obj->whereBetween(DB::raw('DATE(vehicle_dprs.created_at)'),[$request->date_from,$request->date_to]);                  
        }

        if ($request->search['value'] != "") {            
          $obj = $obj->where('vehicle_dprs.status','LIKE',"%".$search."%");
          $obj = $obj->orWhere('users.name','LIKE',"%".$search."%");          
          $obj = $obj->orWhere('sites.name','LIKE',"%".$search."%");
          $obj = $obj->orWhere('vehicle_dprs.created_at','LIKE',"%".$search."%");
          $obj = $obj->orWhere('vehicle_dprs.amount','LIKE',"%".$search."%");
          $obj = $obj->orWhere('vehicle_dprs.rate','LIKE',"%".$search."%");
        }

        if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('users.email',$sort);
        }

        if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('sites.name',$sort);
        }
        if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('vehicle_dprs.distance',$sort);
        }
        if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('vehicle_dprs.fuel_filled',$sort);
        }
        if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('uoms.name',$sort);
        }
        if(isset($request->order[0]['column']) && $request->order[0]['column']==6){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('vehicle_dprs.status',$sort);
        }
        if(isset($request->order[0]['column']) && $request->order[0]['column']==7){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('vehicle_dprs.created_at',$sort);
        }
        if(isset($request->order[0]['column']) && $request->order[0]['column']==8){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('vehicle_dprs.amount',$sort);
        }
        if(isset($request->order[0]['column']) && $request->order[0]['column']==9){
            $flag = 1;   
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('vehicle_dprs.rate',$sort);
        }

        if(!$flag){
          $obj = $obj->orderBy('vehicle_dprs.id','desc');
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

  public function approveRejectVehicle(Request $request){
    try{             
        $response = ["status"=>0,"message"=>"","data"=>[]];

        if(isset($request->type)){
          $status = ($request->type=='1') ? 'approved' : 'rejected'; 
          $result = VehicleDpr::whereIn('id',$request->ids)->where('status','submitted')->update(['status'=>$status]);  

          if($result){
            $response['status'] = '1';
            $response['data'] = [];
          }else{              
            $response['message'] = "No record to update";
          }
          
          return response()->json($response);
        }          
        
    }catch(Exception $e){
        DB::rollBack();
        abort(500, $e->message());
    }
  }
    
}
