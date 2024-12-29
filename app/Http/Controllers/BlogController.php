<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Blog;
use App\Models\Staff;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use Illuminate\Http\Request;


class BlogController extends Controller
{
    // Hiển thị danh sách bài viết
    public function index()
    {
        $blogs = Blog::whereNull('is_delete')->orderBy('created_at','desc')
        ->get();
        return view('admin.blogs.index', compact('blogs'));
    }
    
    public function create()
    {
        $category = Category::all();
        return view('admin.blogs.create', compact('category'));
    }

    public function listting()
    {
        $topBlogs = Blog::orderBy('views', 'desc') // Sắp xếp theo số lượt xem giảm dần
            ->take(5) // Lấy 5 bài viết có views cao nhất
            ->get();
    
        $alltags = Blog::whereNull('is_delete');
    
        $category = Category::withCount(['blogs as blogs_count' => function ($query) {
            $query->where('status', '1')->whereNull('is_delete');
        }])
        ->where('status', '1')
        ->get();
    
        $perPage = 8; // Số bài viết trên mỗi trang
        $blogs = Blog::where('status', '1')->whereNull('is_delete')
            ->with('category')
            ->paginate($perPage); // Sử dụng phân trang
    
        $count = Blog::where('status', '1')->whereNull('is_delete')->count();
    
        return view('blog.listting', compact('blogs', 'topBlogs', 'alltags', 'count', 'category')); // Trả về view với danh sách blog
    }
    

    // Lưu bài viết mới
    public function store(Request $request)
    {


        try {
            // Lấy ID của người dùng đăng nhập hiện tại
            $userId = Auth::guard('staff')->user()->staff_id;

            // Kiểm tra xem người dùng có phải là staff không
            $staff = Staff::find($userId);

            if (!$staff) {
                // Nếu người dùng không phải là staff, trả về thông báo không đủ quyền
                return redirect()->back()->with('alert', [
                    'type' => 'error',
                    'message' => 'You do not have sufficient privileges.'
                ]);
            }

            // Xác thực dữ liệu
            $validatedData = $request->validate([
                'title' => 'required|max:255',
                'content' => 'required|string',
                 
                'short_description' => 'min:10|string|max:255', // Kiểm tra mô tả ngắn
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra ảnh
                'category_id' => 'required|exists:categories,category_id', // Kiểm tra category_id hợp lệ
            ]);

            // Xử lý ảnh
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public'); // Lưu ảnh vào thư mục 'images' trong 'public'
            }

            // Lấy danh sách tags
            $tags = $request->input('tags', []); // Tags mặc định là mảng rỗng nếu không có

            // Tạo bản ghi mới trong bảng blogs
            Blog::create([
                'title' => $validatedData['title'],
                'tags' => $tags,
                'created_at'=>now(),
                'content' => $validatedData['content'],
                'staff_id' => $userId, // Lưu ID của người dùng đăng nhập vào trường staff_id
                'image' => $imagePath, // Lưu đường dẫn ảnh
                'short_description' => $validatedData['short_description'],
                'category_id' => $validatedData['category_id'], // Lưu category_id
            ]);

            // Chuyển hướng về trang danh sách blog với thông báo thành công
            return redirect()->route('blogs.index')->with('alert', [
                'type' => 'success',
                'message' => 'Blog created successfully!'
            ]);
        } catch (\Exception $e) {
            // Xử lý ngoại lệ và trả về thông báo lỗi
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function edit(Blog $blog)
    {
        $categories = Category::all(); // Get all categories
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:6',
            'short_description' => 'required|string|min:10|max:255',
            'status' => 'required|in:0,1',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ảnh có thể không bắt buộc
            'category_id' => 'required|exists:categories,category_id', // Kiểm tra category_id hợp lệ
        ]);

