<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  use DB;
class ExpenceDpr extends Model
{
    
    protected $table = 'expence_dprs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','category_expence_id','site_id','description','quantity','rate','amount','remarks','is_submit','status',
    ];

     public static function getAllDprExpenceByUser($user_id){
		 $result = DB::table('expence_dprs')
            ->select('expence_dprs.*','users.name as username','sites.name as site_name',DB::raw('group_concat(expence_dpr_images.image) as images'))
            ->join('users', 'users.id', '=', 'expence_dprs.user_id')
            ->join('sites', 'sites.id', '=', 'expence_dprs.site_id')                                       
            ->leftJoin('expence_dpr_images', 'expence_dpr_images.expence_dpr_id', '=', 'expence_dprs.id')     
            ->groupBy('expence_dprs.id')
            ->orderBy('expence_dprs.id','Desc')
            ->get();

        return $result;
	}     
	
	public static function getAllDprExpence($params=[]){
		 $result = DB::table('expence_dprs')
            ->select('expence_dprs.*','category_expences.name as expence_category_name',
            'users.name as username','sites.name as site_name',DB::raw('CONCAT(expence_dpr_images.image) as images'))         
            ->leftJoin('expence_dpr_images', 'expence_dpr_images.expence_dpr_id', '=', 'expence_dprs.id')      
            ->join('category_expences', 'category_expences.id', '=', 'expence_dprs.category_expence_id')
            ->join('users', 'users.id', '=', 'expence_dprs.user_id')
            ->join('sites', 'sites.id', '=', 'expence_dprs.site_id');

        return $result;
	}    
	
	
	public static function getDprExpenceById($id){
		 $result = DB::table('expence_dprs')
            ->select('expence_dprs.*','category_expences.name as expence_category_name',
            'users.name as username','sites.name as site_name',DB::raw('group_concat(expence_dpr_images.image) as images'))            
            ->join('category_expences', 'category_expences.id', '=', 'expence_dprs.category_expence_id')
            ->join('users', 'users.id', '=', 'expence_dprs.user_id')
            ->leftJoin('expence_dpr_images', 'expence_dpr_images.expence_dpr_id', '=', 'expence_dprs.id')     
            ->join('sites', 'sites.id', '=', 'expence_dprs.site_id')                        
            ->groupBy('expence_dprs.id')
            ->where('expence_dprs.id',$id)                           
            ->orderBy('expence_dprs.id','Desc')->first();

        return $result;
    }   
    

    public static function totalExpenceForMonth($site_id){
        $month = date('m');
        $result = DB::table('expence_dprs')
            ->select(DB::raw('SUM(amount) as total_for_month'))                                    
            ->where(DB::raw('MONTH(created_at)'),$month)
            ->where('site_id',$site_id)
            ->where('status','approved')
            ->get();

        return $result;
    }

    public static function totalExpenceForDay($site_id){
        $day = date('d');
        $result = DB::table('expence_dprs')
            ->select(DB::raw('SUM(amount) as total_for_day'))                                    
            ->where(DB::raw('DAY(created_at)'),$day)
            ->where('site_id',$site_id)
            ->where('status','approved')
            ->get();

        return $result;
    }
    
    public static function getTodayDprExpenceBySite($site_id){
        $day = date('Y-m-d');
        $result = DB::table('expence_dprs')
           ->select('expence_dprs.*','category_expences.name as expence_category_name',
           'users.name as username','sites.name as site_name',DB::raw('group_concat(expence_dpr_images.image) as images'))            
           ->join('category_expences', 'category_expences.id', '=', 'expence_dprs.category_expence_id')
           ->join('users', 'users.id', '=', 'expence_dprs.user_id')
           ->leftJoin('expence_dpr_images','expence_dpr_images.expence_dpr_id','=','expence_dprs.id')   
           ->join('sites', 'sites.id', '=', 'expence_dprs.site_id')    
           ->where('expence_dprs.site_id',$site_id)          
           ->where(DB::raw('DATE(expence_dprs.created_at)'),$day)                               
           ->groupBy('expence_dprs.id');

       return $result;
   }   

   public static function advanceTaken($site_id){        
        $result = DB::table('site_advances')
            ->select(DB::raw('SUM(amount) as amount'))                                                
            ->where('site_id',$site_id)            
            ->get();

        return $result;
   }
}
