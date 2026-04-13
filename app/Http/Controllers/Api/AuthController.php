<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{User, CustomerProfile, HotelProfile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, DB, Validator};

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required|unique:users',
            'role' => 'required|in:customer,hotel',
            // Profile fields
            'first_name' => 'required_if:role,customer',
            'hotel_name' => 'required_if:role,hotel',
            'address' => 'required_if:role,hotel',
        ]);

        if ($v->fails()) return response()->json($v->errors(), 422);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role,
            ]);

            if ($request->role === 'customer') {
                $user->customerProfile()->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ]);
            } else {
                $user->hotelProfile()->create([
                    'hotel_name' => $request->hotel_name,
                    'address' => $request->address,
                ]);
            }

            return response()->json([
                'token' => $user->createToken('api_token')->plainTextToken,
                'message' => 'Account created successfully'
            ]);
        });
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Load the relevant profile based on role
        $profile = ($user->role === 'hotel') ? $user->hotelProfile : $user->customerProfile;

        return response()->json([
            'token' => $user->createToken('api_token')->plainTextToken,
            'role' => $user->role,
            'profile' => $profile
        ]);
    }
}