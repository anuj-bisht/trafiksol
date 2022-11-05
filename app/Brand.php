<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class Brand extends Model
{
    

    public function vendors(){
        return $this->belongsTo('App\Vendor');
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','parent_id','vendor_id'
    ];
    
    
    public static function deleteProject(){
        
        $project->delete();
        return redirect()->route('projects.index')
                        ->with('success','Project deleted successfully');
    }

    public static function getBrandList(){
        
        return Brand::pluck('name','id')->sortBy("name");
    }

    public static function getBrandListByPatent(){
        
        return Brand::select('name','id')->where('parent_id',0)->orderBy("name")->get();
    }

    public static function getBrandById($id){
        
        return Brand::where('parent_id',$id)->orderBy("name")->get();
    }

    public static function getDDBrandListByPatent(){
        
        return Brand::where('parent_id',0)->pluck('name','id')->sortBy("name");
    }

    public static function getBrandByChild(){
        
        return Brand::where('parent_id','<>',0)->orderBy("name")->get();
    }

    public static function getDDBrandListByChild(){
        
        return Brand::where('parent_id','<>',0)->pluck('name','id')->sortBy("name");
    }

    public static function validateBrand($id){
        return self::where('id',$id)->first();
    }

    public static function ajaxGetBrandByVendor($vendor_id){
        return Brand::where('vendor_id',$vendor_id)->orderBy("name")->get();
    }
}
