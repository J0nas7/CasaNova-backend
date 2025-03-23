<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends BaseController
{
    /**
     * The model class associated with this controller.
     *
     * @var string
     */
    protected string $modelClass = Message::class;

    /**
     * The relationships to eager load when fetching messages.
     *
     * @var array
     */
    protected array $with = ['sender', 'receiver'];

    /**
     * Define the validation rules for messages.
     *
     * @return array The validation rules.
     */
    protected function rules(): array
    {
        return [
            'Sender_ID' => 'required|integer|exists:CN_Users,User_ID',
            'Receiver_ID' => 'required|integer|exists:CN_Users,User_ID'
        ];
    }
}
