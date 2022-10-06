<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use App\Models\Partner;


class PartnerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:partners',
            'password' => 'required',
            'mobile' => 'required',
            'address' => 'required',
            'city' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $partner = Partner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'address' => $request->address,
            'city' => $request->city,
         ]);

        $token = $partner->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $partner,'access_token' => $token, 'token_type' => 'Bearer', ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()
                ->json(['message' => 'Unauthorized'], 401);
        }

        $partner = Partner::where('email', $request['email'])->firstOrFail();

        $token = $partner->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['message' => 'Hi '.$partner->name.', welcome to home','access_token' => $token, 'token_type' => 'Bearer', ]);
    }

    // method for user logout and delete token
    public function logout()
    {
        auth()->partner()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}



