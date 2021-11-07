<?php

namespace App\Observers;

use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;

class UserObserver
{
    /*Eloquent 的模型触发了几个事件，可以在模型的生命周期的以下几点进行监控：
    retrieved、creating、created、updating、updated、saving、saved、deleting、deleted、restoring、restored
    事件能在每次在数据库中保存或更新特定模型类时轻松地执行代码。*/

    /*当模型已存在，不是新建的时候，依次触发的顺序是:
    saving -> updating -> updated -> saved(不会触发保存操作)
    当模型不存在，需要新增的时候，依次触发的顺序则是:
    saving -> creating -> created -> saved(不会触发保存操作)*/

    public function creating(User $user)
    {
        // 这样写扩展性更高，只有空的时候才指定默认头像
        if (empty($user->avatar)) {
            $user->avatar = asset('defaults/default_avatar.jpeg');
        }
    }

    public function created(User $user)
    {
        // to create an admin user.
        $admin_user = Administrator::query()->create([
            'user_id' => $user->id,
            'username' => $user->email,
            'password' => $user->password,
            'name' => $user->name,
            'avatar' => $user->avatar,
        ]);

        // to add role to this admin user.
        $admin_user->roles()->save(Role::query()->find(2));
    }

    // Not working, actually.
    /*public function updated(User $user)
    {
        // to update the relative admin user.
        $admin_user = Administrator::query()
            ->where('user_id', $user->id)
            ->update([
                'username' => $user->email,
                'password' => $user->password,
                'name' => $user->name,
                'avatar' => $user->avatar,
            ]);
    }*/
}