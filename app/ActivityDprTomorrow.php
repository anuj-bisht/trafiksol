<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  use DB;

class ActivityDprTomorrow extends Model
{
    
    protected $table = 'activity_dpr_tomorrows';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity_id','user_id','site_id','description','quantity','rfi_no','uom_id','stretch_one','created_for','stretch_two','status',
    ];

    public static function getAllDprActivityByUser($user_id,$site_id){
      $result = DB::table('activity_dpr_tomorrows')
             ->select('activity_dpr_tomorrows.id','activities.name as activity_name',
             'activity_dpr_tomorrows.quantity','activity_dpr_tomorrows.rfi_no',
             'activity_dpr_tomorrows.description','activity_dpr_tomorrows.stretch_one',
             'activity_dpr_tomorrows.stretch_two','users.name as username',
             'sites.name as site_name','uoms.name as uom_name')
             ->join('activities', 'activities.id', '=', 'activity_dpr_tomorrows.activity_id')
             ->join('users', 'users.id', '=', 'activity_dpr_tomorrows.user_id')
             ->join('sites', 'sites.id', '=', 'activity_dpr_tomorrows.site_id')                  
             ->join('uoms', 'uoms.id', '=', 'activity_dpr_tomorrows.uom_id')
             ->where(DB::raw('DATE(activity_dpr_tomorrows.created_for)'),date('Y-m-d',strtotime("+1 day")))
             ->where('sites.id',$site_id);
             if($user_id){
                $result = $result->where('users.id',$user_id);
              }                                  
             $result = $result->orderBy('activity_dpr_tomorrows.id','Desc')
             ->get();
 
         return $result;
   }
   
   public static function getAllDprActivity($params=[]){
      $result = DB::table('activity_dpr_tomorrows')
             ->select('activity_dpr_tomorrows.*','activities.name as activity_name','activity_categories.name as activity_category_name',
             'users.name as username','sites.name as site_name','uoms.name as uom_name')
             ->join('activities', 'activities.id', '=', 'activity_dpr_tomorrows.activity_id')
             ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id')
             ->join('users', 'users.id', '=', 'activity_dpr_tomorrows.user_id')
             ->join('sites', 'sites.id', '=', 'activity_dpr_tomorrows.site_id')            
             ->join('uoms', 'uoms.id', '=', 'activity_dpr_tomorrows.uom_id')                        
             ->groupBy('activity_dpr_tomorrows.id')
             ->orderBy('activity_dpr_tomorrows.id','Desc');
 
         return $result;
   }    
   
  
     public static function getAllDprTomorrowActivity($params=[]){

        $date = date('Y-m-d', strtotime("+1 day"));
        $result = DB::table('activity_dpr_tomorrows')
             ->select('activity_dpr_tomorrows.*','activities.name as activity_name','activity_categories.name as activity_category_name',
             'users.name as username','sites.name as site_name','uoms.name as uom_name')
             ->join('activities', 'activities.id', '=', 'activity_dpr_tomorrows.activity_id')
             ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id')
             ->join('users', 'users.id', '=', 'activity_dpr_tomorrows.user_id')
             ->join('sites', 'sites.id', '=', 'activity_dpr_tomorrows.site_id')            
             ->join('uoms', 'uoms.id', '=', 'activity_dpr_tomorrows.uom_id')          
             ->where('created_for',$date)              
             ->groupBy('activity_dpr_tomorrows.id')
             ->orderBy('activity_dpr_tomorrows.id','Desc');
 
         return $result;
    }   

    public static function getDprActivityById($id){
      $result = DB::table('activity_dpr_tomorrows')
             ->select('activity_dpr_tomorrows.*','activities.name as activity_name','activity_categories.name as activity_category_name',
             'users.name as username','sites.name as site_name','uoms.name as uom_name')
             ->join('activities', 'activities.id', '=', 'activity_dpr_tomorrows.activity_id')
             ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id')
             ->join('users', 'users.id', '=', 'activity_dpr_tomorrows.user_id')
             ->join('sites', 'sites.id', '=', 'activity_dpr_tomorrows.site_id')            
             ->join('uoms', 'uoms.id', '=', 'activity_dpr_tomorrows.uom_id')                      
             ->groupBy('activity_dpr_tomorrows.id')
             ->where('activity_dpr_tomorrows.id',$id)                           
             ->orderBy('activity_dpr_tomorrows.id','Desc')->first();
 
         return $result;
     }  

     public static function getAllDprTomorrowActivityBySite($site_id){

        $date = date('Y-m-d', strtotime("+1 day"));
        $result = DB::table('activity_dpr_tomorrows')
            ->select('activity_dpr_tomorrows.*','activities.name as activity_name','activity_categories.name as activity_category_name',
            'users.name as username','sites.name as site_name','uoms.name as uom_name')
            ->join('activities', 'activities.id', '=', 'activity_dpr_tomorrows.activity_id')
            ->join('activity_categories', 'activity_categories.id', '=', 'activities.activity_category_id')
            ->join('users', 'users.id', '=', 'activity_dpr_tomorrows.user_id')
            ->join('sites', 'sites.id', '=', 'activity_dpr_tomorrows.site_id')            
            ->join('uoms', 'uoms.id', '=', 'activity_dpr_tomorrows.uom_id')          
            ->where('created_for',$date)              
            ->where('activity_dpr_tomorrows.site_id',$site_id)              
            ->groupBy('activity_dpr_tomorrows.id')
            ->orderBy('activity_dpr_tomorrows.id','Desc');

        return $result;
    }  
}
