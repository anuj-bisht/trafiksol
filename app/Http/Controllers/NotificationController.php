<?php
    
namespace App\Http\Controllers;
    
use App\Notification;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use App\User;
use App\Client;
use App\TypeUser;

use Spatie\Permission\Models\Role;
use DB;
use App\Http\Controllers\Traits\SendMail;


    
class NotificationController extends Controller
{ 
    use SendMail;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:notification-list|notification-create|notification-edit|notification-delete', ['only' => ['index','show']]);
         $this->middleware('permission:notification-create', ['only' => ['create','store']]);
         $this->middleware('permission:notification-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:notification-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $notifications = Notification::latest()->where('id','<>',0)->get();
        return view('notifications.index',compact('notifications'));

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
            $obj = Notification::select('notifications.*')            
            ->whereRaw('1 = 1');
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('email_message','LIKE',"%".$search."%");
              $obj = $obj->orWhere('mobile_message','LIKE',"%".$search."%");
              $obj = $obj->orWhere('notification_message','LIKE',"%".$search."%");
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
        return view('notifications.create',[]);
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
            'subject' => 'required|unique:notifications,subject',            
        ]);
       
    
        Notification::create($request->all());
    
        return redirect()->route('notifications.index')
                        ->with('success','Notification created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        return view('notifications.show',compact('notification'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Notification $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {              

        $typeUser = Role::all();

    
        return view('notifications.edit',compact('notification','typeUser'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {   

        try{
            request()->validate([
                'subject' => 'required',
                'message' => 'required'
            ]);
    
            DB::beginTransaction();
    
            $notification->subject = $request->subject;        
            $notification->message = $request->message;        
            $notification->mobile_message = $request->mobile_message;              
            $notification->notification_message = $request->notification_message;  
        
            $notification->update();
    
            $data = [];
            //echo $notification->id; print_r($request->users); die;
            // if(isset($request->users)){
            //     foreach($request->users as $k=>$v){
            //         $data[$k]['notification_id'] = $notification->id;   
            //         $data[$k]['user_id'] = $v;
            //     }

            //     DB::table('notification_users')->where('notification_id',$notification->id)->delete();                
            //     DB::table('notification_users')->insert($data); 
            // }
            
            DB::commit();            
            return redirect()->route('notifications.index')
                            ->with('success','Notification updated successfully');
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        } 
         
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
    
        return redirect()->route('notifications.index')
                        ->with('success','Notification deleted successfully');
    }

    public function sendnotification(Request $request)
    {

      //echo 'dd'; die;
        $clients = Client::getClientList();
        $userdata = User::with('roles')->get();
        $roles = \Spatie\Permission\Models\Role::all();
        
        $groupOption = [];
        $optionData = [];
        if($roles->count()){
            foreach($roles as $k=>$v){
                $groupOption[$k] = $v->name;
            }
        }
        if($userdata->count()){
            foreach($userdata as $k=>$v){
                //echo '<pre>';  print_r($v->roles[0]); die;
                if(!in_array($v->roles[0]->name,$groupOption)){
                    $optionData[$v->roles[0]->name] = $v;
                }else{
                    $optionData[$v->roles[0]->name][] = $v;
                }
            }
        }
        
        $subjects = Notification::getSubjectList();

        $data = Notification::orderBy('id','DESC')->paginate(10);
        return view('notifications.sendnotification',compact('data','clients','optionData','subjects'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function ajaxSendNotification(Request $request){
        //print_r($request->all()); die; 
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            if($request->subject==""){
                $response['message']="Subject is empty!!";
                return response()->json($response);
                //echo json_encode($response); die;
            }

            if($request->message==""){
                $response['message']="Message is empty!!";
                return response()->json($response);
            }

            if(!is_array($request->userlist)){
                $response['message']="User list is empty!!";
                return response()->json($response);
            }

            $result = User::select(DB::raw('group_concat(email) as user_email'))->whereIn('id', $request->userlist)->first();
            
            if(isset($result->user_email)){
                $data1['to_email'] = explode(",",$result->user_email);
                $data1['from'] = 'ravindra2806@gmail.com';
                $data1['name'] = $request->name;
                $data1['message1'] = $request->message; 
                
                $data1['subject'] = $request->subject;                     
                //$this->SendMail($data1,'notification')
                if(1){
                    $response['status']=1;
                    $response['message']="Notification sent successfully.";
                    return response()->json($response);
                }else{
                    $response['message']="Technical error";
                    return response()->json($response);
                }
            }else{
                $response['message']="Unable to fetch users";
                return response()->json($response);
            }
            
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        } 
        
    }

    public function ajaxGetNotificationById(Request $request){
      //print_r($request->all()); die; 
        try{
          $response = ["status"=>0,"message"=>"","data"=>[]];
          $id = $request->id;
          if($id=="" || $id==0){
              $response['message']="No record found!!";
              return response()->json($response);
              //echo json_encode($response); die;
          }
         
          $data = Notification::where('id',$id)->first();          
          $response['status'] = 1;
          $response['data'] = $data;
          return response()->json($response);
      }catch(Exception $e){
          DB::rollBack();
          abort(500, $e->message());
      } 
    }

    public function ajaxGetUserByType(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->id;
            if($id=="" || $id==0){
                $response['message']="No record found!!";
                return response()->json($response);
                //echo json_encode($response); die;
            }
           
            $data = User::where('type_user_id',$id)->get();          
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response);
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        } 
    }

    public function ajaxGetNotification(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","ntcount"=>0,"data"=>[]];
            $id = \Auth::user()->id;                       
            $data = DB::table('notification_users')
            ->select(DB::raw('count(notification_users.notification_id) as ntcount'))
            ->join('notifications','notifications.id','=','notification_users.notification_id')
            ->where('notification_users.user_id',$id)->first();         
            $response['status'] = 1;
            $response['ntcount'] = $data->ntcount;
            
            return response()->json($response);
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        } 
    }

    public function ajaxNotificationData(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = \Auth::user()->id;                       
            $data = DB::table('notification_users')
            ->select('notifications.*','notification_users.created_at as created')
            ->join('notifications','notifications.id','=','notification_users.notification_id')
            ->where('notification_users.user_id',$id)->get();         
            $response['status'] = 1;
            $response['data'] = $data;
            
            return response()->json($response);
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        } 
    }
    
    
}
