<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Claims\Models\ClaimEntity;
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
        $entityNames = ClaimEntity::pluck('name', 'id');
        return view('claims::users.index', compact('users', 'entityNames'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        return view('claims::users.create', ['entityGroups' => $this->entityGroups()]);
    }

    /**
     * Sub-entity lists for each main entity type (mirrors flow/options)
     */
    private function entityGroups()
    {
        return [
            'contracts' => ClaimEntity::whereNotIn('id', [36, 3, 12])->orderBy('name')->get(['id', 'name', 'metadata']),
            'insurance' => ClaimEntity::where('id', 36)->get(['id', 'name', 'metadata']),
            'comprehensive' => ClaimEntity::where('id', 12)->get(['id', 'name', 'metadata']),
            'ministry' => collect(),
        ];
    }

    /**
     * Build bound_flow array from request (returns null when not bound)
     */
    private function boundFlow(Request $request)
    {
        $type = $request->input('bound_entity_type');

        if ($type === 'waiting_lists') {
            $wlt = $request->input('bound_waiting_list_type');
            $hospital = $request->input('bound_hospital');
            $department = $request->input('bound_department');

            if (in_array($wlt, ['insurance', 'ministry'], true) && $hospital && $department) {
                return [
                    'entity_type' => 'waiting_lists',
                    'waiting_list_type' => $wlt,
                    'hospital' => $hospital,
                    'department' => $department,
                    'law' => ($wlt === 'insurance') ? ($request->input('bound_law') ?: null) : null,
                ];
            }
        } elseif (in_array($type, ['contracts', 'insurance', 'ministry', 'comprehensive'], true)) {
            $entityId = ($type === 'ministry') ? 84 : (int) $request->input('bound_entity_id');
            if (!$entityId) {
                return null;
            }

            $flow = [
                'entity_type' => $type,
                'entity_id' => $entityId,
            ];

            // تأمين صحي فروع / شامل: يتطلب اختيار المحافظة
            if (in_array($type, ['insurance', 'comprehensive'], true)) {
                $location = $request->input('bound_location');
                if (!$location) {
                    return null;
                }
                $flow['location'] = $location;
            }

            // تأمين صحي فروع فقط: يتطلب اختيار المستفيد
            if ($type === 'insurance') {
                $law = $request->input('bound_law');
                if (!$law) {
                    return null;
                }
                $flow['law'] = $law;
            }

            return $flow;
        }

        return null;
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
            'can_view_all_data' => 'nullable',
            'permissions' => 'nullable|array',
            'bound_entity_type' => 'nullable|in:contracts,insurance,ministry,comprehensive,waiting_lists',
            'bound_entity_id' => 'nullable|integer|exists:claim_entities,id',
            'bound_location' => 'nullable|string',
            'bound_law' => 'nullable|string',
            'bound_waiting_list_type' => 'nullable|in:insurance,ministry',
            'bound_hospital' => 'nullable|string',
            'bound_department' => 'nullable|string',
            'bound_law' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->has('active');
        $data['permissions'] = $request->input('permissions', []);
        
        // Only save user_type if role is user, otherwise set to null
        if ($data['role'] === 'user') {
            $data['user_type'] = $request->input('user_type', []);
            $data['can_view_all_data'] = $request->has('can_view_all_data');
        } else {
            $data['user_type'] = null;
            $data['can_view_all_data'] = true;
        }

        $data['bound_flow'] = $this->boundFlow($request);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        return view('claims::users.edit', ['user' => $user, 'entityGroups' => $this->entityGroups()]);
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
            'can_view_all_data' => 'nullable',
            'permissions' => 'nullable|array',
            'bound_entity_type' => 'nullable|in:contracts,insurance,ministry,comprehensive,waiting_lists',
            'bound_entity_id' => 'nullable|integer|exists:claim_entities,id',
            'bound_location' => 'nullable|string',
            'bound_law' => 'nullable|string',
            'bound_waiting_list_type' => 'nullable|in:insurance,ministry',
            'bound_hospital' => 'nullable|string',
            'bound_department' => 'nullable|string',
            'bound_law' => 'nullable|string',
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
            $data['can_view_all_data'] = $request->has('can_view_all_data');
        } else {
            $data['user_type'] = null;
            $data['can_view_all_data'] = true;
        }

        $data['bound_flow'] = $this->boundFlow($request);

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
