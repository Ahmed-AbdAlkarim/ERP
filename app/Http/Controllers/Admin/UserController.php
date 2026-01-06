<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->roles()->attach($request->role);

        $user->forgetCachedPermissions();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    
    public function show(string $id)
    {
        $user = User::with(['roles.permissions', 'permissions'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $user->roles()->sync([$request->role]);

        $user->forgetCachedPermissions();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // ممنوع تحذف نفسك
        if (auth()->id() == $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'لا يمكنك حذف المستخدم الحالي');
        }

        // فك الربط مع الأدوار والصلاحيات (مهم مع Spatie)
        $user->roles()->detach();
        $user->permissions()->detach();

        $user->delete();

        // تنظيف كاش الصلاحيات
        $user->forgetCachedPermissions();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

}
