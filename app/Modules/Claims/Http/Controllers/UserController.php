<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * User Management Controller (Admin Only)
 */
class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('claims::users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        return view('claims::users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'user_type' => 'nullable|array',
            'user_type.*' => 'in:مراجع,معاملات مالية',
            'active' => 'nullable',
            'permissions' => 'nullable|array',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->has('active');
        $data['permissions'] = $request->input('permissions', []);
        
        // Only save user_type if role is user, otherwise set to null
        if ($data['role'] === 'user') {
            $data['user_type'] = $request->input('user_type', []);
        } else {
            $data['user_type'] = null;
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        return view('claims::users.edit', compact('user'));
    }

    /**
     * Update user details
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'user_type' => 'nullable|array',
            'user_type.*' => 'in:مراجع,معاملات مالية',
            'active' => 'nullable',
            'permissions' => 'nullable|array',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['active'] = $request->has('active');
        $data['permissions'] = $request->input('permissions', []);
        
        // Only save user_type if role is user, otherwise set to null
        if ($data['role'] === 'user') {
            $data['user_type'] = $request->input('user_type', []);
        } else {
            $data['user_type'] = null;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * Remove user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
}
