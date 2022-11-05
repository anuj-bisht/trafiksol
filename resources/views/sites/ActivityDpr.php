<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  use DB;
  
class ActivityDpr extends Model
{
    
    protected $table = 'activity_dprs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity_id','site_id','user_id','description','quantity','rfi_no','uom_id','is_submit','stretch_one','stretch_two','status',
    ];

    public static function getAllDprActivityByUser($user_id,$site_id){
		 $result = DB::table('activity_dprs')
            ->select('activity_dprs.id','activities.name as activity_name','activity_dprs.status','activity_dprs.created_at','activity_dprs.quantity','activity_dprs.rfi_no','activity_dprs.description','activity_dprs.stretch_one',
            'activity_dprs.stretch_two','users.name as username','sites.name as site_name','uoms.name as uom_name',DB::raw('group_concat(activity_dpr_images.image) as images'))
            ->join('activities', 'activities.id', '=', 'activity_dprs.activity_id')
            ->join('users', 'users.id', '=', 'activity_dprs.user_id')
            ->join('sites', 'sites.id', '=', 'activity_dprs.site_id')     
            ->leftJoin('activity_dpr_images', 'activity_dpr_images.activity_dpr_id', '=', 'activity_dprs.id')            
            ->join('uoms', 'uoms.id', '=', 'activity_dprs.uom_id')
            ->where(DB::raw('DATE(activity_dprs.created_at)'),date('Y-m-d'))
            ->where('sites.id',$site_id);
            if($user_id){
				$result = $result->where('users.id',$user_id);
			}                        
            $result = $result->groupBy('activity_dprs.id')
            ->orderBy('activity_dprs.id','Desc')
            ->get();

        return $result;
	}
	
	public static function getAllDprActivity($params=[]){
		 $result = DB::table('activity_dprs')
            ->select('activity_dprs.*','activities.name as activity_name','activity_categories.name as activity_category_name',
            'users.name as username','sites.name as site_name','uoms.name as uom_name',
            DB::raw('CONCAT(activity_dpr_images.image) as images')            
            )
            ->join('activities', 'activities.id', '=', 'activity_dprs.activity_id')              
            ->leftJoin('activity_dpr_images', 'activity_dpr_images.activity_dpr_id', '=', 'activity_dprs.id')   
            ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id')
            ->join('users', 'users.id', '=', 'activity_dprs.user_id')
            ->join('sites', 'sites.id', '=', 'activity_dprs.site_id')            
            ->join('uoms', 'uoms.id', '=', 'activity_dprs.uom_id');                            

        return $result;
	}    
	
	public static function getDprActivityById($id){
		 $result = DB::table('activity_dprs')
            ->select('activity_dprs.*','activities.name as activity_name','activity_categories.name as activity_category_name',
            'users.name as username','sites.name as site_name','uoms.name as uom_name',DB::raw('group_concat(activity_dpr_images.image) as images'))
            ->join('activities', 'activities.id', '=', 'activity_dprs.activity_id')
            ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id')
            ->join('users', 'users.id', '=', 'activity_dprs.user_id')
            ->join('sites', 'sites.id', '=', 'activity_dprs.site_id')            
            ->join('uoms', 'uoms.id', '=', 'activity_dprs.uom_id')         
            ->leftJoin('activity_dpr_images', 'activity_dpr_images.activity_dpr_id', '=', 'activity_dprs.id')            
            ->groupBy('activity_dprs.id')
            ->where('activity_dprs.id',$id)                           
            ->orderBy('activity_dprs.id','Desc')->first();

        return $result;
    }   


    public static function getTotalActivity($site_id){        
        $result = DB::table('site_activities')
            ->select('site_activities.*','activities.name as activity_name',
            'uoms.name as uom_name','activity_categories.name as activity_category_name','sites.name as site_name',
            DB::raw('SUM(activity_dprs.quantity) as total_finished')
            )
            ->join('activities','activities.id','=','site_activities.activity_id')   
            ->leftJoin('sites','sites.id','=','site_activities.site_id')         
            ->leftJoin('activity_dprs', function($join){
                $join->on('activity_dprs.activity_id', '=', 'activities.id')                
                ->where('activity_dprs.status', '=', 'approved');
             })              
             ->join('activity_categories','activity_categories.id','=','site_activities.activity_id')                               
            //->leftJoin('activity_dprs','activities.id','=','activity_dprs.activity_id')                                                                                          
            ->leftJoin('uoms','uoms.id','=','activities.uom_id')     
            ->groupBy('site_activities.activity_id')                                     
            ->where('site_activities.site_id',$site_id)            
            ->get();            
        return $result;
    }

    public static function getTodaysActivity($site_id){        
        $result = DB::table('site_activities')
            ->select('site_activities.*','activities.name as activity_name',
            DB::raw('SUM(activity_dprs.quantity) as total'),'uoms.name as uom_name'
            )
            ->join('activities','activities.id','=','site_activities.activity_id')   
            ->leftJoin('sites','sites.id','=','site_activities.site_id')         
            ->leftJoin('activity_dprs', function($join){
                $join->on('activity_dprs.activity_id', '=', 'activities.id')                
                ->where('activity_dprs.status', '=', 'approved');
             })                                             
            //->leftJoin('activity_dprs','activities.id','=','activity_dprs.activity_id')                                                                                          
            ->leftJoin('uoms','uoms.id','=','activity_dprs.uom_id')     
            ->groupBy('activities.name')                                     
            ->where('site_activities.site_id',$site_id)            
            ->get();            
        return $result;
    }

    public static function getTodaysActivityReport($site_id){        
        $result = DB::table('activity_dprs')
            ->select('activity_dprs.*','activities.name as activity_name',
            'uoms.name as uom_name',DB::raw('group_concat(activity_dpr_images.image) as images')
            )
            ->join('activities','activities.id','=','activity_dprs.activity_id')   
            ->leftJoin('activity_dpr_images','activity_dpr_images.activity_dpr_id','=','activity_dprs.id')   
            ->join('sites','sites.id','=','activity_dprs.site_id')                     
            ->leftJoin('uoms','uoms.id','=','activity_dprs.uom_id')     
            ->groupBy('activities.name')                                     
            ->where('activity_dprs.site_id',$site_id)            
            ->get();            
        return $result;
    }

    
    
}
