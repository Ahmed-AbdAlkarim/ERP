<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'guard_name'];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'model_has_roles', // الجدول اللي موجود عندك
            'role_id',          // عمود الدور
            'model_id'          // عمود المستخدم
        )->where('model_type', User::class); // مهم عشان يفرق بين أنواع الموديلات
    }



    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_has_permissions', // اسم جدول الربط الصحيح عند Spatie
            'role_id',             // المفتاح الخارجي في جدول الربط بالنسبة للـ Role
            'permission_id'        // المفتاح الخارجي في جدول الربط بالنسبة للـ Permission
        );
    }

}
