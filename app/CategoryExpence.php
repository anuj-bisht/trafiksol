<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class CategoryExpence extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','parent_id'
    ];

    public static function getExpenceCategoryList(){
        return self::where('parent_id',0)->orderBy('name')->get();
    }
  
    public static function deleteProject(){
        
        $project->delete();
        return redirect()->route('category_expences.index')
                        ->with('success','Category expences deleted successfully');
    }

    
}
