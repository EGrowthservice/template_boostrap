<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Like;
use App\Models\SubcategoryAttribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table('products')
        ->leftJoin('photo_gallery', 'products.product_id', '=', 'photo_gallery.product_id')
        ->leftJoin('sub_categories', 'products.sub_category_id', '=', 'sub_categories.sub_category_id')
        ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.category_id')
        ->select(
            'products.*', 
            DB::raw('MIN(photo_gallery.image_name) as image_name'), 
            'categories.name_category as category_name', 
            'sub_categories.name_sub_category as sub_category_name'
        )
        ->where('products.is_delete', '=', '0')
        ->groupBy('products.product_id')
        ->get();
    

    return view('admin.products.index', ['data' => $data]);
    }

    

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     $categories = Category::all();
    //     $subcategories = Subcategory::all(); 

    //     return view('admin.products.add-product', compact('categories', 'subcategories'));
    // }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.add-product', compact('categories'));
    }

    public function getSubcategories($categoryId)
    {
        $subcategoryAttributes = SubcategoryAttribute::where('sub_category_id', $categoryId)->get();
        $subcategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json([
            'subcategories' => $subcategories,
            'subcategory_attributes' => $subcategoryAttributes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'images.*' => 'required|max:2048',
                'productTitle' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'required|string|max:255',
                'category_id' => 'required|integer',
                'subcategory_id' => 'integer',
                'status' => 'integer',
                'variant' => 'required|array',
                'variant.*.name' => 'required|string',
                'variant.*.option' => 'required|string',
            ]);

            $productData = [
                'staff_id' => Auth::id(),
                'sub_category_id' => $validatedData['subcategory_id'],
                'channel_id' => 1,
                'name_product' => $validatedData['productTitle'],
                'price' => $validatedData['price'],
                'data' => json_encode([
                    'productTitle' => $validatedData['productTitle'],
                    'price' => $validatedData['price'],
                    'category_id' => $validatedData['category_id'],
                    'subcategory_id' => $validatedData['subcategory_id'],
                    'description' => $validatedData['description'],
                    'variants' => $validatedData['variant'],
                    'images' => $validatedData['images'],
                ]),
                'description' => $validatedData['description'],
                'status' => $validatedData['status'],
                'created_at' => now(),
            ];

            $insertProduct = DB::table('products')->insertGetId($productData);

            $imageRecords = []; 
            foreach ($request->file('images') as $image) {
                $imageName = 'product_' . time() . '_' . uniqid() . '.' . $image->extension(); 
                $imagePath = 'storage/product/' . $imageName; 
                Storage::disk('public')->putFileAs('product', $image, $imageName);
                $imageRecords[] = [
                    'product_id' => $insertProduct, 
                    'image_name' => $imagePath,
                    'created_at' => now(),
                ];
            }
            if (!empty($imageRecords)) {
                DB::table('photo_gallery')->insert($imageRecords);
            }
            if ($insertProduct) {
                return redirect()->back()->with('alert', [
                    'type' => 'success',
                    'message' => 'Added Successfully !'
                ]);
            } else {
                return redirect()->back()->with('alert', [
                    'type' => 'error',
                    'message' => 'Không thành công !'
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $product_id)
    {
        try {
            $product = Product::find($product_id);
            if ($product) {
                $product->is_delete = '1';
                $product->save();
                return redirect()->back()->with('alert', [
                    'type' => 'success',
                    'message' => 'Product deleted successfully!'
                ]);
            }
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Product not found!'
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => ' Error : ' . $th->getMessage()
            ]);
        }
    }
    public function addToWishlist(Request $request)
{
    if (!Auth::check()) {
        return response()->json([
            'message' => 'Vui lòng đăng nhập trước',
        ], 401);
    }

    $productId = $request->input('product_id');
    $userId = Auth::id();

    // Kiểm tra nếu sản phẩm đã có trong bảng like
    $existingLike = Like::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->first();

    if ($existingLike) {
        return response()->json([
            'message' => 'Sản phẩm đã có trong danh sách yêu thích.',
        ], 400); // Trả về lỗi nếu sản phẩm đã có trong danh sách yêu thích
    }

    // Nếu chưa có, thêm vào bảng like
    Like::create([
        'user_id' => $userId,
        'product_id' => $productId,
    ]);

    return response()->json([
        'message' => 'Sản phẩm đã được thêm vào danh sách yêu thích!',
    ]);
}


}
