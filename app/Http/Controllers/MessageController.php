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
    protected array $with = ['sender', 'receiver', 'property', 'property.images', 'property.user'];

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

    /**
     * Retrieve messages for a specific user.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getMessagesByUserId(int $userId): JsonResponse
    {
        // Fetch messages where the user is either the sender or receiver
        $messages = Message::with($this->with)
            ->where('Sender_ID', $userId)
            ->orWhere('Receiver_ID', $userId)
            ->orderByDesc('Message_CreatedAt')
            ->get();

        return response()->json($messages);
    }
}
?>