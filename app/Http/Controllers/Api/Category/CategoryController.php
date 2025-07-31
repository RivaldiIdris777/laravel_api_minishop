<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        //get all posts
        $categories = Category::latest()->get();

        //return collection of posts as a resource
        return new CategoryResource(true, 'List Data Categories', 200, $categories);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [            
            'name'     => 'required',
            'slug'   => 'required',
            'status' => 'required',
            'show_at_home' => 'required'
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }        

        //create post
        $categories = Category::create([            
            'name'     => $request->name,
            'slug'     => $request->slug,
            'status'     => $request->status,
            'show_at_home'     => $request->show_at_home,            
        ]);

        //return response
        return new CategoryResource(true, 'Data Category Berhasil Ditambahkan!', 200, $categories);
    }
}
