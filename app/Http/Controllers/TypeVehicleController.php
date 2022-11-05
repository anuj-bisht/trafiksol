<?php
    
namespace App\Http\Controllers;
    
use App\Vehicle;
use App\TypeVehicle;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class TypeVehicleController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         //$this->middleware('permission:vehicle-list|vehicle-create|vehicle-edit|vehicle-delete', ['only' => ['index','show']]);
         //$this->middleware('permission:vehicle-create', ['only' => ['create','store']]);
         //$this->middleware('permission:vehicle-edit', ['only' => ['edit','update']]);
         //$this->middleware('permission:vehicle-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $type_vehicles = TypeVehicle::latest()->where('id','<>',0)->get();
        return view('type_vehicles.index',compact('type_vehicles'));

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
            $obj = TypeVehicle::whereRaw('1 = 1');            
            
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
        
        return view('type_vehicles.create',[]);
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
            'name' => 'required|unique:type_vehicles,name',            
        ]);
       
    
        TypeVehicle::create($request->all());
    
        return redirect()->route('type_vehicles.index')
                        ->with('success','Vehicle type created successfully.');
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
    public function edit(TypeVehicle $type_vehicle)
    {      
        return view('type_vehicles.edit',compact('type_vehicle'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vehicle  $Vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeVehicle $type_vehicle)
    {
         request()->validate([
            'name' => 'required|unique:type_vehicles,name,'.$type_vehicle->id
        ]);

    
        $type_vehicle->update($request->all());
    
        return redirect()->route('type_vehicles.index')
                        ->with('success','Vehicle type updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vehicle  $Vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeVehicle $type_vehicle)
    {
        $type_vehicle->delete();
    
        return redirect()->route('vehicles.index')
                        ->with('success','Vehicle type deleted successfully');
    }

    
}
