<?php
    
namespace App\Http\Controllers;
    
use App\Client;
use App\User;
//use App\ClientPhase;
use App\Phase;
use App\Country;
use App\State;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use DB;    
class ClientController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:client-list|client-create|client-edit|client-delete', ['only' => ['index','show']]);
         $this->middleware('permission:client-create', ['only' => ['create','store']]);
         $this->middleware('permission:client-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:client-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //$result = Client::getClientsDetails();        
        //$clients = Client::latest()->paginate(5);
        //return view('clients.index',compact('clients'))->with('i', (request()->input('page', 1) - 1) * 5);
        return view('clients.index',[]);
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

            $obj = Client::select("clients.*",DB::raw('group_concat(users.name) as username'))->where('clients.id','<>',0)
            ->leftJoin('client_users','client_users.client_id','=','clients.id')
            ->leftJoin('users','client_users.user_id','=','users.id');
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('clients.name','LIKE',"%".$search."%");
            } 
            
            $obj = $obj->groupBy('clients.id');

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('clients.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('clients.email',$sort);
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
        $client_user = User::getClientUser();
        $countrydata = Country::getAllCountry();        
        return view('clients.create',compact('client_user','countrydata'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();    
            request()->validate([
                'name' => 'required',                
                'address1' => 'required',                                       
                'city' => 'required',                                       
                'state_id' => 'required',                                       
                'country_id' => 'required',                                       
                'zip' => 'required',                                       
            ]);
    
            
            
            $obj = new Client();
    
            $obj->name = $request->name;            
            $obj->address1 = $request->address1;
            $obj->address2 = $request->address2;
            $obj->state_id = $request->state_id;
            $obj->city = $request->city;
            $obj->country_id = $request->country_id;
            $obj->zip = $request->zip;
                
            if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
    
                $upload_handler = new UploadFile();
                $path = public_path('uploads/clients'); 
                $data = $upload_handler->upload($path,'clients');
                $res = json_decode($data);
                if($res->status=='ok'){
                  $obj->image = $res->path;
                  $obj->file_path = $res->img_path;
                }else{
                    return redirect()->route('clients.index')
                    ->with('error','upload file error.');
                }
            }
    
        
            $obj->save();
            //$objID = $obj->id;
    
            $data = [];
            if(isset($request->users) && count($request->users)>0){
                foreach($request->users as $k=>$v){
                    $data[$k]['client_id'] = $obj->id;   
                    $data[$k]['user_id'] = $v;
                }
                                
                DB::table('client_users')->insert($data);
                //echo '<pre>';print_r($data); die;

                
            } 
            DB::commit();
            return redirect()->route('clients.index')
                            ->with('success','Client created successfully.');
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }

        
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $Client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //$client = Client::getClientById($client->id);
        //echo '<pre>';print_r($client); die;

        $clientdata = Client::select("clients.*",'users.name as username')        
        ->leftJoin('client_users','client_users.client_id','=','clients.id')
        ->leftJoin('users','client_users.user_id','=','users.id')
        ->where('clients.id',$client->id)
        ->groupBy('clients.id')
        ->first();

        $client = $clientdata;
        return view('clients.show',compact('client'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Client  $Client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        
        $client_user = User::getClientUser();
        $selected_users = User::getUserByClient($client->id);

        $country = Country::getAllCountry();
        $state = State::getStateByCountryDD($client->country_id);
        
        return view('clients.edit',['client'=>$client, 
        'client_user'=>$client_user,
        'selected_users'=>$selected_users,
        'countrydata'=>$country,
        'state'=>$state
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $Client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        try{
           DB::beginTransaction(); 

            request()->validate([
                'name' => 'required',                
                'address1' => 'required',                                       
                'city' => 'required',                                       
                'state_id' => 'required',                                       
                'country_id' => 'required',                                       
                'zip' => 'required',                                       
            ]);
    
            
            $client->name = $request->name;
            $client->email = $request->email;
            $client->representative = $request->representative;
            $client->address1 = $request->address1;
            $client->address2 = $request->address2;
            $client->state_id = $request->state_id;
            $client->city = $request->city;
            $client->country_id = $request->country_id;
            $client->zip = $request->zip;
                
            if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
    
                $upload_handler = new UploadFile();
                $path = public_path('uploads/clients'); 
                $data = $upload_handler->upload($path,'clients');
                $res = json_decode($data);
                if($res->status=='ok'){
                  //@unlink($client->file_path);
                  $client->image = $res->path;
                  $client->file_path = $res->img_path;
                }else{
                    return redirect()->route('clients.index')
                    ->with('error','upload file error.');
                }
            }
            
            //echo $client->image; die;
        
            $client->update();

            $data = [];
            if(isset($request->users)){
                foreach($request->users as $k=>$v){
                    $data[$k]['client_id'] = $client->id;   
                    $data[$k]['user_id'] = $v;
                }
                
                DB::table('client_users')->where('client_id', $client->id)->delete();
                DB::table('client_users')->insert($data);
                //echo '<pre>';print_r($data); die;

                
            } 
            DB::commit();
            return redirect()->route('clients.index')
                            ->with('success','Client updated successfully');       
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }         
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $Client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();
    
        return redirect()->route('clients.index')
                        ->with('success','Client deleted successfully');
    }
        
}
