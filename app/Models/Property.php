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

    protected $fillable = [
        'User_ID', 'Property_Title', 'Property_Description', 'Property_Price', 'Property_Location'
    ];

    protected $dates = ['Property_CreatedAt', 'Property_UpdatedAt', 'Property_DeletedAt'];
    const CREATED_AT = 'Property_CreatedAt';
    const UPDATED_AT = 'Property_UpdatedAt';
    const DELETED_AT = 'Property_DeletedAt';

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'Property_ID');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'Property_ID');
    }
}
?>