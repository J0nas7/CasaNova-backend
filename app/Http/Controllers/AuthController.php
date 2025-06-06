<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\TaskTimeTrack;
use App\Models\TeamUserSeat;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    use AuthService;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $result = $this->registerUser($request->all());

        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], 400);
        }

        return response()->json($result['user'], 201);
    }

    /**
     * Login a user and issue a JWT.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['User_Email', 'password']);
        $result = $this->authenticateUser($credentials);

        if (isset($result['error'])) {
            return response()->json([
                'error' => $result['error'],
                $credentials
            ], 401);
        }

        return response()->json($result, 200);
    }

    /**
     * Logout the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->logoutUser();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * Get details of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        // Get all the teams where the user is assigned a seat
        /*$seats = TeamUserSeat::where('User_ID', $user->User_ID)
            ->with(['team.organisation', 'team.projects']) // Eager load the related Team, Organisation and Projects model
            ->get();

        $organisation = Organisation::with('teams.projects') // Eager load teams and projects
            ->where('User_ID', $user->User_ID)  // Check if the user is the owner of the organisation
            ->orWhereHas('teams.userSeats', function($query) use ($user) {
                $query->where('User_ID', $user->User_ID);  // Check if the user has a seat in any team within the organisation
            })
            ->first();  // Get the first organisation that matches either condition

        $activeTimeTrack = TaskTimeTrack::with('task.project')
            ->where('User_ID', $user->User_ID)
            ->whereNull('Time_Tracking_End_Time') // This checks for an active timer (no end time)
            ->first();*/

        return response()->json([
            "success" => true,
            "message" => "Is logged in",
            "userData" => $user,
            /*"userSeats" => $seats,
            "userOrganisation" => $organisation,
            "userActiveTimeTrack" => $activeTimeTrack*/
        ], 200);
    }
}
?>