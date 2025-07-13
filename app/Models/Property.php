<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'CN_Properties';
    protected $primaryKey = 'Property_ID';

    // Specify the table's column names
    protected $fillable = [
        'User_ID', 
        'Property_Title', 
        'Property_Description', 
        'Property_Address', 
        'Property_Latitude', 
        'Property_Longitude', 
        'Property_City', 
        // 'Property_State', 
        'Property_Zip_Code', 
        'Property_Price_Per_Month', 
        'Property_Num_Bedrooms', 
        'Property_Num_Bathrooms', 
        'Property_Square_Feet', 
        'Property_Amenities', 
        'Property_Property_Type', 
        'Property_Available_From', 
        'Property_Available_To', 
        'Property_Is_Active'
    ];

    // Specify the dates for the soft deletes and timestamps
    protected $dates = [
        'Property_CreatedAt', 
        'Property_UpdatedAt', 
        'Property_DeletedAt', 
        'Property_Available_From'
    ];

    const CREATED_AT = 'Property_CreatedAt';
    const UPDATED_AT = 'Property_UpdatedAt';
    const DELETED_AT = 'Property_DeletedAt';

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'Property_ID');
    }
    
    public function messages()
    {
        return $this->hasMany(Message::class, 'Property_ID');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'Property_ID');
    }

    // Optionally, you can add accessors or mutators if necessary.
    // For example, to ensure amenities are always returned as an array
    public function getPropertyAmenitiesAttribute($value)
    {
        return json_decode($value, true); // Decode the JSON value to an array
    }

    public function setPropertyAmenitiesAttribute($value)
    {
        $this->attributes['Property_Amenities'] = json_encode($value); // Encode array to JSON when saving
    }
}
?>