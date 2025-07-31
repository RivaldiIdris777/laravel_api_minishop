<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        //get all posts
        $users = user::latest()->get();

        //return collection of posts as a resource
        return new UserResource(true, 'List Data Users', $users);
    }
}
