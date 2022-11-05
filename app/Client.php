<?php
  
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;

class Client extends Model
{
    
    public function country(){
        return $this->belongsTo('App\Country');
    }

    public function state(){
        return $this->belongsTo('App\State');
    }
    // public function phases() {
    //     return $this->belongsToMany(Phase::class, 'client_phase');
    // }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phase_id', 'address1',
        'address2','city','state_id','country_id','zip'
    ];
    
    public static function getClientList(){

        return self::where('id','<>',0)->pluck('name','id')->sortBy('name');
    }

    public static function getClientsDetails(){

        $clients = DB::table('clients')
            ->select('clients.*', DB::raw('group_concat(phases.name) as phase_name'), 'projects.name as project_name')
            ->join('client_phases', 'client_phases.client_id', '=', 'clients.id')
            ->join('phases', 'phases.id', '=', 'client_phases.phase_id')
            ->join('projects', 'projects.id', '=','phases.project_id')
            ->groupBy('clients.id')
            ->orderBy('clients.id')
            ->get();

        return $clients;
    }

    public static function getClientById($id){

        $clients = DB::table('clients')
            ->select('clients.*', DB::raw('group_concat(phases.name) as phase_name'), 'projects.name as project_name')
            ->join('client_phases', 'client_phases.client_id', '=', 'clients.id')
            ->join('phases', 'phases.id', '=', 'client_phases.phase_id')
            ->join('projects', 'projects.id', '=','phases.project_id')
            ->groupBy('clients.id')
            ->orderBy('clients.id')
            ->where('clients.id',$id)
            ->first();

        return $clients;
    }

    public static function getPhaseByClientId($id){
        $clients = DB::table('client_phases')
            ->select('phases.*', 'projects.name as project_name')
            ->join('clients','clients.id','=','client_phases.client_id')            
            ->join('phases', 'phases.id', '=', 'client_phases.phase_id')
            ->join('projects', 'projects.id', '=','phases.project_id')
            //->groupBy('clients.id')
            ->orderBy('clients.id')
            ->where('clients.id',$id)
            ->get();
        return $clients;
    }
    
}
