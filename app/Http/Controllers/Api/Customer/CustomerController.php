<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|max:191|unique:customers,email',
            'phone'    => 'nullable|string|max:50',
            'city'     => 'nullable|string|max:150',
            'address'  => 'nullable|string',
            'password' => 'required|string|min:6|confirmed', // expects password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // create customer
        $customer = Customer::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'phone'    => $request->input('phone'),
            'city'     => $request->input('city'),
            'address'  => $request->input('address'),
            'password' => Hash::make($request->input('password')),
            'api_token' => Str::random(60),
        ]);

        // return customer (without password) and token
        return response()->json([
            'message'  => 'Customer registered',
            'customer' => $customer,
            'token'    => $customer->api_token,
        ], 201);
    }

     /**
     * Login customer
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $customer = Customer::where('email', $request->input('email'))->first();

        if (!$customer || !Hash::check($request->input('password'), $customer->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // generate new token (optional rotate token on every login)
        $customer->api_token = Str::random(60);
        $customer->save();

        return response()->json([
            'message'  => 'Login successful',
            'customer' => $customer,
            'token'    => $customer->api_token,
        ], 200);
    }

     /**
     * logout (invalidate token)
     */
    public function logout(Request $request)
    {
        $customer = $request->user(); // requires middleware that sets user by token
        if ($customer) {
            $customer->api_token = null;
            $customer->save();
        }

        return response()->json(['message' => 'Logged out'], 200);
    }
}
