<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'CN_Messages';
    protected $primaryKey = 'Message_ID';

    protected $fillable = [
        'Sender_ID', 'Receiver_ID', 'Property_ID', 'Message_Text', 'Message_Status'
    ];

    protected $dates = ['Message_CreatedAt', 'Message_UpdatedAt', 'Message_DeletedAt'];
    const CREATED_AT = 'Message_CreatedAt';
    const UPDATED_AT = 'Message_UpdatedAt';
    const DELETED_AT = 'Message_DeletedAt';

    public function sender()
    {
        return $this->belongsTo(User::class, 'Sender_ID');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'Receiver_ID');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'Property_ID');
    }
}
?>