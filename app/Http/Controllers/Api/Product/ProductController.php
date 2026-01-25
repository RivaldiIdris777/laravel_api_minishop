<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all posts
        $products = Product::latest()->get();

        //return collection of posts as a resource
        return new ProductResource(true, 'List Data Products', 200, $products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [            
            'image'   =>            'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'name'     =>           'required|string|max:200',            
            'status' =>             'required',
            'show_at_home'  =>      'required',
            'category_id' =>        'required',
            'price' =>              'required|numeric',
            'offer_price' =>        'required|numeric',
            'short_description' =>  'required',
            'long_description' =>   'required',
            'sku' =>                'required',
            'seo_title' =>          'required',
            'seo_description' =>    'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
         
        // upload image to public disk and store filename
        $image = $request->file('image');
        $imagePath = $image->store('product', 'public');
        $imageName = basename($imagePath);

        // create post (store filename, accessor builds URL)
        $products = Product::create([            
            'image'         =>      $imageName,
            'name' =>               $request->name,
            'slug' =>               generateUniqueSlug('Product', $request->name),
            'category_id' =>        $request->category_id,
            'price' =>              $request->price,
            'offer_price' =>        $request->offer_price,
            'short_description' =>  $request->short_description,
            'long_description' =>   $request->long_description,
            'sku' =>                $request->sku,
            'seo_title' =>          $request->seo_title,
            'seo_description' =>    $request->seo_description,
            'show_at_home' =>       $request->show_at_home,
            'status' =>             $request->status,
        ]);

        //return response
        return new ProductResource(true, 'Data Product Berhasil Ditambahkan!', 200, $products);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //find post by ID
        $product = Product::find($id);

        //return single post as a resource
        return new ProductResource(true, 'Detail Data Product!', 200, $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
           // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'status' => 'required',
            'show_at_home' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'offer_price' => 'required|numeric',
            'short_description' => 'required',
            'long_description' => 'required',
            'sku' => 'required',
            'seo_title' => 'required',
            'seo_description' => 'required',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cari produk berdasarkan ID
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        try {
                // Build update payload
                $updateData = [
                    'name' => $request->name,
                    'slug' => generateUniqueSlug('Product', $request->name),
                    'category_id' => $request->category_id,
                    'price' => $request->price,
                    'offer_price' => $request->offer_price,
                    'short_description' => $request->short_description,
                    'long_description' => $request->long_description,
                    'sku' => $request->sku,
                    'seo_title' => $request->seo_title,
                    'seo_description' => $request->seo_description,
                    'show_at_home' => $request->show_at_home,
                    'status' => $request->status,
                ];

                // If new image provided, store it and delete old one
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imagePath = $image->store('product', 'public');
                    $imageName = basename($imagePath);

                    if ($product->image) {
                        Storage::disk('public')->delete('product/' . $product->image);
                    }

                    $updateData['image'] = $imageName;
                }

                // Update product
                $product->update($updateData);

            return new ProductResource(true, 'Data Product berhasil diperbarui!', 200, $product);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui produk.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //find post by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // Ambil nama file dari URL
        $imageName = basename($product->image);

        // Delete image file
        Storage::delete('product/' . $imageName);

        // Delete product
        $product->delete();

        //return response
        return new ProductResource(true, 'Detail Data Product!', 200, null);
    }
}