        try {
            // Cập nhật thông tin bài viết
            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->tags = $request->tags;
            $blog->short_description = $request->short_description; // Cập nhật mô tả ngắn
            $blog->status = $request->status;
            $blog->category_id = $request->category_id; // Cập nhật category_id

            // Xử lý ảnh
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                    Storage::disk('public')->delete($blog->image);
                }
                // Lưu ảnh mới
                $imagePath = $request->file('image')->store('images', 'public');
                $blog->image = $imagePath; // Cập nhật đường dẫn ảnh
            }

            // Lưu thay đổi vào cơ sở dữ liệu
            $blog->save();

            // Trả về thông báo thành công
            return redirect()->route('blogs.index')->with('alert', [
                'type' => 'success',
                'message' => 'Blog updated successfully!'
            ]);
        } catch (\Exception $e) {
            // Trả về thông báo lỗi nếu có lỗi xảy ra
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Xóa bài viết
    public function destroy(Blog $blog)
    {
        try {
           
            $blog->is_delete = '1';
           
            $blog->save();



            // Trả về thông báo thành công
            return redirect()->route('blogs.index')->with('alert', [
                'type' => 'success',
                'message' => 'Blog deleted successfully!'
            ]);
        } catch (\Exception $e) {
            // Trả về thông báo lỗi nếu có lỗi xảy ra
            return redirect()->route('blogs.index')->with('alert', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Chuyển đổi trạng thái (Ẩn/Hiển thị)
    public function toggleStatus($id)
    {
        try {
            $blog = Blog::findOrFail($id);
    
            // Đảo ngược trạng thái
            $blog->status = $blog->status == 1 ? 0 : 1;
            $blog->save();
    
            // Xác định loại thông báo và nội dung
            $type = $blog->status == 1 ? 'success' : 'success';
            $message = $blog->status == 1 
                ? 'Blog status has been changed to "Active".' 
                : 'Blog status has been changed to "Inactive".';
    
            // Trả về phản hồi JSON
            return response()->json([
                'status' => $blog->status,
                'alert' => [
                    'type' => $type,
                    'message' => $message
                ]
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi
            return response()->json([
                'status' => 'error',
                'alert' => [
                    'type' => 'error',
                    'message' => 'An error has occurred!: ' . $e->getMessage()
                ]
            ]);
        }
    }

    // Hiển thị chi tiết bài viết
    public function show($id)
    {
         
        $blog = Blog::findOrFail($id)->with('category');
        $blog->increment('views');
        return view('admin.blogs.detail', compact('blog'));
    }
    public function detail($id)
    {
        
        $blogs = Blog::findOrFail($id);
        $blogs->increment('views');
        $alltags = Blog::all()->whereNull('is_delete')->take(4);
        
        $topBlogs = Blog::where('status', 1)->whereNull('is_delete') // Lọc bài viết có status = 1
            ->orderBy('views', 'desc')       // Sắp xếp theo số lượt xem giảm dần
            ->take(4)                         // Lấy 5 bài viết có views cao nhất
            ->get();
        $relatedBlogs = Blog::where('category_id', $blogs->category_id)   // Lọc bài viết theo cùng danh mục
            ->where('status', 1)
            ->whereNull('is_delete')  // Lọc bài viết có status = 1
            ->where('blog_id', '!=', $blogs->blog_id)  // Loại trừ bài viết hiện tại

            ->get();
        $category = Category::withCount(['blogs as blogs_count' => function ($query) {
            $query->where('status', '1')->whereNull('is_delete');
        }])
            ->where('status', '1')
            ->get();


        return view('blog.detail-blog', compact('blogs', 'topBlogs', 'alltags', 'category', 'relatedBlogs'));
    }

    public function search(Request $request)
    {
        // Nếu từ khóa (ws) rỗng, quay lại trang hiện tại
        if (!$request->has('ws') || $request->ws == '') {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Please enter keywords!'
            ]);
        }
    
        // Khởi tạo query cho Blog
        $query = Blog::query();
    
        // Lọc theo từ khóa
        if ($request->has('ws') && $request->ws != '') {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->ws . '%')
                    ->orWhere('content', 'like', '%' . $request->ws . '%');
            });
        }
    
        // Lọc theo ngày tháng (nếu có)
        if ($request->has('date_filter') && $request->date_filter != '') {
            $dateFilter = $request->date_filter;
    
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'this-week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last-week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this-month':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'last-month':
                    $lastMonth = now()->subMonth();
                    $query->whereMonth('created_at', $lastMonth->month)
                        ->whereYear('created_at', $lastMonth->year);
                    break;
                default:
                    break;
            }
        }
    
        // Thêm điều kiện whereNull('is_delete')
        $query->whereNull('is_delete');
    
        $blogs = $query->get();
    
        // Kiểm tra dữ liệu trống
        if ($blogs->isEmpty()) {
            return view('blog.listting_search', [
                'message' => 'There are no articles matching the selected keyword or date.'
            ]);
        }
    
        return view('blog.listting_search', compact('blogs'));
    }
    
    public function trash()
    {

        $blogs = Blog::where('is_delete', '1')->get();
        return view('admin.trash.blog', compact('blogs'));
    }
    public function restore($id)
    {
        try {
            $item = Blog::findOrFail($id);

            // Thay đổi trạng thái giữa 0 và 2
            $item->is_delete = null ;
            $item->save();

            return redirect()->back()->with('alert', [
                'type' => 'success',
                'message' => ' Reject  successfully!'
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }
    public function destroyofadmin(string $id)
    {
        $check = Blog::findOrFail($id);
        if ($check) {

            $check->delete();
            return redirect()->back()->with('alert', [
                'type' => 'success',
                'message' => 'Delete successful !'
            ]);
        } else {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Not found !'
            ]);
        }
    }
}