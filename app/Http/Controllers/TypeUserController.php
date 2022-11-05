<?php
    
namespace App\Http\Controllers;
    
use App\TypeUser;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class TypeUserController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
          //$this->middleware('permission:activity_category-list', ['only' => ['index','show']]);
         $this->middleware('permission:type-user-list|type-user-create|type-user-edit|type-user-delete', ['only' => ['index','show']]);
         $this->middleware('permission:type-user-create', ['only' => ['create','store']]);
         $this->middleware('permission:type-user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:type-user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $usertypes = TypeUser::latest()->where('id','<>',0)->get();
        return view('type_users.index',[compact('usertypes')]);

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
            $obj = TypeUser::whereRaw('1 = 1');
                    
            
            if ($request->parent != "") {            
                $obj = $obj->where('parent_id',$request->parent);
            }

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
                
        return view('type_users.create',[]);
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
            'name' => 'required|unique:type_users,name',            
        ]);
       
    
        TypeUser::create($request->all());
    
        return redirect()->route('type_users.index')
                        ->with('success','User type created successfully.');
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\TypeUser  $TypeUser
     * @return \Illuminate\Http\Response
     */
    public function show(TypeUser $typeuser)
    {
        return view('type_users.show',compact('typeuser'));
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TypeUser  $TypeUser
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeUser $type_user)
    {              
        return view('type_users.edit',compact('type_user'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeUser  $TypeUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeUser $type_user)
    {
        
         request()->validate([
            'name' => 'required|unique:type_users,name,'.$type_user->id
        ]);

                
        $type_user->name = $request->name;        
    
        $type_user->update();
    
        return redirect()->route('type_users.index')
                        ->with('success','User Type updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ActivityCategory  $ActivityCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeUser $typeuser)
    {
        $typeuser->delete();
    
        return redirect()->route('type_users.index')
                        ->with('success','User type deleted successfully');
    }

    
}
