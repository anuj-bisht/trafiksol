<?php
    
namespace App\Http\Controllers;
    
use App\Project;
use App\Country;
use App\State;
use App\City;
use Illuminate\Http\Request;
    
class ProjectController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:project-list|project-create|project-edit|project-delete', ['only' => ['index','show']]);
         $this->middleware('permission:project-create', ['only' => ['create','store']]);
         $this->middleware('permission:project-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:project-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::latest()->paginate(5);
        return view('projects.index',compact('projects'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
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

            $obj = Project::where('id','<>',0);
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('name','LIKE',"%".$search."%");
            } 

            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('alias_name',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('location',$sort);
            }

            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('city',$sort);
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
        $country_data = Country::getAllCountry();
        return view('projects.create',compact('country_data'));
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
            'alias_name' => 'required',
            'location' => 'required',
            'country_id'=>'required',
            'state_id'=>'required',
            'city' => 'required',
        ]);
    
        Project::create($request->all());
    
        return redirect()->route('projects.index')
                        ->with('success','Project created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('projects.show',compact('project'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $country_data = Country::getAllCountry();
        $state_data = State::getStateByCountryDD($project->country_id);
        return view('projects.edit',compact('project','country_data','state_data'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
         request()->validate([
            'name' => 'required',
            'alias_name' => 'required',
            'location' => 'required',
            'country_id'=>'required',
            'state_id'=>'required',
            'city' => 'required',
        ]);
    
        $project->update($request->all());
    
        return redirect()->route('projects.index')
                        ->with('success','Project updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
    
        return redirect()->route('projects.index')
                        ->with('success','Project deleted successfully');
    }

    public function ajaxGetProjectChainage(Request $request){
        $id = $request->id;
        $data = Project::getProjectById($id);        
        $result = $this->getChainage($data->stretch_from,$data->stretch_to);
        //echo $chainage_from; die;
        $this->ajaxResponse = ['success'=>true,'msg'=>"","data"=>$result];
        return response()->json($this->ajaxResponse);
  
    }


}
