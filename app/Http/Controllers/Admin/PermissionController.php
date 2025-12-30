<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    
    public function create()
    {
        return view('admin.permissions.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array|min:1',
            'permissions.*.name' => 'required|string|max:255|unique:permissions,name',
            'permissions.*.display_name' => 'required|string|max:255',
        ]);

        $createdCount = 0;
        foreach ($request->permissions as $permissionData) {
            Permission::create([
                'name' => $permissionData['name'], // الاسم للتحقق @can
                'display_name' => $permissionData['display_name'], // الاسم للعرض في الواجهة
                'guard_name' => 'web',
            ]);
            $createdCount++;
        }

        $message = $createdCount === 1
            ? 'تم إضافة الصلاحية بنجاح'
            : "تم إضافة {$createdCount} صلاحيات بنجاح";

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')->with('success', $message);
    }

    
    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')->with('success', 'تم تحديث الصلاحية بنجاح');
    }

   
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')->with('success', 'تم حذف الصلاحية بنجاح');
    }
}
