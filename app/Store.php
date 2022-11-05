<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  use DB;
class Store extends Model
{
    protected $table = "stores";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function brand(){
        return $this->belongsTo('App\Brand');
    }

    public function site(){
        return $this->belongsTo('App\Site');
    }
    
    public function models(){
        return $this->belongsTo('App\Models','model_id','id');
    }

    
    protected $fillable = [
        'quantity','site_id', 'brand_id','model_id'.'equipment_id','item_name',
        'item_code','docket_no','type','store_type'
    ];
  
    
    public static function getStoreBySite($site_id,$type=1,$store_type="Store"){
        $result = DB::table('stores')
            ->select('stores.*','brands.name as brand_name','models.model','equipments.title','sites.name as site_name')                        
            ->join('sites', 'sites.id', '=', 'stores.site_id')                                                                                  
            ->join('brands', 'brands.id', '=', 'stores.brand_id')                                                                                                                                     
            ->leftJoin('equipments', 'equipments.id', '=', 'stores.equipment_id')                                                                                                                                     
            ->join('models', 'models.id', '=', 'stores.model_id')                                                                                                                                     
            ->where('stores.site_id',$site_id)            
            ->where('stores.type',"$type")    
            ->where('stores.store_type',"$store_type")    
            ->orderBy('stores.id','desc')
            ->get();

        return $result;
    }
}
