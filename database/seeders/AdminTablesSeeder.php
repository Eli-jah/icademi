<?php

// namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        \Encore\Admin\Auth\Database\Menu::truncate();
        \Encore\Admin\Auth\Database\Menu::insert(
            [
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "title" => "Dashboard",
                    "icon" => "fa-bar-chart",
                    "uri" => "/",
                    "permission" => NULL,
                ],
                [
                    "parent_id" => 0,
                    "order" => 6,
                    "title" => "Admin",
                    "icon" => "fa-tasks",
                    "uri" => "",
                    "permission" => NULL,
                ],
                [
                    "parent_id" => 2,
                    "order" => 7,
                    "title" => "Users",
                    "icon" => "fa-users",
                    "uri" => "auth/users",
                    "permission" => NULL,
                ],
                [
                    "parent_id" => 2,
                    "order" => 8,
                    "title" => "Roles",
                    "icon" => "fa-user",
                    "uri" => "auth/roles",
                    "permission" => NULL,
                ],
                [
                    "parent_id" => 2,
                    "order" => 9,
                    "title" => "Permission",
                    "icon" => "fa-ban",
                    "uri" => "auth/permissions",
                    "permission" => NULL,
                ],
                [
                    "parent_id" => 2,
                    "order" => 10,
                    "title" => "Menu",
                    "icon" => "fa-bars",
                    "uri" => "auth/menu",
                    "permission" => NULL,
                ],
                [
                    "parent_id" => 2,
                    "order" => 11,
                    "title" => "Operation log",
                    "icon" => "fa-history",
                    "uri" => "auth/logs",
                    "permission" => NULL,
                ],
                [
                    "parent_id" => 0,
                    "order" => 2,
                    "title" => "Teacher Management",
                    "icon" => "fa-user-secret",
                    "uri" => "teachers",
                    "permission" => "auth.teacher",
                ],
                [
                    "parent_id" => 0,
                    "order" => 3,
                    "title" => "School Management",
                    "icon" => "fa-bank",
                    "uri" => "schools",
                    "permission" => "auth.school",
                ],
                [
                    "parent_id" => 0,
                    "order" => 4,
                    "title" => "Student Management",
                    "icon" => "fa-users",
                    "uri" => "students",
                    "permission" => "auth.student",
                ],
                [
                    "parent_id" => 0,
                    "order" => 5,
                    "title" => "Invitation Management",
                    "icon" => "fa-briefcase",
                    "uri" => "invitations",
                    "permission" => "auth.invitation",
                ],
            ]
        );

        \Encore\Admin\Auth\Database\Permission::truncate();
        \Encore\Admin\Auth\Database\Permission::insert(
            [
                [
                    "name" => "All permission",
                    "slug" => "*",
                    "http_method" => "",
                    "http_path" => "*",
                ],
                [
                    "name" => "Dashboard",
                    "slug" => "dashboard",
                    "http_method" => "GET",
                    "http_path" => "/",
                ],
                [
                    "name" => "Login",
                    "slug" => "auth.login",
                    "http_method" => "",
                    "http_path" => "/auth/login\r\n/auth/logout",
                ],
                [
                    "name" => "User setting",
                    "slug" => "auth.setting",
                    "http_method" => "GET,PUT",
                    "http_path" => "/auth/setting",
                ],
                [
                    "name" => "Auth management",
                    "slug" => "auth.management",
                    "http_method" => "",
                    "http_path" => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
                ],
                [
                    "name" => "Teacher Management",
                    "slug" => "auth.teacher",
                    "http_method" => "GET,POST,PUT,PATCH,OPTIONS,HEAD",
                    "http_path" => "/teachers\r\n/teachers/*",
                ],
                [
                    "name" => "School Management",
                    "slug" => "auth.school",
                    "http_method" => "",
                    "http_path" => "/schools\r\n/schools/*",
                ],
                [
                    "name" => "Student Management",
                    "slug" => "auth.student",
                    "http_method" => "",
                    "http_path" => "/students\r\n/students/*",
                ],
                [
                    "name" => "Invitation Management",
                    "slug" => "auth.invitation",
                    "http_method" => "",
                    "http_path" => "/invitations\r\n/invitations/*",
                ],
            ]
        );

        \Encore\Admin\Auth\Database\Role::truncate();
        \Encore\Admin\Auth\Database\Role::insert(
            [
                [
                    "name" => "Administrator",
                    "slug" => "administrator",
                ],
                [
                    "name" => "Teacher",
                    "slug" => "teacher",
                ],
            ]
        );

        // pivot tables
        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "role_id" => 1,
                    "menu_id" => 2,
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 8,
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 9,
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 10,
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 11,
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 8,
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 9,
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 10,
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 11,
                ],
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [
                [
                    "role_id" => 1,
                    "permission_id" => 1,
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 2,
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 3,
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 4,
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 6,
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 7,
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 8,
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 9,
                ],
            ]
        );

        // users tables
        \Encore\Admin\Auth\Database\Administrator::truncate();
        \Encore\Admin\Auth\Database\Administrator::insert(
            [
                [
                    "username" => "admin",
                    "password" => "\$2y\$10\$Y1pIqWsxb6vaWoev7ORUjeB553OCnA.VaKFZ4k8mIQ4zQfvEZtjyu",
                    "name" => "Administrator",
                    "avatar" => NULL,
                    "remember_token" => "zd5QqlnUYA5bIdIfe2g3QXPhBD54b4KH5baBkXxhOhbuA2nIobAELdAcyZik",
                ],
            ]
        );

        DB::table('admin_role_users')->truncate();
        DB::table('admin_role_users')->insert(
            [
                [
                    "role_id" => 1,
                    "user_id" => 1,
                ],
            ]
        );

        DB::table('admin_user_permissions')->truncate();
        DB::table('admin_user_permissions')->insert(
            [

            ]
        );

        // finish
    }
}
