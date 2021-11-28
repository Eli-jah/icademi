<?php

namespace App\Admin\Controllers;

use App\Models\School;
use App\Models\SchoolUser;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\DropdownActions;
use Encore\Admin\Grid\Tools as GridTools;
use Encore\Admin\Grid\Tools\BatchActions;
use Encore\Admin\Show;
use Encore\Admin\Show\Tools as ShowTools;
use Illuminate\Support\Carbon;

class SchoolsController extends Controller
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
        if ($this->isAdministrator()) {
            // $grid->disableActions();
            // $grid->disableExport();
            // $grid->disableFilter();
            $grid->disableCreateButton();
            // $grid->disablePagination();
            // $grid->disableRowSelector();
            $grid->actions(function (DropdownActions $actions) {
                // $actions->disableView();
                // $actions->disableEdit();
                $actions->disableDelete();
            });
            $grid->tools(function (GridTools $tools) {
                $tools->batch(function (BatchActions $batch) {
                    $batch->disableDelete();
                });
            });
        } else if ($this->isTeacher()) {
            $grid->model()
                ->select([
                    'schools.*',
                ])
                ->join('school_user', 'schools.id', '=', 'school_user.school_id')
                ->where('school_user.user_id', $this->teacherId);

            // $grid->disableActions();
            // $grid->disableExport();
            // $grid->disableFilter();
            // $grid->disableCreateButton();
            // $grid->disablePagination();
            // $grid->disableRowSelector();
            $teacher_id = $this->teacherId;
            $grid->actions(function (DropdownActions $actions) use ($teacher_id) {
                // $actions->disableAll();
                // $actions->disableView();
                // $current_row_id = $this->getKey();
                // $current_row_id = $this->getRouteKey();
                if ($teacher_id != $this->getAttribute('user_id')) {
                    $actions->disableEdit();
                    $actions->disableDelete();
                }
            });
            $grid->tools(function (GridTools $tools) {
                $tools->batch(function (BatchActions $batch) {
                    $batch->disableDelete();
                });
            });
        } else {
            $grid->disableActions();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableCreateButton();
            $grid->disablePagination();
            $grid->disableRowSelector();
            $grid->actions(function (DropdownActions $actions) {
                $actions->disableView();
                $actions->disableEdit();
                $actions->disableDelete();
            });
            $grid->tools(function (GridTools $tools) {
                $tools->batch(function (BatchActions $batch) {
                    $batch->disableDelete();
                });
            });
        }

        // $grid->column('id', __('Id'));
        // $grid->column('user_id', __('User id'));
        $grid->column('name', __('School Name'));
        $grid->column('founder_name', __('Founder'));
        $grid->column('student_count', __('Student Number'));
        if ($this->isAdministrator()) {
            $states = [
                'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
            ];
            $grid->column('is_approved', 'Is Approved')->switch($states);
        } else {
            $grid->column('is_approved', __('Is Approved'))->display(function ($is_approved) {
                return $is_approved ? '<span class="label label-primary">YES</span>' : '<span class="label label-default">NO</span>';
            });
        }
        // $grid->column('approved_at', __('Approved at'));
        // $grid->column('rejected_at', __('Rejected at'));
        // $grid->column('created_at', __('Created at'));
        // $grid->column('updated_at', __('Updated at'));

        $is_teacher = $this->isTeacher();
        $teacher_id = $this->teacherId;

        if ($is_teacher) {
            $grid->column('', __('Your Title'))->display(function () use ($teacher_id) {
                return $teacher_id == $this->user_id ? 'Founder' : 'Teacher';
            });
            $grid->column('', __('Student Management'))->display(function () {
                return '<a class="btn btn-xs btn-primary" style="margin-right:8px" href="' . route('admin.students.index', [
                        'school_id' => $this->id,
                    ]) . '">Student Management</a>';
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
        $show = new Show(School::findOrFail($id));

        if ($this->isTeacher()) {
            // $school = $show->getModel();
            $is_working_at_the_school = SchoolUser::query()
                ->where('school_id', $id)
                ->where('user_id', $this->teacherId)
                ->first();
            if ($is_working_at_the_school) {
                if (!$is_working_at_the_school->is_founder) {
                    $show->panel()->tools(function (ShowTools $tools) {
                        $tools->disableEdit();
                        // $tools->disableList();
                        $tools->disableDelete();
                    });
                }
            } else {
                $this->shouldRedirect = true;
                $this->redirectUrl = route('admin.schools.index');
                // return redirect()->route('admin.schools.index');
                // return redirect()->back();
                return false;
            }
        } else if ($this->isAdministrator()) {
            $show->panel()->tools(function (ShowTools $tools) {
                // $tools->disableEdit();
                // $tools->disableList();
                $tools->disableDelete();
            });
        } else {
            $show->panel()->tools(function (ShowTools $tools) {
                $tools->disableEdit();
                // $tools->disableList();
                $tools->disableDelete();
            });
        }

        // $show->field('id', __('Id'));
        // $show->field('user_id', __('Founder ID'));
        $show->field('founder_name', __('Founder'));
        $show->field('name', __('School Name'));
        $show->field('student_count', __('Student Number'));
        $show->field('is_approved', __('Is Approved'))->as(function ($value) {
            return $value ? 'YES' : 'NO';
        });
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

        if ($this->isTeacher()) {
            /*if ($form->isCreating()) {
                return redirect()->back();
            }*/
            if ($form->isEditing()) {
                $school = School::query()->find($this->modelId);
                if ($school->user_id != $this->teacherId) {
                    $this->shouldRedirect = true;
                    $this->redirectUrl = route('admin.schools.index');
                    // return redirect()->back();
                    return false;
                }
            }
            $form->tools(function (Form\Tools $tools) {
                // $tools->disableDelete();
                // $tools->disableList();
                // $tools->disableView();
            });

            // $form->number('user_id', __('User id'));
            $form->hidden('user_id', __('Founder ID'))->value($this->teacherId);
            $form->text('name', __('School Name'))->required(true)->placeholder('Your school name ...');
        } else if ($this->isAdministrator()) {
            if ($form->isCreating()) {
                $this->shouldRedirect = true;
                $this->redirectUrl = route('admin.schools.index');
                // return redirect()->back();
                return false;
            }
            /*if ($form->isEditing()) {
                return redirect()->back();
            }*/
            $form->tools(function (Form\Tools $tools) {
                $tools->disableDelete();
                // $tools->disableList();
                // $tools->disableView();
            });

            // $form->number('user_id', __('User id'));
            // $form->display('user_id', __('Founder ID'));
            $form->display('founder_name', __('Founder'));
            $form->display('name', __('School Name'));
            $form->display('student_count', __('Student Number'));;
            $states = [
                'on' => ['value' => 1, 'text' => 'YES', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'NO', 'color' => 'danger'],
            ];
            $form->switch('is_approved', __('Is Approved'))->states($states)->default(0);
        } else {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.schools.index');
            // return redirect()->back();
            return false;
        }

        // $form->datetime('approved_at', __('Approved at'))->default(date('Y-m-d H:i:s'));
        // $form->datetime('rejected_at', __('Rejected at'))->default(date('Y-m-d H:i:s'));

        $form->saving(function (Form $form) {
            //
        });

        $form->saved(function (Form $form) {
            $data = [];
            if (request()->input('is_approved') == 'on') {
                $data['approved_at'] = Carbon::now()->toDateTimeString();
                $data['rejected_at'] = null;
            } else if (request()->input('is_approved') == 'on') {
                $data['approved_at'] = null;
                $data['rejected_at'] = Carbon::now()->toDateTimeString();
            }
            if (!empty($data)) {
                $form->model()->update($data);
            }
        });

        return $form;
    }
}
