<?php
    
namespace App\Http\Controllers;
    
use App\EquipmentSla;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class EquipmentSlaController extends Controller
{ 


    public function index()
    {                    
      
        return view('equipment_slas.index',[]);            
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
          
          $obj = EquipmentSla::where('id','<>',0);
                   
         
          if ($request->search['value'] != "") {            
            $obj = $obj->where('title','LIKE',"%".$search."%");
          } 

          if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
            $sort = $request->order[0]['dir'];
            $obj = $obj->orderBy('name',$sort);
          }

          if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('type',$sort);
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
        return view('equipment_slas.create',[]);
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
            'name' => 'required',           
            'hours_allocated' => 'required',     
            'sla_type' => 'required',      
            
        ]);
       
    
        EquipmentSla::create($request->all());
    
        return redirect()->route('equipment_slas.index')
                        ->with('success','Equipment SLA created successfully.');
    }
    
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function edit(EquipmentSla $equipment_sla)
    {      
        
        return view('equipment_slas.edit',['equipment_sla'=>$equipment_sla]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EquipmentSla $equipment_sla)
    {
         request()->validate([
            'name' => 'required',
            'hours_allocated' => 'required',
            'sla_type' => 'required',
        ]);

        $equipment_sla->update($request->all());
    
        return redirect()->route('equipment_slas.index')
                        ->with('success','Equipment Sla updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(EquipmentSla $equipment_sla)
    {
        $equipment_sla->delete();
    
        return redirect()->route('equipment_slas.index')
                        ->with('success','Equipment Sla deleted successfully');
    }

    
}
