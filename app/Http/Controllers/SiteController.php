<?php

namespace App\Http\Controllers;

use App\Site;
use App\Client;
use App\User;
use App\SiteSla;
use App\SiteAdvance;
use App\Role;
use App\SiteUser;
use App\Project;
use App\TypeVehicle;
use App\SiteVehicle;
use App\ActivityCategory;
use App\SiteActivity;
use App\SiteEquipment;
use App\Brand;
use App\EquipmentSla;
use App\Country;
use App\State;
use App\Vendor;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;

use App\Http\Controllers\Traits\Common;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:site-list|site-create|site-edit|site-delete', ['only' => ['index','show']]);
         $this->middleware('permission:site-create', ['only' => ['create','store']]);
         $this->middleware('permission:site-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:site-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeVehicleList = TypeVehicle::getVehicleTypeList();
        $activityCategoryList = ActivityCategory::getActivityListDD();
        $brands = Brand::getDDBrandListByPatent();

        $vendors = Vendor::getVendorDD();

        $users = User::getUsersList();

        $sla_list = EquipmentSla::getSlaByTypeList('site');

        $sites = Site::latest()->paginate(10);
        return view('sites.index',compact('sites',
        'typeVehicleList','activityCategoryList',
        'brands','sla_list','users','vendors'
        ));
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

            $sub = Site::select('sites.*','clients.name as client_name',
            DB::raw('SUM(site_advances.amount) as advance_sum'),
            'projects.name as project_name')
            ->join('clients','clients.id','=','sites.client_id')
            ->leftJoin('site_advances','site_advances.site_id','=','sites.id')
            ->join('projects','projects.id','=','sites.project_id')
            ->groupBy('sites.id');

            $obj = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery());

            if ($request->search['value'] != "") {
              $obj = $obj->where('name','LIKE',"%".$search."%");
              $obj = $obj->orWhere('alias_name','LIKE',"%".$search."%");
              //$obj = $obj->orWhere('projects.name','LIKE',"%".$search."%");
              //$obj = $obj->orWhere('clients.name','LIKE',"%".$search."%");
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.alias_name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('projects.name',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('clients.name',$sort);
            }
            if(isset($request->order[0]['column']) && $request->order[0]['column']==5){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.advance',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==6){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.location',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==7){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.city',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==8){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.stretch',$sort);
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
        $typeVehicleList = TypeVehicle::getVehicleTypeList();

        $projects = Project::getProjectList();
        $clients = Client::getClientList();
        $userdata = User::with('roles')->get();
        $roles = \Spatie\Permission\Models\Role::all();
        $country_data = Country::getAllCountry();
        $sla_list = EquipmentSla::getSlaByTypeList('site');
		$sla_list = EquipmentSla::getAllSla();
		//print_r($country);die();

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
        //echo '<pre>'; print_r($optionData); die;
        return view('sites.create',compact('clients',
            'optionData',
            'projects',
            'typeVehicleList',
            'sla_list','country_data'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //echo '<pre>'; print_r($request->all()); die;
        try{
            DB::beginTransaction();

            request()->validate([
                'name' => 'required',
                'project_id' => 'required',
                'client_id' => 'required',
                'alias_name' => 'required',
                'stretch_from' => 'required',
                'stretch_to' => 'required',
                'location' => 'required',
                'city' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
            ]);

            $request->merge(['user_id'=>Auth::id()]);
            $this->createSite('site-create' , $request->all(),$request->project_id);

            $insertQuery = Site::create($request->all());
            $data = [];
            if(count($request->to)>0){
                foreach($request->to as $k=>$v){
                    $data[$k]['site_id'] = $insertQuery->id;
                    $data[$k]['user_id'] = $v;
                }
                //SiteUser::where('site_id', $site->id)->delete();
                SiteUser::insert($data);
                DB::commit();
            }

            return redirect()->route('sites.index')
                            ->with('success','Site created successfully.');

        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Site  $Site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
        $equipmentAssigned = Site::getEquipmentAssigned($site->id);
        $activityAssigned = Site::getActivityAssigned($site->id);
        $vehicleAssigned = Site::getVehicleAssigned($site->id);
        //$sla_assigned = SiteSla::where('site_id',$site->id)->first();
        $advance = SiteAdvance::getSiteAdvance($site->id);
        //echo '<pre>';print_r($site->client->name); die;
        $site_users = Site::getSiteUsers($site->id);

        return view('sites.show',compact('site','equipmentAssigned',
        'activityAssigned','vehicleAssigned','advance','site_users'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Site  $Site
     * @return \Illuminate\Http\Response
     */
    public function print(Request $request)
    {
        $site = Site::find($request->id);
        $equipmentAssigned = Site::getEquipmentAssigned($site->id);
        $activityAssigned = Site::getActivityAssigned($site->id);
        $vehicleAssigned = Site::getVehicleAssigned($site->id);
        //$sla_assigned = SiteSla::where('site_id',$site->id)->first();
        $advance = SiteAdvance::getSiteAdvance($site->id);

        $site_users = Site::getSiteUsers($site->id);


        $pdf = PDF::loadView('sites.print', compact(
            'site','equipmentAssigned',
            'activityAssigned','vehicleAssigned','advance','site_users'
        ));

        return $pdf->download($site->name.date('y-m-d').'.pdf');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        $projects = Project::getProjectList();
        $clients = Client::getClientList();
        $userdata = User::with('roles')->get();
        $roles = \Spatie\Permission\Models\Role::all();
        $selected_users = SiteUser::where('site_id',$site->id)->get();
        // print_r($selected_users);die;
        $country_data = Country::getAllCountry();
        $state = State::getStateByCountryDD($site->country_id);

        $sla_list = EquipmentSla::getSlaByTypeList('site');
		$sla_list = EquipmentSla::getAllSla();

        $userids = [];
        if($selected_users->count()){
            foreach($selected_users as $v){
                array_push($userids,$v->user_id);
            }
        }

        //echo '<pre>';print_r($selected_users); die;
        $groupOption = [];
        $optionData = [];
        if($roles->count()){
            foreach($roles as $k=>$v){
                $groupOption[$k] = $v->name;
            }
        }
        $toUser = [];
        if($userdata->count()){
            foreach($userdata as $k=>$v){
                //echo '<pre>';  print_r($v->roles[0]); die;
                if(in_array($v->id,$userids)){
                    if(!in_array($v->roles[0]->name,$groupOption)){
                        $toUser[$v->roles[0]->name] = $v;
                    }else{
                        $toUser[$v->roles[0]->name][] = $v;
                    }
                }else{
                    if(!in_array($v->roles[0]->name,$groupOption)){
                        $optionData[$v->roles[0]->name] = $v;
                    }else{
                        $optionData[$v->roles[0]->name][] = $v;
                    }
                }

            }
        }
        
        return view('sites.edit',compact('clients','site',
        'optionData','toUser','projects','sla_list','country_data','state'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Site  $Site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        try{
            DB::beginTransaction();

            request()->validate([
                'name' => 'required',
                'client_id' => 'required',
                'alias_name' => 'required',
                'stretch_from' => 'required',
                'stretch_to' => 'required',
                'location' => 'required',
                'city' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
            ]);
           
            $request->merge(
	['user_id'=>Auth::id()]);
            $site->update($request->all());

            $data = [];
            if(count($request->to)>0){
                foreach($request->to as $k=>$v){
                    $data[$k]['site_id'] = $site->id;
                    $data[$k]['user_id'] = $v;
                }
                SiteUser::where('site_id', $site->id)->delete();
                SiteUser::insert($data);
                DB::commit();
            }
            $request->merge(['user_id'=>Auth::id()]);
            $this->editSite('site-edit' , $request->all(),$request->project_id);

            return redirect()->route('sites.index')
                            ->with('success','Site updated successfully');

        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site  $Site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')
                        ->with('success','Site deleted successfully');
    }

    public function ajaxGetSiteChainage(Request $request){
        $id = $request->id;
        $data = Site::getSiteById($id);
        $result = $this->getChainage($data->stretch_from,$data->stretch_to);
        //echo $chainage_from; die;
        $this->ajaxResponse = ['success'=>true,'msg'=>"","data"=>$result];
        return response()->json($this->ajaxResponse);

    }

    public function getAssignedVehicleAjax(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->site_id;
            if($id=="" || $id==0){
                $response['message']="No record found!!";
                return response()->json($response);
            }

            $data = Site::getVehicleAssigned($id);
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response);
        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function assignVehicleToSite(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            $site_id = $request->hidden_site_id;
            $vehicle_id = $request->vehicle_id;
            $vehicle_quantity = $request->vehicle_quantity;

            if(empty($site_id) || empty($vehicle_id) || empty($vehicle_quantity)){
                $response['message']="Invalid data send!!";
                return response()->json($response);
            }
            $obj = new SiteVehicle();
            $obj->site_id = $site_id;
            $obj->vehicle_id = $vehicle_id;
            $obj->quantity = $vehicle_quantity;

            if($obj->save()){
                $response['status'] = 1;
                $response['data'] = $obj;
                return response()->json($response);
            }else{
                $response['message'] = 'unable to process!!';
                return response()->json($response);
            }


        }catch(Exception $e){
            abort(500, $e->message());
        }
    }


    public function getAssignedActivityAjax(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->site_id;
            if($id=="" || $id==0){
                $response['message']="No record found!!";
                return response()->json($response);
            }

            $data = Site::getActivityAssigned($id);
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response);
        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function assignActivityToSite(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            $site_id = $request->activity_hidden_site_id;
            $activity_id = $request->activity_id;
            $activity_quantity = $request->activity_quantity;

            if(empty($site_id) || empty($activity_id) || empty($activity_quantity)){
                $response['message']="Invalid data send!!";
                return response()->json($response);
            }
            $obj = new SiteActivity();
            $obj->site_id = $site_id;
            $obj->activity_id = $activity_id;
            $obj->quantity = $activity_quantity;

            if($obj->save()){
                $response['status'] = 1;
                $response['data'] = $obj;
                return response()->json($response);
            }else{
                $response['message'] = 'unable to process!!';
                return response()->json($response);
            }


        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function getAssignedEquipmentAjax(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->site_id;
            if($id=="" || $id==0){
                $response['message']="No record found!!";
                return response()->json($response);
            }

            $data = Site::getEquipmentAssigned($id);
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response);
        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function assignEquipmentToSite(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            $site_id = $request->equipment_hidden_site_id;
            $equipment_id = $request->equipment_id;
            $equipment_chainage = $request->equipment_chainage;
            $equipment_location = $request->location;
            $equipment_sla = $request->equipment_sla;

            if(empty($site_id) || empty($equipment_id)){
                $response['message']="Invalid data send!!";
                return response()->json($response);
            }
           $request->merge(['user_id'=>Auth::id()]);
           $this->addEquipment('equipment-add' , $request->all(),$request->equipment_hidden_site_id);
            $obj = new SiteEquipment();
            $obj->site_id = $site_id;
            $obj->equipment_id = $equipment_id;
            $obj->sla_id = $equipment_sla;
            $obj->chainage = $equipment_chainage;
            $obj->location = $equipment_location;
            if($request->id != '')
            {
$request->merge(['user_id'=>Auth::id()]);
                $this->editEquipment('equipment-edit' , $request->all(),$request->equipment_hidden_site_id);

                DB::table("site_equipments")->where("id",$request->id)->update(['site_id'=>$site_id,'equipment_id'=>$equipment_id,'sla_id'=>$equipment_sla,'chainage'=>$equipment_chainage,'location'=>$equipment_location]);
            }
            else{
                if($obj->save()){
                    $response['status'] = 1;
                    $response['data'] = $obj;
                    return response()->json($response);
                }else{
                    $response['message'] = 'unable to process!!';
                    return response()->json($response);
                }
            }




        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function getAssignedSlaAjax(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->site_id;
            if($id=="" || $id==0){
                $response['message']="No record found!!";
                return response()->json($response);
            }
            $selected = 0;
            $result = SiteSla::where('site_id',$id)->first();
            if(isset($result->sla_id)){
                $selected = $result->sla_id;
            }

            $data = EquipmentSla::getSlaByType('site');
            $response['status'] = 1;
            $response['selected'] = $selected;
            $response['data'] = $data;
            return response()->json($response);
        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function assignSlaToSite(Request $request){

        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            $site_id = $request->hidden_site_sla_id;
            $site_sla_id = $request->sla_type;

            if(empty($site_id) || empty($site_sla_id)){
                $response['message']="Invalid data send!!";
                return response()->json($response);
            }
            SiteSla::where('site_id', $site_id)->delete();

            $data  = [];
            $data[0]['site_id'] = $site_id;
            $data[0]['sla_id'] = $site_sla_id;

            $siteObj = SiteSla::insert($data);

            if($siteObj){
                $response['status'] = 1;
                $response['data'] = $siteObj;
                return response()->json($response);
            }else{
                $response['message'] = 'unable to process!!';
                return response()->json($response);
            }


        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function getAssignedAdvanceAjax(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->site_id;
            if($id=="" || $id==0){
                $response['message']="No record found!!";
                return response()->json($response);
            }
            $selected = 0;
            $result = SiteAdvance::getSiteAdvance($id);

            $response['status'] = 1;
            $response['data'] = $result;
            return response()->json($response);
        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function assignAdvanceToSite(Request $request){

        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];

            $site_id = $request->advance_hidden_site_id;
            $amount = $request->amount;
            $paid_to = $request->paid_to;

            if(empty($site_id) || empty($amount) || empty($paid_to)){
                $response['message']="Invalid data send!!";
                return response()->json($response);
            }
            $data  = [];
            $data[0]['site_id'] = $site_id;
            $data[0]['amount'] = $amount;
            $data[0]['paid_to'] = $paid_to;
            $data[0]['reason'] = $request->reason;
            $data[0]['added_by'] = Auth::id();

            $siteObj = SiteAdvance::insert($data);

            if($siteObj){
                $response['status'] = 1;
                $response['data'] = $siteObj;
                return response()->json($response);
            }else{
                $response['message'] = 'unable to process!!';
                return response()->json($response);
            }


        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function deleteSiteAssignment(Request $request){
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            $id = $request->id;
            $type = $request->type;


            if($type=="equipment"){
                $response['status'] = SiteEquipment::where('id',$id)->delete();
            }else if($type=="activity"){
                $response['status'] = SiteActivity::where('id',$id)->delete();
            }else if($type=="advance"){
                $response['status'] = SiteAdvance::where('id',$id)->delete();
            }else if($type=="vehicle"){
                $response['status'] = SiteVehicle::where('id',$id)->delete();
            }else if($type=="sitesla"){
                $response['status'] = SiteSla::where('id',$id)->delete();
            }


            return response()->json($response);
        }catch(Exception $e){
            abort(500, $e->message());
        }
    }

    public function getEquipmentData($equip_id)
    {

        $data = DB::table("site_equipments")->select('site_equipments.*','equipments.model_id','equipments.brand_id','brands.vendor_id')->join("equipments","equipments.id","=","site_equipments.equipment_id")->join("brands","brands.id","=","equipments.brand_id")->where("site_equipments.id",$equip_id)->get();
        // print_r(json_encode($data));
        if(!$data->isEmpty())
        {
            return response()->json(['status'=>200,'data'=>$data]);
        }
        else
        {
            return response()->json(['status'=>400,'data'=>'']);
        }
    }
}
