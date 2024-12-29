<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\SaleNews;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\VipPackage;
use Illuminate\Support\Carbon;
use App\Models\Channel;
use App\Models\Subcategory;

use Illuminate\Support\Facades\File;


use App\Models\SubcategoryAttribute;
use App\Models\Transactions;
use Illuminate\Database\Eloquent\Collection;

class SaleNewsController extends Controller




{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = SaleNews::with(['user', 'subcategory', 'firstImage', 'categoryToSubcategory'])->where('is_delete', null)->get();
        return view('admin.products.index', ['data' => $data]);
    }

    public function indexSaleNewsPartner()
    {

        $channelId = Channel::where('user_id', auth()->user()->user_id)->value('channel_id');

        $data = SaleNews::with(['user', 'sub_category', 'firstImage', 'categoryToSubcategory'])->where('channel_id', $channelId)->where('is_delete', null)->get();
        return view('partner.sale_news.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('salenews.add-sale-news', compact('categories'));
    }

    public function createSaleNewsPartner()
    {
        $categories = Category::all();

        $channels = Channel::where('user_id', auth()->user()->user_id)->first();

        // Trả về view và truyền các dữ liệu cần thiết
        return view('partner.sale_news.add', compact('categories', 'channels'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request['variant']);
        $validatedData = $request->validate([
            'productTitle' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'subcategory_id' => 'required|integer',
            'name_category' => 'required|integer',
            'variant' => 'required',
            'phone' => ['required', 'regex:/^\+?[0-9]{10,15}$/'],
            'hiddenAddress' => 'required',
            'addressDetail' => 'required',
            'images.*' => 'required|max:2048',

        ]);
        // dd($validatedData, auth()->user()->user_id);
        try {
            if (!$request->hasFile('images')) {
                return redirect()->back()->with('alert', [
                    'type' => 'error',
                    'message' => 'Please upload at least one image.',
                ]);
            }


            $jsonData = json_encode($validatedData['variant']);
            // dd($jsonData);

            $productData = [
                'user_id' => auth()->user()->user_id,
                'title' => $validatedData['productTitle'],
                'price' => $validatedData['price'],
                'description' => $validatedData['description'],
                'sub_category_id' => $validatedData['subcategory_id'],
                'data' => $jsonData,
                'phone' => $validatedData['phone'],
                'address' => $validatedData['hiddenAddress'],
                'approved' => 0,
                'status' => 1,
                'created_at' => now(),
            ];

            // dd($productData);
            // $query=DB::table('sale_news')->insert($productData);


            $insertSaleNews = DB::table('sale_news')->insertGetId($productData);

            $imageRecords = [];
            foreach ($request->file('images') as $image) {
                $imageName = 'sale-news_' . time() . '_' . uniqid() . '.' . $image->extension();
                $imagePath = 'storage/sale-news/' . $imageName;
                Storage::disk('public')->putFileAs('sale-news', $image, $imageName);
                $imageRecords[] = [
                    'sale_new_id' => $insertSaleNews,
                    'image_name' => $imagePath,
                    'created_at' => now(),
                ];
            }
            if (!empty($imageRecords)) {
                DB::table('photo_gallery')->insert($imageRecords);
            }
            if ($insertSaleNews) {
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
                'message' => 'Lỗi: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine()
            ]);
        }
    }


    public function storeSaleNewsPartner(Request $request)
    {
        $address = !empty($request->hiddenAddress) ? $request->hiddenAddress : $request->hiddenAddressChannel;
        // dd($address);
        // dd($request['variant']);

        $validatedData = $request->validate([
            'productTitle' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'subcategory_id' => 'required|integer',
            'variant' => 'required',
            'phone' => ['required', 'regex:/^\+?[0-9]{10,15}$/'],
            // 'hiddenAddress' => 'required',
            'images.*' => 'required|max:2048',
        ]);
        // dd($validatedData['variant']);

        // dd($errors->all());
        // dd($validatedData, auth()->user()->user_id);
        try {


            // Kiểm tra và giải mã JSON nếu cần
            $variant = $validatedData['variant'];
            if (is_string($variant)) {
                $variant = json_decode($variant, true); // Giải mã JSON thành mảng
            }

            // Kiểm tra nếu biến $variant không phải là mảng sau khi giải mã
            if (!is_array($variant)) {
                die('Dữ liệu không hợp lệ: variant không phải là mảng.');
            }

            // Chuyển đổi cấu trúc
            $formattedVariant = array_map(function ($item) {
                return [
                    'name' => $item['name'],
                    'option' => $item['options'][0] ?? null // Lấy giá trị đầu tiên trong 'options', nếu không có thì trả về null
                ];
            }, $variant);

            // Chuyển đổi thành JSON
            $jsonData = json_encode($formattedVariant, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // Hiển thị hoặc lưu lại JSON

            // dd($jsonData);


            // $jsonData = json_encode($validatedData['variant']);
            $channel = Channel::where('user_id', auth()->user()->user_id)->first(['vip_package_id', 'vip_start_at', 'vip_end_at']);
            // dd($jsonData);

            // dd($channel);

            $productData = [
                'user_id' => auth()->user()->user_id,
                'channel_id' => $request->idChannel,
                'title' => $validatedData['productTitle'],
                'price' => $validatedData['price'],
                'description' => $validatedData['description'],
                'sub_category_id' => $validatedData['subcategory_id'],
                'data' => $jsonData,
                'address' => $address,
                'phone' => $validatedData['phone'],
                'approved' => 1,
                'status' => 1,
                'vip_package_id' => $channel->vip_package_id,
                'vip_start_at' => $channel->vip_start_at,
                'vip_end_at' => $channel->vip_end_at,
                'created_at' => now(),
            ];

            // dd($productData);
            // $query=DB::table('sale_news')->insert($productData);


            $insertSaleNews = DB::table('sale_news')->insertGetId($productData);

            $imageRecords = [];
            foreach ($request->file('images') as $image) {
                $imageName = 'sale-news_' . time() . '_' . uniqid() . '.' . $image->extension();
                $imagePath = 'storage/sale-news/' . $imageName;
                Storage::disk('public')->putFileAs('sale-news', $image, $imageName);
                $imageRecords[] = [
                    'sale_new_id' => $insertSaleNews,
                    'image_name' => $imagePath,
                    'created_at' => now(),
                ];
            }
            if (!empty($imageRecords)) {
                DB::table('photo_gallery')->insert($imageRecords);
            }
            if ($insertSaleNews) {
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
                'message' => 'Lỗi: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine()
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

        // $dataProductID= DB::table('products')->where('product_id',$id)->first();ß
        // $dataProductID = Product::with(['subcategory', 'images',])->where('product_id', $id)->first();
        $dataCategory = Category::with(['subcategories'])->get();
        // dd($dataCategory);
        return view('admin.products.update-product', [
            // 'dataProductID' => $dataProductID,
            'dataCategory' => $dataCategory
        ]);
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



    public function getAllSaleStatus()
    {
        $data = SaleNews::with('vipPackage', 'firstImage', 'sub_category')
            ->where('status', 1)->where('is_delete', null)->where('user_id', auth()->user()->user_id);

        $count_now_showing = $data->where('approved', 1)->where('is_delete', null)->count();
        $list_now_showing = $data->where('approved', 1)->where('is_delete', null)->get();

        $list_pending_approval = SaleNews::with('vipPackage', 'firstImage', 'sub_category')
            ->where('approved', 0)->where('is_delete', null)->where('user_id', auth()->user()->user_id)
            ->get();
        $count_pending_approval = SaleNews::with('vipPackage')
            ->where('approved', 0)->where('user_id', auth()->user()->user_id)->where('is_delete', null)->count();


        $count_not_accepted = SaleNews::with('vipPackage')
            ->where('approved', 2)->where('user_id', auth()->user()->user_id)->where('is_delete', null)
            ->count();
        $list_not_accepted = SaleNews::with('vipPackage', 'firstImage', 'sub_category')
            ->where('approved', 2)->where('user_id', auth()->user()->user_id)->where('is_delete', null)
            ->get();

        $count_hidden = SaleNews::with('vipPackage')
            ->where('status', 0)
            ->where('approved', 1)->where('user_id', auth()->user()->user_id)->where('is_delete', null)
            ->count();
        $list_hidden = SaleNews::with('vipPackage', 'firstImage', 'sub_category')
            ->where('status', 0)
            ->where('approved', 1)->where('user_id', auth()->user()->user_id)->where('is_delete', null)
            ->get();
        $transactionCount = Transactions::where('user_id', auth()->user()->user_id)->count();

        return view('salenews.index', compact('count_now_showing', 'list_now_showing', 'list_pending_approval', 'count_pending_approval', 'list_not_accepted', 'count_not_accepted', 'count_hidden', 'list_hidden', 'transactionCount'));
    }

    public function getSubcategories($categoryId)
    {
        $subcategoryAttributes = SubcategoryAttribute::where('category_id', $categoryId)->get();
        $subcategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json([
            'subcategories' => $subcategories,
            'subcategory_attributes' => $subcategoryAttributes
        ]);
    }


    public function promote($id)
    {
        $listing = SaleNews::with(['vipPackage', 'firstImage'])->findOrFail($id);
        $vipPackages = VipPackage::where('type', 'user')->where('status', 1)->get();
        // dd($listing);
        return view('salenews.promote', compact('listing', 'vipPackages'));
    }

    public function upgrade(Request $request, $id)
    {
        $listing = SaleNews::findOrFail($id);
        $vipPackage = VipPackage::findOrFail($request->vip_package_id);

        $listing->vip_package_id = $vipPackage->id;
        $listing->vip_start_date = Carbon::now();
        $listing->vip_end_date = Carbon::now()->addDays($vipPackage->duration_days);
        $listing->save();

        return redirect()->route('listings.index')->with('success', 'Listing promoted successfully.');
    }

    public function getNextSaleNewId($currentId)
    {
        return SaleNews::where('sale_new_id', '>', $currentId)
            ->where('approved', 1)
            ->where('status', 1)
            ->where('is_delete', null)
            ->orderBy('sale_new_id')
            ->first();
    }

    public function getPreviousSaleNewId($currentId)
    {
        return SaleNews::where('sale_new_id', '<', $currentId)
            ->where('approved', 1)
            ->where('status', 1)
            ->where('is_delete', null)
            ->orderBy('sale_new_id', 'desc')
            ->first();
    }

    public function renderSaleNewDetail(string $id)
    {
        $threeDaysAgo = Carbon::now()->subDays(3);
        // dd($get_data_7subcategory);
        try {
            $update_views = DB::table('sale_news')->where('sale_new_id', $id)->increment('views', 1);

            $news = SaleNews::with(['channel', 'images', 'firstImage', 'category', 'sub_category'])
                ->where('sale_new_id', $id)
                ->where('approved', 1)->where('is_delete', null)->first();
            // dd($news);

            if (!is_null($news->channel_id)) {
                $data_count_news = DB::table('sale_news')->where('channel_id', $news->channel_id)->where('approved', 1)->where('status', 1)->where('is_delete', null)->count();
                $data_count_news_sold = DB::table('sale_news')->where('channel_id', $news->channel_id)->where('approved', 1)->where('status', 0)->where('is_delete', null)->count();
            }



            $get_user_phone = DB::table('users')->where('user_id', $news->user_id)->first();
            $get_data_7subcategory = SaleNews::with(['channel', 'images', 'firstImage', 'sub_category'])
                ->where('sale_news.status', 1)
                ->whereNull('sale_news.is_delete')
                ->where('sale_news.vip_package_id', '!=', null)
                ->where('sale_news.approved', 1)
                ->where('sale_news.price', '>', 0)
                ->join('users', 'sale_news.user_id', '=', 'users.user_id')
                ->select('sale_news.*', 'users.created_at as user_created_at')
                // Sắp xếp các sản phẩm có vip_package_id lên đầu
                ->orderByRaw("CASE WHEN sub_category_id = ? THEN 0 ELSE 1 END", [$news->sub_category_id])
                ->orderByRaw("CASE WHEN users.created_at >= ? THEN 0 ELSE 1 END", [$threeDaysAgo])
                // Sắp xếp theo thời gian tạo
                ->orderBy('sale_news.created_at', 'desc')

                // Lấy kết quả ngẫu nhiên
                ->inRandomOrder()
                ->take(7)
                ->get();
            // dd($get_data_7subcategory);



            if ($news) {
                $data = $news->data;
                $data_json = json_decode($data);
                $nextNews = $this->getNextSaleNewId($id);
                $prevNews = $this->getPreviousSaleNewId($id);
                $nextNewsId = $nextNews ? $nextNews->sale_new_id : null;
                $prevNewsId = $prevNews ? $prevNews->sale_new_id : null;

                //  dd($data_json);
                if (!is_null($news->channel_id)) {
                    return view('salenews.detail', [
                        'new' => $news,
                        'get_user' => $get_user_phone,
                        'data_count_news' => $data_count_news,
                        'data_count_news_sold' => $data_count_news_sold,
                        'data_json' => $data_json,

                        'get_data_7subcategory' => $get_data_7subcategory,
                        'nextNewsId' => $nextNewsId,
                        'prevNewsId' => $prevNewsId
                    ]);
                }

                return view('salenews.detail', [
                    'new' => $news,
                    'get_user' => $get_user_phone,
                    'data_json' => $data_json,
                    'get_data_7subcategory' => $get_data_7subcategory,
                    'nextNewsId' => $nextNewsId,
                    'prevNewsId' => $prevNewsId
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


    public function list_salenew()
    {
        $data = SaleNews::with('user', 'sub_category.category', 'images')
            ->where('is_delete', NULL)
            ->get();


        // Sử dụng withCount để đếm số lượng tin có trạng thái = 0
        $count = SaleNews::where('approved', 0)->count();

        return view('admin.sale_new.sale_new_list', compact('data', 'count'));
    }

    public function reject($id)
    {
        try {
            $item = SaleNews::findOrFail($id);

            // Thay đổi trạng thái giữa 0 và 2
            $item->approved = $item->approved == 2 ? 0 : 2;
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

    public function approve($id)
    {
        try {
            $item = SaleNews::findOrFail($id);

            // Thay đổi trạng thái giữa 0 và 1
            $item->approved = $item->approved == 1 ? 0 : 1;
            $item->save();

            return redirect()->back()->with('alert', [
                'type' => 'success',
                'message' => ' Approve successfully!'
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            $item = SaleNews::findOrFail($id);

            // Nếu is_delete là NULL, gán giá trị là 1
            if (is_null($item->is_delete)) {
                $item->is_delete = 1;
            }

            // Chuyển trạng thái approved thành 1
            $item->approved = 2;

            $item->save();

            // Thông báo
            $message = 'Delete successfully!';

            return redirect()->back()->with('alert', [
                'type' => 'success',
                'message' => $message
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: ' . $th->getMessage()
            ]);
        }
    }



    public function navbar_sale()
    {
        $count = SaleNews::where('approved', 0)->get();

        return view('layouts.admin', compact('count'));
    }
    public function confirmedSale($id)
    {


        $sale = SaleNews::find($id);
        if ($sale) {
            // $sale->status = 'sold';
            // $sale->save();
            $confirmed = DB::table('sale_news')->where('sale_new_id', $id)->update(['status' => 0]);
        }

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => 'Operation successful'
        ]);
    }
    public function search(Request $request)
    {

        $maxPriceRange = SaleNews::max('price');

        $keyword = $request->input('keyword');
        $categoryId = $request->get('category'); // Get category filter
        $address = $request->get('address');    // Get address filter
        $categoryId = $request->get('category'); // Get category filter
        // $keyword = $request->input('keyword');
        // $minPrice = $request->get('minPrice');
        // $maxPrice = $request->get('maxPrice');


        $subcategoryID = $request->get('subcategory');    // Get address filter
        // dd($subcategoryID);


        $minPrice = $request->get('minPrice') ?? 0;
        $maxPrice = $request->get('maxPrice') ?? $maxPriceRange;

        // dd($minPrice);


        $subcategoryID = $request->get('subcategory');    // Get address filter
        // dd($subcategoryID);

        $threeDaysAgo = Carbon::now()->subDays(3);

        // Recent VIP SaleNews (users created within the last 3 days)
        $recentVipSaleNews = SaleNews::where('title', 'like', "%$keyword%")
            ->with('categoryToSubcategory', 'user', 'sub_category.category')
            ->whereNotNull('vip_package_id')
            ->where('status', 1)
            ->where('approved', 1)
            ->where('is_delete', null)
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->whereHas('user', function ($query) use ($threeDaysAgo) {
                $query->where('created_at', '>=', $threeDaysAgo);
            });
        // if (!empty($subcategoryID)) {
        //     $recentVipSaleNews->where('sub_category_id', $subcategoryID);
        // }
        if ($categoryId) {
            $recentVipSaleNews->whereHas('sub_category.category', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }

        if ($address) {
            $recentVipSaleNews->where('address', 'like', "%$address%");
        }
        $recentVipSaleNews = $recentVipSaleNews->inRandomOrder()->get();

        // Older VIP SaleNews (users created more than 3 days ago)
        $olderVipSaleNews = SaleNews::where('title', 'like', "%$keyword%")
            ->with('categoryToSubcategory', 'user', 'sub_category.category')
            ->whereNotNull('vip_package_id')
            ->where('status', 1)
            ->where('approved', 1)
            ->where('is_delete', null)
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->whereHas('user', function ($query) use ($threeDaysAgo) {
                $query->where('created_at', '<', $threeDaysAgo);
            });
        // if (!empty($subcategoryID)) {
        //     $olderVipSaleNews->where('sub_category_id', $subcategoryID);
        // }
        if ($categoryId) {
            $olderVipSaleNews->whereHas('sub_category.category', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }

        if ($address) {
            $olderVipSaleNews->where('address', 'like', "%$address%");
        }
        $olderVipSaleNews = $olderVipSaleNews->inRandomOrder()->get();
        // Non-VIP SaleNews with pagination
        $perPage = $request->get('perPage', 8);
        $nonVipSaleNews = SaleNews::where('title', 'like', "%$keyword%")
            ->with('sub_category.category')
            ->whereNull('vip_package_id')
            ->where('status', 1)
            ->where('is_delete', null)
            ->where('approved', 1)
            ->whereBetween('price', [$minPrice, $maxPrice]);

        if ($categoryId) {
            $nonVipSaleNews->whereHas('sub_category.category', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }

        if ($address) {
            $nonVipSaleNews->where('address', 'like', "%$address%");
        }

        $nonVipSaleNews = $nonVipSaleNews->inRandomOrder()->paginate($perPage);

        $totalNonVipSaleNews = $nonVipSaleNews->total();

        $category = Category::all();


        return view('salenews.search', compact(
            'recentVipSaleNews',
            'olderVipSaleNews',
            'nonVipSaleNews',
            'keyword',
            'perPage',
            'totalNonVipSaleNews',
            'category',
            'address',
            'categoryId',
            'maxPriceRange'
        ));
    }
    public function all_sale_news(Request $request)
    {
        $currentCategoryId = $request->input('category', 'all');

        // Lấy danh mục với số lượng tin liên quan
        $categories = Category::with(['subcategories.salenews' => function ($query) {
            $query->where('status', 1)
                ->where('approved', 1)
                ->where('is_delete', null);
        }])
            ->select('category_id', 'name_category', 'image_category')
            ->get()
            ->map(function ($category) {
                $category->news_count = $category->subcategories->flatMap->salenews->count();
                return $category;
            });
        $items = SaleNews::with(['user', 'sub_category.category', 'images'])
            ->where('status', 1)
            ->where('approved', 1)
            ->where('is_delete', null)
            ->when($currentCategoryId !== 'all', function ($query) use ($currentCategoryId) {
                $query->whereHas('sub_category.category', function ($q) use ($currentCategoryId) {
                    $q->where('category_id', $currentCategoryId);
                });
            })
            ->paginate(8);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.sale-news-items', compact('items'))->render(),
                'pagination' => (string)$items->links(),
            ]);
        }

        return view('salenews.all-sale-news', compact('categories', 'items', 'currentCategoryId'));
    }




    public function trash()
    {

        $data = SaleNews::with('vipPackage', 'images', 'firstImage', 'sub_category')
            ->where('is_delete', 1)->get();
        return view('admin.trash.sale-news', compact('data'));
    }
    public function restore($id)
    {
        try {
            $item = SaleNews::findOrFail($id);

            // Thay đổi trạng thái giữa 0 và 2
            $item->is_delete = null;
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
        $check = SaleNews::findOrFail($id);
        if ($check) {
            foreach ($check->images as $photo) {
                $filePath = public_path($photo->image_name);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $photo->delete();
            }
            $check->likes()->delete();
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


    public function search_category(Request $request)
    {
        $maxPriceRange = SaleNews::max('price');
        $categoryId = $request->get('category');
        $subcategoryID = $request->get('subcategory');
        $threeDaysAgo = Carbon::now()->subDays(3);

        $recentVipSaleNews = SaleNews::with('categoryToSubcategory', 'user', 'sub_category.category')
            ->whereNotNull('vip_package_id')
            ->where('status', 1)
            ->where('approved', 1)
            ->where('is_delete', null)
            ->whereHas('user', function ($query) use ($threeDaysAgo) {
                $query->where('created_at', '>=', $threeDaysAgo);
            });
        if ($subcategoryID) {
            $recentVipSaleNews->where('sub_category_id', $subcategoryID);
        }
        if ($categoryId) {
            $recentVipSaleNews->whereHas('sub_category.category', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }
        $recentVipSaleNews = $recentVipSaleNews->inRandomOrder()->get();
        $olderVipSaleNews = SaleNews::with('categoryToSubcategory', 'user', 'sub_category.category')
            ->whereNotNull('vip_package_id')
            ->where('status', 1)
            ->where('approved', 1)
            ->where('is_delete', null)
            ->whereHas('user', function ($query) use ($threeDaysAgo) {
                $query->where('created_at', '<', $threeDaysAgo);
            });
        if ($subcategoryID) {
            $olderVipSaleNews->where('sub_category_id', $subcategoryID);
        }
        if ($categoryId) {
            $olderVipSaleNews->whereHas('sub_category.category', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }
        $olderVipSaleNews = $olderVipSaleNews->inRandomOrder()->get();
        // Non-VIP SaleNews with pagination
        $perPage = $request->get('perPage', 8);
        $nonVipSaleNews = SaleNews::with('sub_category.category')
            ->whereNull('vip_package_id')
            ->where('status', 1)
            ->where('is_delete', null)
            ->where('approved', 1);
        if ($subcategoryID) {
            $nonVipSaleNews->where('sub_category_id', $subcategoryID);
        }
        if ($categoryId) {
            $nonVipSaleNews->whereHas('sub_category.category', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }
        $nonVipSaleNews = $nonVipSaleNews->inRandomOrder()->paginate($perPage);

        $totalNonVipSaleNews = $nonVipSaleNews->total();

        $category = Category::all();

        $keyword = null;
        $address = null;
        return view('salenews.search', compact(
            'recentVipSaleNews',
            'olderVipSaleNews',
            'nonVipSaleNews',
            'keyword',
            'perPage',
            'totalNonVipSaleNews',
            'category',
            'address',
            'categoryId',
            'maxPriceRange'
        ));
    }
    public function toggleStatus($id)
    {

        try {
            $sale_news = SaleNews::findOrFail($id);
            $sale_news->status = $sale_news->status == 1 ? 0 : 1;
            $sale_news->save();

            $statusMessage = $sale_news->status == 1 ? 'Active' : 'Inactive';

            // Thêm thông báo vào session sau khi thay đổi trạng thái
            session()->flash('alert', [
                'type' => 'success',
                'message' => "Status has been updated to {$statusMessage}!"
            ]);

            // Redirect về trang trước đó (không trả về JSON nữa)
            return redirect()->back();
        } catch (\Exception $e) {
            // Lưu thông báo lỗi vào session
            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Error!: ' . $e->getMessage()
            ]);

            // Redirect về trang trước đó (không trả về JSON nữa)
            return redirect()->back();
        }
    }
}
