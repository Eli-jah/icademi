<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Teacher;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\DropdownActions;
use Encore\Admin\Grid\Tools as GridTools;
use Encore\Admin\Grid\Tools\BatchActions;
use Encore\Admin\Show;
use Encore\Admin\Show\Tools as ShowTools;

class TeachersController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Teacher';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Teacher());

        $is_teacher = $this->isTeacher();
        $teacher_id = $this->teacherId;

        // $grid->disableActions();
        // $grid->disableExport();
        // $grid->disableFilter();
        $grid->disableCreateButton();
        // $grid->disablePagination();
        // $grid->disableRowSelector();
        $grid->actions(function (DropdownActions $actions) use ($is_teacher, $teacher_id) {
            // $actions->disableAll();
            // $actions->disableView();
            $current_row_id = $this->getKey();
            // $current_row_id = $this->getRouteKey();
            if (!$is_teacher || $teacher_id != $current_row_id) {
                $actions->disableEdit();
            }
            $actions->disableDelete();
        });
        $grid->tools(function (GridTools $tools) {
            $tools->batch(function (BatchActions $batch) {
                $batch->disableDelete();
            });
        });

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'))->copyable();
        // $grid->column('password', __('Password'));
        // $grid->column('random_code', __('Random code'));
        // $grid->column('avatar', __('Avatar'));
        // $grid->avatar('Avatar')->image('', 40);
        $grid->column('avatar', __('Avatar'))->image('', 40);
        // $grid->column('remember_token', __('Remember token'));
        // $grid->column('created_at', __('Created at'));
        // $grid->column('updated_at', __('Updated at'));
        $grid->column('founded_school_count', 'Founded School Number');
        $grid->column('is_a_school_founder', __('Is A School Founder'))->display(function ($is_a_school_founder) {
            return $is_a_school_founder ? '<span class="label label-primary">YES</span>' : '<span class="label label-default">NO</span>';
        });

        if ($is_teacher) {
            $grid->column('', 'Send Email')->display(function () use ($is_teacher, $teacher_id) {
                if ($is_teacher && $teacher_id != $this->id && !$this->is_a_school_founder) {
                    return '<a class="btn btn-xs btn-primary" style="margin-right:8px" href="' . route('admin.invitations.create', [
                            'recipient_id' => $this->id,
                        ]) . '">Send Email</a>';
                }
            });
        }

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Teacher::findOrFail($id));

        $is_teacher = $this->isTeacher();
        $teacher_id = $this->teacherId;

        $show->panel()->tools(function (ShowTools $tools) use ($id, $is_teacher, $teacher_id) {
            if (!$is_teacher || $teacher_id != $id) {
                $tools->disableEdit();
            }
            // $tools->disableList();
            $tools->disableDelete();
        });

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        // $show->field('password', __('Password'));
        // $show->field('random_code', __('Random code'));
        // $show->field('avatar', __('Avatar'));
        // $show->avatar('头像')->image('', 120);
        $show->field('avatar', __('Avatar'))->image('', 120);
        // $show->field('remember_token', __('Remember token'));
        // $show->field('created_at', __('Created at'));
        // $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Teacher());

        $id = false;
        $is_teacher = $this->isTeacher();
        $teacher_id = $this->teacherId;

        if ($form->isCreating()) {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.teachers.index');
            // return redirect()->route('admin.teachers.index');
            // return redirect()->back();
            return false;
        } else if ($form->isEditing()) {
            $id = $this->modelId;
            if (!$is_teacher || $teacher_id != $this->modelId) {
                $this->shouldRedirect = true;
                $this->redirectUrl = route('admin.teachers.index');
                // return redirect()->route('admin.teachers.index');
                // return redirect()->back();
                return false;
            }
            // return redirect()->route('admin.teachers.index');
            // return redirect()->back();
        } else {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.teachers.index');
            // return redirect()->route('admin.teachers.index');
            // return redirect()->back();
            return false;
        }

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            // $tools->disableList();
            // $tools->disableView();
        });

        $form->text('name', __('Name'));
        // $form->display('name', __('Name'));
        // $form->email('email', __('Email'));
        $form->display('email', __('Email'));
        // $form->password('password', __('Password'));
        // $form->text('random_code', __('Random code'));
        $form->image('avatar', __('Avatar'))
            ->uniqueName()
            ->move('avatar')
            ->rules('required|image');
        // $form->text('remember_token', __('Remember token'));

        $form->saving(function (Form $form) {
            //
        });

        $form->saved(function (Form $form) use ($is_teacher, $teacher_id) {
            $id = $form->model()->id;
            if ($id && $is_teacher && $id == $teacher_id) {
                Administrator::query()
                    ->where('user_id', $teacher_id)
                    ->update([
                        'name' => $form->model()->name,
                        'avatar' => $form->model()->avatar,
                    ]);
            }
        });

        return $form;
    }
}
