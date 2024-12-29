<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\SaleNews;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function addToWishlist(Request $request)
    {
        $user = Auth::user();

        try {
            // Check if the user is logged in
            

            // Validate the input data
            $request->validate([
                'sale_new_id' => 'required|integer|exists:sale_news,sale_new_id',
            ]);

            // Retrieve the product information
            $saleNews = SaleNews::find($request->sale_new_id);

            // Check if the product belongs to the logged-in user
            if ($saleNews->user_id == $user->user_id) {
                if ($request->ajax()) {
                    return response()->json(['type' => 'error', 'message' => 'This is your own sale news. You cannot add it to your favorites list.'], 400);
                }
                return redirect()->back()->with('alert', ['type' => 'error', 'message' => 'This is your own sale news. You cannot add it to your favorites list.']);
            }

            // Check if the product is already in the favorites list
            $existingLike = Like::where('user_id', $user->user_id)
                ->where('sale_new_id', $request->sale_new_id)
                ->first();

            if ($existingLike) {
                if ($request->ajax()) {
                    return response()->json(['type' => 'error', 'message' => 'This item is already in your favorites list!'], 400); // Return error if it's an AJAX request
                }
                return redirect()->back()->with('alert', ['type' => 'error', 'message' => 'This item is already in your favorites list!']);
            }

            // If not already added, add it to the favorites list
            Like::create([
                'user_id' => $user->user_id,
                'sale_new_id' => $request->sale_new_id,
                'status' => 1,
            ]);

            if ($request->ajax()) {
                return response()->json(['type' => 'success', 'message' => 'Item added to your favorites list successfully.']); // Return success message for AJAX
            }

            return redirect()->back()->with('alert', ['type' => 'success', 'message' => 'Item added to your favorites list successfully.']);
        } catch (\Exception $e) {
            // If an error occurs, return an error message
            if ($request->ajax()) {
                return response()->json(['type' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('alert', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }




    public function index()
    {
        $userId = Auth::id();

        // Lấy các sản phẩm từ bảng wishlist của người dùng
        $wishlist = Like::where('user_id', $userId)
            ->with(['saleNews' => function ($query) {
                $query->where('approved', 1)  // Chỉ lấy các sản phẩm đã được phê duyệt
                    ->whereNull('is_delete');  // Chỉ lấy các sản phẩm có is_delete là null
            }])
            ->with('saleNews.images') // Lấy các hình ảnh liên quan đến sản phẩm
            ->get();

        // Lọc những sản phẩm yêu thích có thông tin hợp lệ
        $salenews = SaleNews::with('images')  // Đảm bảo bao gồm mối quan hệ 'images'
            ->whereIn('sale_new_id', $wishlist->pluck('sale_new_id'))  // Lấy các sale_new_id từ wishlist

            ->get();  // Lấy tất cả các bản ghi hợp lệ

        // Truyền các sản phẩm vào view
        return view('layouts.wishlist', compact('salenews', 'wishlist'));
    }


    public function destroy(Like $like)
    {
        // Xóa mục yêu thích
        $like->delete();

        // Trả về thông báo thành công qua JSON
        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites list.'
        ]);
    }
    // headeer
    public function toggleWishlist(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['type' => 'error', 'message' => 'You need to log in to manage your wishlist.'], 401);
        }
       
        $user = Auth::user();
        
        try {
            $request->validate([
                'sale_new_id' => 'required|integer|exists:sale_news,sale_new_id',
            ]);

            // Kiểm tra xem bài đăng có phải của chính người dùng hay không
            $saleNew = SaleNews::find($request->sale_new_id);

            if ($saleNew->user_id === $user->user_id) {
                return response()->json(['type' => 'error', 'message' => 'This is your own sale news. You cannot add it to your favorites list.'], 403);
            }

            // Tìm hoặc xóa sản phẩm khỏi danh sách yêu thích
            $like = Like::where('user_id', $user->user_id)
                ->where('sale_new_id', $request->sale_new_id)
                ->first();

            if ($like) {
                // Nếu đã tồn tại, xóa khỏi danh sách yêu thích
                $like->delete();

                return response()->json(['type' => 'success', 'message' => 'Removed from favorites list.']);
            } else {
                // Nếu chưa tồn tại, thêm vào danh sách yêu thích
                Like::create([
                    'user_id' => $user->user_id,
                    'sale_new_id' => $request->sale_new_id,
                    'status' => 1,
                ]);

                return response()->json(['type' => 'success', 'message' => 'Item added to your favorites list successfully.']);
            }
        } catch (\Exception $e) {
            return response()->json(['type' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}