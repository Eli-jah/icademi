<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TeachersController extends AdminController
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

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        // $grid->column('password', __('Password'));
        // $grid->column('random_code', __('Random code'));
        $grid->column('avatar', __('Avatar'));
        // $grid->column('remember_token', __('Remember token'));
        // $grid->column('created_at', __('Created at'));
        // $grid->column('updated_at', __('Updated at'));

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

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        // $show->field('password', __('Password'));
        // $show->field('random_code', __('Random code'));
        $show->field('avatar', __('Avatar'));
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

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        // $form->password('password', __('Password'));
        // $form->text('random_code', __('Random code'));
        $form->image('avatar', __('Avatar'));
        // $form->text('remember_token', __('Remember token'));

        return $form;
    }
}
