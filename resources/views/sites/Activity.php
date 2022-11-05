<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Activity extends Model
{
    //protected $table = 'models';

    public function activity_cateory(){
        return $this->belongsTo('App\ActivityCategory','activity_category_id','id');
    }

    public function uom(){
        return $this->belongsTo('App\Uom');
    }

    public static function getActivityByCategoryId($category_id){
        return self::where('activity_category_id',$category_id)->get();
    }

    public static function validateActivity($id){
        return self::where('id',$id)->first();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity_category_id','uom_id','name','status'
    ];

    public static function siteActivityList($site_id){        
        $result = DB::table('site_activities')
            ->select('site_activities.*','activities.name as activity_name',
            'uoms.name as uom_name','activity_categories.name as activity_category_name','sites.name as site_name')
            ->join('activities','activities.id','=','site_activities.activity_id')   
            ->leftJoin('sites','sites.id','=','site_activities.site_id')                                   
             ->join('activity_categories','activity_categories.id','=','site_activities.activity_id')                                           
            ->leftJoin('uoms','uoms.id','=','activities.uom_id')     
            ->groupBy('site_activities.activity_id')                                     
            ->where('site_activities.site_id',$site_id)            
            ->get();            
        return $result;
    }

        
}
