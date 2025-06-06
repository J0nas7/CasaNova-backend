<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'CN_Property_Images';
    protected $primaryKey = 'Image_ID';

    protected $fillable = [
        'Property_ID',
        'Image_Name',
        'Image_Path',
        'Image_Type',
        'Image_URL',
        'Image_Order',
    ];

    protected $dates = ['Image_CreatedAt', 'Image_UpdatedAt', 'Image_DeletedAt'];
    const CREATED_AT = 'Image_CreatedAt';
    const UPDATED_AT = 'Image_UpdatedAt';
    const DELETED_AT = 'Image_DeletedAt';

    public function property()
    {
        return $this->belongsTo(Property::class, 'Property_ID');
    }
}
?>