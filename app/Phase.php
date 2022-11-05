<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class Phase extends Model
{
    
    public function project(){
        return $this->belongsTo('App\Project');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'project_id','alias_name'
    ];

    public static function getPhasesByProjectId($id){
        return self::where('project_id',$id)->get();
    }
}
