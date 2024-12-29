<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFollowed;
use Illuminate\Support\Facades\Auth;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserManageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $followedChannels = UserFollowed::where('user_id', $userId)
            ->with('channel')
            ->get();
        $transactionCount = Transactions::where('user_id', $userId)->count();
        $createdAt = Auth::user()->created_at;
        $now = now();
        $diffInMonths =  (int) $createdAt->diffInMonths($now);
        $diffInDays = (int) $createdAt->diffInDays($now->copy()->subMonths($diffInMonths));
        $title = 'Profile - Good & Cheep';
        return view('user.manage', compact('followedChannels', 'transactionCount', 'title', 'diffInMonths', 'diffInDays'));
    }
    public function unfollow($id)
    {
        $userId = Auth::id();
        $follow = UserFollowed::where('channel_id', $id)->where('user_id', $userId)->first();

        if ($follow) {
            $follow->delete();
            return response()->json(['success' => true, 'message' => 'Unfollowed successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Channel not found or not followed'], 404);
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $emailRules = ['required', 'email', 'max:255'];

        if ($request->input('email') !== $user->email) {
            $emailRules[] = Rule::unique('users')->ignore($user->id);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => $emailRules,
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'image_user' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $updateData = [
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'phone_number' => $request->input('phone_number'),
            ];

            if ($request->hasFile('image_user')) {
                if ($user->image_user) {
                    $oldImagePath = public_path($user->image_user);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $imageName = 'user' . time() . '.' . $request->file('image_user')->extension();
                $image_userPath = $request->file('image_user')->storeAs('image_users', $imageName, 'public');
                $updateData['image_user'] = 'storage/' . $image_userPath;
            }

            DB::table('users')->where('user_id', $user->user_id)->update($updateData);

            // Thông báo thành công
            return redirect()->back()->with('alert', [
                'type' => 'success',
                'message' => 'Profile updated successfully!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'An error occurred while updating your profile. Please try again later.'
            ]);
        }
    }

    public function show($id){
        $user=User::findOrFail($id);
        $sale_news = $user->saleNews()->with('sub_category', 'firstImage')
       
        ->where('is_delete', null)
        ->where('approved',1)
        ->paginate(5);
      
        $sale_news1 = $user->saleNews()->with('sub_category', 'firstImage')
       
        ->where('is_delete', null)
        ->where('approved',1)
        ->where('status',1)
        ->paginate(5);
     
        
       
        return view('user.user-show',compact('user','sale_news','sale_news1'));
    }
}
