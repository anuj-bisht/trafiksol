<?php
    
namespace App\Http\Controllers;
    
use App\ActivityCategory;
use App\Activity;
use App\ActivityDpr;
use App\ActivityDprTomorrow;
use App\Uom;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use DB;

class ActivityController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
          //$this->middleware('permission:activity_category-list', ['only' => ['index','show']]);
         $this->middleware('permission:activity-list|activity-create|activity-edit|activity-delete', ['only' => ['index','show']]);
         $this->middleware('permission:activity-create', ['only' => ['create','store']]);
         $this->middleware('permission:activity-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:activity-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$data = Activity::whereRaw('1 = 1')->get();      
        //echo '<pre>'; print_r($data[0]->activity_cateory->name); die;
        return view('activities.index',[]);

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
            
            
            //$obj = Activity::whereRaw('1 = 1');

             $obj = DB::table('activities')
            ->select('activities.*', 'uoms.name as uom_name','activity_categories.name as category_name')
            ->join('uoms', 'uoms.id', '=', 'activities.uom_id')
            ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id');
      
            $flag = 0;
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('activity_categories.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $flag = 1;
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('activities.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('uoms.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('activities.status',$sort);
            }

            if(!$flag){
              $obj = $obj->orderBy('activities.id','desc');
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
        $uomList = Uom::getUOMList();
                
        return view('activities.create',compact('category','uomList'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      if(isset($request->activity_category_id) && $request->activity_category_id==0){
        $request->merge(
          [
            'activity_category_id' => $request->main_category
          ]
        );
      }

        request()->validate([
            'name' => 'required',            
            'activity_category_id' => 'required',            
            'uom_id' => 'required',            
        ]);
       
        // print_r($request->all()); die; 

        Activity::create($request->all());
    
        return redirect()->route('activities.index')
                        ->with('success','Activity created successfully.');
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\activity $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return view('activities.show',[]);
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity,Request $request)
    {      
               $category = ActivityCategory::getActivityList();        
        $cat_row = ActivityCategory::getCategoryId($activity->activity_category_id);

        $child_cat = ActivityCategory::getCategoryByParent($cat_row->parent_id);

        if($cat_row->parent_id==0){
          $selected_category = $cat_row->id;
          $selected_sub_category = 0;
        }else{
          $selected_category = $cat_row->parent_id;
          $selected_sub_category = $cat_row->id;
        }
        

        $uomList = Uom::getUOMList();

        return view('activities.edit',compact('category',
        'activity','uomList','cat_row','child_cat','selected_category',
        'selected_sub_category'
      ));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        
        request()->validate([              
          'name' => 'required', 
          'activity_category_id' => 'required',            
          'uom_id' => 'required',            
        ]);

        $activity->update($request->all());
    
        return redirect()->route('activities.index')
                        ->with('success','Activity updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Activity  $Activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();
    
        return redirect()->route('activities.index')
                        ->with('success','Activity deleted successfully');
    }

    public function ajaxGetActivitySubCategory(Request $request){

      try{
          $response = ["status"=>0,"message"=>"","data"=>[]];
          $id = $request->id;

          if($id=="" || $id==0){
              $response['message']="No record found!!";
              return response()->json($response);
              //echo json_encode($response); die;
          }
        
          $data = ActivityCategory::getCategoryByParent($id);        

          $response['status'] = 1;
          $response['data'] = $data;
          
          return response()->json($response);
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      } 
      
    }

    public function getActivityByCategoryId(Request $request){
      try{
          $response = ["status"=>0,"message"=>"","data"=>[]];
          $id = $request->category_id;

          if($id=="" || $id==0){
              $response['message']="No record found!!";
              return response()->json($response);
              //echo json_encode($response); die;
          }
        
          $data = Activity::getActivityByCategoryId($id);        

          $response['status'] = 1;
          $response['data'] = $data;
          
          return response()->json($response);
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      } 
    }

    public function dpractivity(Request $request){
      try{
                            
          $params = [];
          //$data = ActivityDpr::getAllDprActivity($params);                
          return view('activities.dpractivity',[]);
          
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      } 
    }

    public function dprshow(Request $request,ActivityDpr $activity_dpr){
      try{             
          $data = new \StdClass();               
          $params = [];
          $activity_dpr = ActivityDpr::find($request->id);
          if(isset($activity_dpr->id)){
            $data = ActivityDpr::getDprActivityById($request->id);       
            
          }
          //echo '<pre>';print_r($activity_dpr->id); die;          
          //$data = ActivityDpr::getAllDprActivity($params);                
          return view('activities.dprshow',compact('data'));
          
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
                   
          $obj = ActivityDpr::getAllDprActivity($cond);   

          
          if ($request->search['value'] != "") {            
            $obj = $obj->where('activity_dprs.status','LIKE',"%".$search."%");
            $obj = $obj->orWhere('users.name','LIKE',"%".$search."%");
            $obj = $obj->orWhere('activities.name','LIKE',"%".$search."%");
            $obj = $obj->orWhere('sites.name','LIKE',"%".$search."%");
            $obj = $obj->orWhere('activity_dprs.created_at','LIKE',"%".$search."%");
          }

          if(isset($request->date_from) && isset($request->date_to)){
            $obj = $obj->whereBetween(DB::raw('DATE(activity_dprs.created_at)'),[$request->date_from,$request->date_to]);                  
          }

          
          $flag = 0;

          if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('users.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('activities.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('sites.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('uoms.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==6){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('activity_dprs.status',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==7){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('activity_dprs.created_at',$sort);
          }
          
          if(!$flag){
            $obj = $obj->orderBy('activity_dprs.id','desc');
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

    public function activitytomorrow(){
      
      return view('activities.activitytomorrow',[]);
    }

    public function ajaxDprTomorrowData(Request $request){
    
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
                   
          $obj = ActivityDprTomorrow::getAllDprTomorrowActivity($cond);   
          $flag = 0;

          if ($request->search['value'] != "") {            
            $obj = $obj->where('activity_dpr_tomorrows.status','LIKE',"%".$search."%");
            $obj = $obj->orWhere('users.name','LIKE',"%".$search."%");
            $obj = $obj->orWhere('activities.name','LIKE',"%".$search."%");
            $obj = $obj->orWhere('sites.name','LIKE',"%".$search."%");
          }

          if(isset($request->date_from) && isset($request->date_to)){
            
            $obj = $obj->whereBetween(DB::raw('DATE(activity_dpr_tomorrows.created_at)'),[$request->date_from,$request->date_to]);                  
          }

          if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('users.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('activities.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('sites.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('uoms.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('activity_dpr_tomorrows.created_at',$sort);
          }
          
          if(!$flag){
            $obj = $obj->orderBy('activity_dpr_tomorrows.id','desc');
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

    public function tdprs(Request $request){
      try{          
        
          $data = new \StdClass();               
          $params = [];
          $activity_dpr_tomorrow = ActivityDprTomorrow::find($request->id);
          if(isset($activity_dpr_tomorrow->id)){
            $data = ActivityDprTomorrow::getDprActivityById($request->id);       
            
          }
          //echo '<pre>';print_r($activity_dpr->id); die;          
          //$data = ActivityDpr::getAllDprActivity($params);                
          return view('activities.tdprs',compact('data'));
          
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      }
    }

    public function approveRejectActivity(Request $request){
      try{             
          $response = ["status"=>0,"message"=>"","data"=>[]];

          if(isset($request->type)){
            $status = ($request->type=='1') ? 'approved' : 'rejected'; 
            $result = ActivityDpr::whereIn('id',$request->ids)->where('status','submitted')->update(['status'=>$status]);  

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
