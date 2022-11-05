<?php
    
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notification;
use DB;
    
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','DESC')->paginate(10);
        return view('roles.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        $notifications = Notification::latest()->where('id','<>',0)->get();
        return view('roles.create',compact('permission','notifications'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        $data = [];
        if(count($request->notification)>0){
            foreach($request->notification as $k=>$v){
                $data[$k]['role_id'] = $role->id;   
                $data[$k]['notification_id'] = $v;
            }
                        
            DB::table('role_notifications')->insert($data);
            //echo '<pre>';print_r($data); die;
          
        } 

    
        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return view('roles.show',compact('role','rolePermissions'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        DB::beginTransaction();
        

        try{

            $role = Role::find($id);

            $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                ->where("role_has_permissions.role_id",$id)
                ->get();

            $role_permission = DB::table('role_has_permissions')->where('role_id',$id)->get();

            $roleObj = new Role();
            $roleObj->name = 'Role-'.uniqid();
            $roleObj->guard_name = 'web';
            $roleObj->save();
            
            $data = [];
            if($role_permission->count()>0){
                foreach($role_permission as $k=>$v){
                    $data[$k]['permission_id'] = $v->permission_id;   
                    $data[$k]['role_id'] = $roleObj->id;
                }
                DB::table('role_has_permissions')->insert($data); 
                
                
            }
            DB::commit();
            return redirect()->action(
                'RoleController@index' 
            );
            
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        } 
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        
        $notifications = Notification::latest()->where('id','<>',0)->get();
        $selectedNotification = DB::table('role_notifications')->select('notification_id')->where('role_id',$id)->get();
        $selArr = [];
        foreach($selectedNotification as $k=>$v){
            $selArr[] = $v->notification_id;
        }
        $selectedNotification = $selArr;
        //print_r($selectedNotification); die;
        return view('roles.edit',compact('role','permission','rolePermissions','notifications','selectedNotification'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
    
        $role->syncPermissions($request->input('permission'));

        $data = [];
        if(!empty($request->notification)){
            foreach($request->notification as $k=>$v){
                $data[$k]['role_id'] = $id;   
                $data[$k]['notification_id'] = $v;
            }
            
            DB::table('role_notifications')->where('role_id', $id)->delete();
            DB::table('role_notifications')->insert($data);
            //echo '<pre>';print_r($data); die;
          
        } 
    
        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');
    }
}
