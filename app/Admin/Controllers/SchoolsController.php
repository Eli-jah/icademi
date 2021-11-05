<?php

namespace App\Admin\Controllers;

use App\Models\School;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SchoolsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'School';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new School());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('name', __('Name'));
        // $grid->column('approved_at', __('Approved at'));
        // $grid->column('rejected_at', __('Rejected at'));
        // $grid->column('created_at', __('Created at'));
        // $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(School::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('name', __('Name'));
        // $show->field('approved_at', __('Approved at'));
        // $show->field('rejected_at', __('Rejected at'));
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
        $form = new Form(new School());

        $form->number('user_id', __('User id'));
        $form->text('name', __('Name'));
        // $form->datetime('approved_at', __('Approved at'))->default(date('Y-m-d H:i:s'));
        // $form->datetime('rejected_at', __('Rejected at'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
