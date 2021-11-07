<?php

// namespace Encore\Admin\Auth\Database;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OldAdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create a user.
        Administrator::query()->truncate();
        Administrator::query()->create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'name' => 'Administrator',
        ]);

        // create a role.
        Role::query()->truncate();
        Role::query()->create([
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]);
        Role::query()->create([
            'name' => 'Teacher',
            'slug' => 'teacher',
        ]);

        // add role to user.
        Administrator::query()->first()->roles()->save(Role::query()->first());

        //create a permission
        Permission::query()->truncate();
        Permission::query()->insert([
            [
                'name' => 'All permission',
                'slug' => '*',
                'http_method' => '',
                'http_path' => '*',
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'http_method' => 'GET',
                'http_path' => '/',
            ],
            [
                'name' => 'Login',
                'slug' => 'auth.login',
                'http_method' => '',
                'http_path' => "/auth/login\r\n/auth/logout",
            ],
            [
                'name' => 'User setting',
                'slug' => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path' => '/auth/setting',
            ],
            [
                'name' => 'Auth management',
                'slug' => 'auth.management',
                'http_method' => '',
                'http_path' => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
            [
                'name' => 'Teacher Management',
                'slug' => 'auth.teacher',
                'http_method' => 'GET,POST,PUT',
                'http_path' => "/teachers\r\n/teachers/*",
            ],
            [
                'name' => 'School Management',
                'slug' => 'auth.school',
                'http_method' => '',
                'http_path' => "/schools\r\n/schools/*",
            ],
            [
                'name' => 'Student Management',
                'slug' => 'auth.student',
                'http_method' => '',
                'http_path' => "/students\r\n/students/*",
            ],
            [
                "name" => "Invitation Management",
                "slug" => "auth.invitation",
                "http_method" => "",
                "http_path" => "/invitations\r\n/invitations/*",
            ],
        ]);

        Role::query()->first()->permissions()->save(Permission::query()->first());
        Role::query()->find(2)->permissions()->save(Permission::query()->find(2));
        Role::query()->find(2)->permissions()->save(Permission::query()->find(3));
        Role::query()->find(2)->permissions()->save(Permission::query()->find(4));
        Role::query()->find(2)->permissions()->save(Permission::query()->find(6));
        Role::query()->find(2)->permissions()->save(Permission::query()->find(7));
        Role::query()->find(2)->permissions()->save(Permission::query()->find(8));
        Role::query()->find(2)->permissions()->save(Permission::query()->find(9));

        // add default menus.
        Menu::query()->truncate();
        Menu::query()->insert([
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Dashboard',
                'icon' => 'fa-bar-chart',
                'uri' => '/',
            ],
            [
                'parent_id' => 0,
                'order' => 5,
                'title' => 'Admin',
                'icon' => 'fa-tasks',
                'uri' => '',
            ],
            [
                'parent_id' => 2,
                'order' => 3,
                'title' => 'Users',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
            ],
            [
                'parent_id' => 2,
                'order' => 4,
                'title' => 'Roles',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
            ],
            [
                'parent_id' => 2,
                'order' => 5,
                'title' => 'Permission',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
            ],
            [
                'parent_id' => 2,
                'order' => 6,
                'title' => 'Menu',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
            ],
            [
                'parent_id' => 2,
                'order' => 7,
                'title' => 'Operation log',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Teacher Management',
                'icon' => 'fa-user-secret',
                'uri' => '/teachers',
                'permission' => 'auth.teacher',
            ],
            [
                'parent_id' => 0,
                'order' => 3,
                'title' => 'School Management',
                'icon' => 'fa-bank',
                'uri' => '/schools',
                'permission' => 'auth.school',
            ],
            [
                'parent_id' => 0,
                'order' => 4,
                'title' => 'Student Management',
                'icon' => 'fa-users',
                'uri' => '/students',
                'permission' => 'auth.student',
            ],
        ]);

        // add role to menu.
        Menu::query()->find(2)->roles()->save(Role::query()->first());
    }
}
