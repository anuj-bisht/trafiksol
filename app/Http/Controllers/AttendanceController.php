<?php
    
namespace App\Http\Controllers;
    
use App\Attendance;
use App\ManpowerAttendance;
use App\LoginHistory;
use App\TypeUser;
use Illuminate\Http\Request;
use DB;
    
class AttendanceController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         //$this->middleware('permission:a-list|project-create|project-edit|project-delete', ['only' => ['index','show']]);         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $attendances = LoginHistory::latest()->paginate(5);
        return view('attendances.index',compact('attendances'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\activity $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        return view('attendances.show',[]);
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
            $obj = LoginHistory::with(['User'])
            ->select('login_history.*','users.name')
            ->join('users','users.id','=','login_history.user_id')
            ->whereRaw('1 = 1');
                    
                        
            if ($request->search['value'] != "") {            
              $obj = $obj->where('mark_datetime','LIKE',"%".$search."%");
              $obj = $obj->orWhere('entry_type','LIKE',"%".$search."%");
              $obj = $obj->orWhere('name','LIKE',"%".$search."%");
            }

            if(isset($request->date_from) && isset($request->date_to)){
              $obj = $obj->whereBetween(DB::raw('DATE(login_history.mark_datetime)'),[$request->date_from,$request->date_to]);                  
            }
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('users.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('login_history.mark_datetime',$sort);
            }


            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('login_history.ostype',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('login_history.entry_type',$sort);
            }

            //$obj = $obj->orderBy('created_at','desc');

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


    public function manpower(){
        
        $usertypes = TypeUser::where('id','<>',0)->get();
        return view('attendances.manpower',compact('usertypes'));
    }

    public function ajaxManpowerData(Request $request){
    
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
            $obj = ManpowerAttendance::select('manpower_attendances.*',DB::raw('DATE_FORMAT(manpower_attendances.created_at,"%Y-%m-%d %H:%i:%s") as created'),'users.name','type_users.name as type_name')
            ->join('users','users.id','=','manpower_attendances.user_id')
            ->join('type_users','type_users.id','=','users.type_user_id');

            if(isset($request->usertype)  && $request->usertype>0){
              $obj = $obj->where('users.type_user_id',$request->usertype);
            }

            if(isset($request->date_from) && isset($request->date_to)){
              $obj = $obj->whereBetween(DB::raw('DATE(manpower_attendances.created_at)'),[$request->date_from,$request->date_to]);                  
            }
            
            
            $obj = $obj->whereRaw('1 = 1');
                    
                        
            if ($request->search['value'] != "") {            
              $obj = $obj->where('manpower_attendances.attendance','LIKE',"%".$search."%");
              $obj = $obj->orWhere('users.name','LIKE',"%".$search."%");
              $obj = $obj->orWhere('manpower_attendances.created_at','LIKE',"%".$search."%");
            }
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('type_users.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('users.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('manpower_attendances.attendance',$sort);
            }


            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('manpower_attendances.created_at',$sort);
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


}
