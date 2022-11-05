<?php


namespace App;


use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'name', 'detail'
    ];


    public static function deleteProject(){
        
        $project->delete();
        return redirect()->route('projects.index')
                        ->with('success','Project deleted successfully');
    }
}
