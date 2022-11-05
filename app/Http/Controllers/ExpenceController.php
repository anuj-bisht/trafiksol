<?php
    
namespace App\Http\Controllers;


use App\ExpenceDpr;
use App\Uom;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use DB;

class ExpenceController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
          //$this->middleware('permission:activity_category-list', ['only' => ['index','show']]);
         //$this->middleware('permission:activity-list|activity-create|activity-edit|activity-delete', ['only' => ['index','show']]);
         //$this->middleware('permission:activity-create', ['only' => ['create','store']]);
         //$this->middleware('permission:activity-edit', ['only' => ['edit','update']]);
         //$this->middleware('permission:activity-delete', ['only' => ['destroy']]);
    }
    
    public function dprexpence(Request $request){
      try{
                            
          $params = [];
          //$data = ActivityDpr::getAllDprActivity($params);                
          return view('expences.dprexpence',[]);
          
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      } 
    }

    public function dprshow(Request $request,ExpenceDpr $expence_dpr){
      try{             
          $data = new \StdClass();               
          $params = [];
          $expence_dpr = ExpenceDpr::find($request->id);
          if(isset($expence_dpr->id)){
            $data = ExpenceDpr::getDprExpenceById($request->id);       
            
          }
          //echo '<pre>';print_r($activity_dpr->id); die;          
          //$data = ActivityDpr::getAllDprActivity($params);                
          return view('expences.dprshow',compact('data'));
          
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
                   
          $obj = ExpenceDpr::getAllDprExpence($cond);   

          $flag = 0;

          if ($request->search['value'] != "") {            
            $obj = $obj->where('expence_dprs.status','LIKE',"%".$search."%");
            $obj = $obj->orWhere('users.name','LIKE',"%".$search."%");            
            $obj = $obj->orWhere('sites.name','LIKE',"%".$search."%");
            $obj = $obj->orWhere('expence_dprs.created_at','LIKE',"%".$search."%");
          }

          if(isset($request->date_from) && isset($request->date_to)){
            $flag = 1;
            $obj = $obj->whereBetween(DB::raw('DATE(expence_dprs.created_at)'),[$request->date_from,$request->date_to]);                  
          }
          
          if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
              $flag = 1;  
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('users.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('sites.name',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('expence_dprs.amount',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('expence_dprs.rate',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('expence_dprs.status',$sort);
          }
          if(isset($request->order[0]['column']) && $request->order[0]['column']==6){
              $flag = 1;
              $sort = $request->order[0]['dir'];
              $obj = $obj->orderBy('expence_dprs.created_at',$sort);
          }

          if(!$flag){
            $obj = $obj->orderBy('expence_dprs.id','desc');
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

    public function approveRejectExpence(Request $request){
      try{             
          $response = ["status"=>0,"message"=>"","data"=>[]];

          if(isset($request->type)){
            $status = ($request->type=='1') ? 'approved' : 'rejected'; 
            $result = ExpenceDpr::whereIn('id',$request->ids)->where('status','submitted')->update(['status'=>$status]);  

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
