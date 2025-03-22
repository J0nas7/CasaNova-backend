<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'CN_Favorites';
    protected $primaryKey = 'Favorite_ID';

    protected $fillable = [
        'User_ID', 'Property_ID'
    ];

    protected $dates = ['Favorite_CreatedAt', 'Favorite_UpdatedAt', 'Favorite_DeletedAt'];
    const CREATED_AT = 'Favorite_CreatedAt';
    const UPDATED_AT = 'Favorite_UpdatedAt';
    const DELETED_AT = 'Favorite_DeletedAt';

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'Property_ID');
    }
}
?>