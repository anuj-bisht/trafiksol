<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class Project extends Model
{

    public function country(){
        return $this->belongsTo('App\Country');
    }

    public function state(){
        return $this->belongsTo('App\State');
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','alias_name','location','address1','address2',
        'city','state_id','zip','country_id',
    ];
  
    
    public static function getProjectList(){
        
        return Project::pluck('name','id')->sortBy("name");
    }

    public static function getAllProject(){
        
        return Project::where('id','<>','0')->orderBy("name")->get();
    }

    public static function getProjectById($id){
        return Project::where('id',$id)->first();
    }
    
}
