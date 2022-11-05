<?php
    
namespace App\Http\Controllers;
    
use App\Phase;
use App\Project;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
    
class PhaseController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:phase-list|phase-create|phase-edit|phase-delete', ['only' => ['index','show']]);
         $this->middleware('permission:phase-create', ['only' => ['create','store']]);
         $this->middleware('permission:phase-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:phase-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $phases = Phase::latest()->paginate(5);
        return view('phases.index',compact('phases'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $project = Project::getProjectList();
        return view('phases.create',['project'=>$project]);
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
            'project_id' => 'required',        
        ]);

        Phase::create($request->all());

        return redirect()->route('phases.index')
                        ->with('success','Project phase created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $Client
     * @return \Illuminate\Http\Response
     */
    public function show(Phase $phase)
    {
        return view('phases.show',compact('phase'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Phase  $Phase
     * @return \Illuminate\Http\Response
     */
    public function edit(Phase $phase)
    {
        $project = Project::getProjectList();

        return view('phases.edit',['project'=>$project,'phase'=>$phase]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Phase  $Phase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Phase $phase)
    {
         request()->validate([
            'name' => 'required',            
            'project_id' => 'required',
        ]);

        $phase->update($request->all());
    
        return redirect()->route('phases.index')
                        ->with('success','Project phase updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Phase  $Phase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Phase $phase)
    {
        $phase->delete();
    
        return redirect()->route('phases.index')
                        ->with('success','Project phase deleted successfully');
    }

    public function ajaxGetPhases(Request $request){
        $id = $request->id;
        $data = Phase::getPhasesByProjectId($id);        
        $this->ajaxResponse = ['success'=>true,'msg'=>"","data"=>$data];
        return response()->json($this->ajaxResponse);
  
    }
}
