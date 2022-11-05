<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class HardwareRequest extends Model
{
    protected $table = 'hardware_requests';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id','user_id','equipment_required','reason','quantity','ref_no',
        'status','assign_to'
    ];

	public static function getHWList(){
		$result = DB::table('hardware_requests')
                ->select('hardware_requests.*','users.name as username','tickets.subject', 'hardware_request_docs.file_path as path' )            
                //->leftJoin('hardware_request_docs', 'hardware_request_docs.hardware_request_id', '=', 'hardware_requests.id')   
                ->join('users', 'hardware_requests.user_id', '=', 'users.id')                                            
                ->join('tickets', 'tickets.id', '=', 'hardware_requests.ticket_id');
    
        return $result;
    }
    
    public static function getHWListApi($site_id){
		$result = DB::table('hardware_requests')
                ->select('hardware_requests.*','users.name as username','tickets.subject',
                    // DB::raw('group_concat(hardware_request_docs.image) as images'),
                    // DB::raw('group_concat(stores.docket_no) as docket')
                )            
                ->leftJoin('hardware_request_docs', 'hardware_request_docs.hardware_request_id', '=', 'hardware_requests.id')   
                ->join('users', 'hardware_requests.user_id', '=', 'users.id')                                                            
                ->join('tickets', 'tickets.id', '=', 'hardware_requests.ticket_id')
                ->join('equipments', 'equipments.id', '=', 'tickets.equipment_id')
                // ->leftJoin('stores', 'stores.equipment_id', '=', 'equipments.id')
                ->join('sites', 'tickets.site_id', '=', 'sites.id') 
                ->groupBy('tickets.id')               
                ->where('sites.id',$site_id)->get();
    
        return $result;
	}
    
}
