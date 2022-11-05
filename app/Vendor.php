<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class Vendor extends Model
{
    
    public function category_vendor(){
        return $this->belongsTo('App\CategoryVendor');
    }

    

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name', 'category_vendor_id','regofc_address', 'regofc_telephone','regofc_website',
      'regofc_fax','regofc_contact_person','regofc_email','regofc_designation',
      'regofc_mobile','workofc_address', 'workofc_telephone','workofc_website',
      'workofc_fax','workofc_contact_person','workofc_email','workofc_designation',
      'workofc_mobile',
  ];


  public static function getVendorDD(){
    return self::where('id','<>',0)->pluck('name','id')->sortBy('name');
  }
  
  public static function getVendors(){
    return self::where('id','<>',0)->get();
  }
    
}
