<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;

class Controller extends AdminController
{
    protected $modelId = false;
    protected $shouldRedirect = false;
    protected $redirectUrl = false;
    protected $adminUser = false;
    protected $adminRole = false;
    protected $adminId = false;
    protected $teacherId = false;
    protected $schoolId = false;

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = $this->grid();
        if ($this->shouldRedirect) {
            return redirect()->to($this->redirectUrl);
        }
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($grid);
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        $this->modelId = $id;
        $detail = $this->detail($id);
        if ($this->shouldRedirect) {
            return redirect()->to($this->redirectUrl);
        }
        return $content
            ->title($this->title())
            ->description($this->description['show'] ?? trans('admin.show'))
            ->body($detail);
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $this->modelId = $id;
        $form = $this->form();
        if ($this->shouldRedirect) {
            return redirect()->to($this->redirectUrl);
        }
        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($form->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        $form = $this->form();
        if ($this->shouldRedirect) {
            return redirect()->to($this->redirectUrl);
        }
        return $content
            ->title($this->title())
            ->description($this->description['create'] ?? trans('admin.create'))
            ->body($form);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $form = $this->form();
        if ($this->shouldRedirect) {
            return redirect()->to($this->redirectUrl);
        }
        return $form->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        $form = $this->form();
        if ($this->shouldRedirect) {
            return redirect()->to($this->redirectUrl);
        }
        return $form->store();
    }

    /**
     * Determine whether current admin user's role is 'Administrator'.
     *
     * @return bool $isAdministrator
     */
    protected function isAdministrator()
    {
        $admin_user = Admin::user();
        // $admin_user = Admin::guard()->user();
        $this->adminUser = $admin_user;
        $this->adminId = $admin_user->id;
        $this->adminRole = $admin_user->roles()->first();
        $admin_roles_table = config('admin.database.roles_table');
        /*$sql = $admin_user->roles()
            ->where($admin_roles_table . '.id', 1)
            ->toSql();
        dd($sql);*/
        return $admin_user->roles()
            ->where($admin_roles_table . '.id', 1)
            ->exists();
    }

    /**
     * Determine whether current admin user's role is 'Teacher'.
     *
     * @return bool $isTeacher
     */
    protected function isTeacher()
    {
        $admin_user = Admin::user();
        // $admin_user = Admin::guard()->user();
        $this->adminUser = $admin_user;
        $this->adminId = $admin_user->id;
        $this->adminRole = $admin_user->roles()->first();
        $hasTeacherId = is_integer($admin_user->user_id);
        $admin_roles_table = config('admin.database.roles_table');
        /*$sql = $admin_user->roles()
            ->where($admin_roles_table . '.id', 2)
            ->toSql();
        dd($sql);*/
        $isTeacher = $admin_user->roles()
            ->where($admin_roles_table . '.id', 2)
            ->exists();
        if ($hasTeacherId && $isTeacher) {
            $this->teacherId = $admin_user->user_id;
            return true;
        }
        return false;
    }
}