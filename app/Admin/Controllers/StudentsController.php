<?php

namespace App\Admin\Controllers;

use App\Models\School;
use App\Models\SchoolUser;
use App\Models\Student;
use App\Models\StudentUser;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\DropdownActions;
use Encore\Admin\Grid\Tools as GridTools;
use Encore\Admin\Grid\Tools\BatchActions;
use Encore\Admin\Show;
use Encore\Admin\Show\Tools as ShowTools;

class StudentsController extends Controller
{
    protected $schoolId = false;
    protected $isSchoolFounder = false;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Student';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Student());

        if ($this->isAdministrator()) {
            // $grid->disableActions();
            // $grid->disableExport();
            // $grid->disableFilter();
            $grid->disableCreateButton();
            // $grid->disablePagination();
            // $grid->disableRowSelector();
            $grid->actions(function (DropdownActions $actions) {
                // $actions->disableView();
                $actions->disableEdit();
                $actions->disableDelete();
            });
            $grid->tools(function (GridTools $tools) {
                $tools->batch(function (BatchActions $batch) {
                    $batch->disableDelete();
                });
            });
        } else if ($this->isTeacher()) {
            $this->schoolId = request()->input('school_id');
            if ($this->schoolId) {
                $is_working_at_the_school = SchoolUser::query()
                    ->where('school_id', $this->schoolId)
                    ->where('user_id', $this->teacherId)
                    ->first();
                if ($is_working_at_the_school) {
                    if ($is_working_at_the_school->is_founder) {
                        $this->isSchoolFounder = true;
                        $grid->model()
                            ->where('school_id', $this->schoolId);
                    } else {
                        $grid->model()
                            ->select([
                                'students.*',
                            ])
                            ->join('student_user', 'students.id', '=', 'student_user.student_id')
                            ->where('student_user.user_id', $this->teacherId)
                            ->where('students.school_id', $this->schoolId);
                    }
                } else {
                    $this->shouldRedirect = true;
                    $this->redirectUrl = route('admin.schools.index');
                    // return redirect()->back();
                    return false;
                }
            } else {
                $is_working_at_the_school = SchoolUser::query()
                    ->where('user_id', $this->teacherId)
                    ->first();
                if ($is_working_at_the_school) {
                    $this->schoolId = $is_working_at_the_school->school_id;
                    if ($is_working_at_the_school->is_founder) {
                        $this->isSchoolFounder = true;
                        $grid->model()
                            ->where('school_id', $this->schoolId);
                    } else {
                        $grid->model()
                            ->select([
                                'students.*',
                            ])
                            ->join('student_user', 'students.id', '=', 'student_user.student_id')
                            ->where('student_user.user_id', $this->teacherId)
                            ->where('students.school_id', $this->schoolId);
                    }
                } else {
                    $this->shouldRedirect = true;
                    $this->redirectUrl = route('admin.schools.index');
                    // return redirect()->back();
                    return false;
                }
            }

            // $grid->disableActions();
            // $grid->disableExport();
            // $grid->disableFilter();
            $grid->disableCreateButton();
            // $grid->disablePagination();
            // $grid->disableRowSelector();
            $is_school_founder = $this->isSchoolFounder;
            $grid->actions(function (DropdownActions $actions) use ($is_school_founder) {
                // $actions->disableAll();
                // $actions->disableView();
                // $current_row_id = $this->getKey();
                // $current_row_id = $this->getRouteKey();
                if (!$is_school_founder) {
                    $actions->disableEdit();
                    $actions->disableDelete();
                }
            });
            $grid->tools(function (GridTools $tools) use ($is_school_founder) {
                $tools->batch(function (BatchActions $batch) use ($is_school_founder) {
                    $batch->disableDelete();
                });
                if ($is_school_founder) {
                    $create_route = route('admin.students.create', [
                        'school_id' => $this->schoolId,
                    ]);
                    $new = trans('admin.new');
                    $create_button = <<<EOT

<div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
    <a href="{$create_route}" class="btn btn-sm btn-success" title="{$new}">
        <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;{$new}</span>
    </a>
</div>

EOT;
                    $tools->append($create_button);
                    // $tools->prepend($create_button);
                }
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
        // $grid->column('school_id', __('School id'));
        $grid->column('school_name', __('School'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'))->copyable();
        $grid->column('avatar', __('Avatar'))->image('', 40);
        // $grid->column('password', __('Password'));
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
        $show = new Show(Student::findOrFail($id));

        if ($this->isTeacher()) {
            $student = $show->getModel();
            $school_id = $student->school_id;
            $is_a_creator = School::query()
                ->where('id', $student->school_id)
                ->where('user_id', $this->teacherId)
                ->exists();
            $is_followed = StudentUser::query()
                ->where('student_id', $id)
                ->where('user_id', $this->teacherId)
                ->exists();
            if ($is_a_creator) {
                $show->panel()->tools(function (ShowTools $tools) use ($id, $school_id) {
                    $tools->disableEdit();
                    $tools->disableList();
                    // $tools->disableDelete();
                    $edit_route = route('admin.students.edit', [
                        'student_id' => $id,
                        'school_id' => $school_id,
                    ]);
                    $edit = trans('admin.edit');
                    $edit_button = <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$edit_route}" class="btn btn-sm btn-primary" title="{$edit}">
        <i class="fa fa-edit"></i><span class="hidden-xs"> {$edit}</span>
    </a>
</div>
HTML;
                    $tools->append($edit_button);
                    $list_route = route('admin.students.index', [
                        'school_id' => $school_id,
                    ]);
                    $list = trans('admin.list');
                    $list_button = <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$list_route}" class="btn btn-sm btn-default" title="{$list}">
        <i class="fa fa-list"></i><span class="hidden-xs"> {$list}</span>
    </a>
</div>
HTML;
                    $tools->append($list_button);
                });
            } else if ($is_followed) {
                $show->panel()->tools(function (ShowTools $tools) {
                    $tools->disableEdit();
                    // $tools->disableList();
                    $tools->disableDelete();
                });
            } else {
                $this->shouldRedirect = true;
                $this->redirectUrl = route('admin.schools.index');
                // return redirect()->route('admin.schools.index');
                // return redirect()->back();
                return false;
            }
        } else {
            $show->panel()->tools(function (ShowTools $tools) {
                $tools->disableEdit();
                // $tools->disableList();
                $tools->disableDelete();
            });
        }

        // $show->field('id', __('Id'));
        // $show->field('school_id', __('School id'));
        $show->field('school_name', __('School'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('avatar', __('Avatar'))->image('', 120);
        // $show->field('password', __('Password'));
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
        $form = new Form(new Student());

        $is_editing = false;
        if ($this->isTeacher()) {
            $should_redirect = true;
            $school_id = request()->input('school_id');
            if ($school_id) {
                if ($form->isEditing()) {
                    $is_editing = true;
                }
                $this->schoolId = $school_id;
                $is_school_founder = SchoolUser::query()
                    ->where('school_id', $school_id)
                    ->where('user_id', $this->teacherId)
                    ->where('is_founder', true)
                    ->exists();
                if ($is_school_founder) {
                    $should_redirect = false;
                    // $form->number('school_id', __('School id'));
                    $form->hidden('school_id', __('School'))->value($school_id);
                }
            } else {
                if ($form->isCreating()) {
                    $school = School::query()
                        ->where('user_id', $this->teacherId)
                        ->first();
                    if ($school) {
                        $should_redirect = false;
                        $this->schoolId = $school->id;
                        // $form->number('school_id', __('School id'));
                        $form->hidden('school_id', __('School'))->value($school->id);
                    }
                } else if ($form->isEditing()) {
                    $is_editing = true;
                    $student = Student::query()->find($this->modelId);
                    $school_id = $student->school_id;
                    $this->schoolId = $student->school_id;
                    $is_school_founder = SchoolUser::query()
                        ->where('school_id', $school_id)
                        ->where('user_id', $this->teacherId)
                        ->where('is_founder', true)
                        ->exists();
                    if ($is_school_founder) {
                        $should_redirect = false;
                        // $form->number('school_id', __('School id'));
                        $form->hidden('school_id', __('School'))->value($school_id);
                    }
                }
            }
            if ($should_redirect) {
                $this->shouldRedirect = true;
                $this->redirectUrl = route('admin.schools.index');
                // return redirect()->back();
                return false;
            }
            $student_id = $this->modelId;
            $school_id = $this->schoolId;
            $form->tools(function (Form\Tools $tools) use ($is_editing, $student_id, $school_id) {
                // $tools->disableDelete();
                $tools->disableList();
                $tools->disableView();
                if ($is_editing) {
                    $view_route = route('admin.students.show', [
                        'student_id' => $student_id,
                        'school_id' => $school_id,
                    ]);
                    $view_text = trans('admin.view');
                    $view_button = <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$view_route}" class="btn btn-sm btn-primary" title="{$view_text}">
        <i class="fa fa-eye"></i><span class="hidden-xs"> {$view_text}</span>
    </a>
</div>
HTML;
                    $tools->append($view_button);
                }
                $list_route = route('admin.students.index', [
                    'school_id' => $school_id,
                ]);
                $list_text = trans('admin.list');
                $list_button = <<<EOT
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$list_route}" class="btn btn-sm btn-default" title="{$list_text}"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;{$list_text}</span></a>
</div>
EOT;
                $tools->append($list_button);
            });
        } else {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.schools.index');
            // return redirect()->back();
            return false;
        }

        // $form->number('school_id', __('School id'));
        // $form->number('school_id', __('School'));
        $form->text('name', __('Name'))->required(true)->placeholder('Student name ...');
        if ($form->isCreating()) {
            $form->email('email', __('Email'));
            $form->password('password', __('Password'))->default(Student::PASSWORD)->placeholder('Default Password is: ' . Student::PASSWORD);
        } else if ($form->isEditing()) {
            $form->email('email', __('Email'))->readonly();
        }
        $form->image('avatar', __('Avatar'))
            ->uniqueName()
            ->move('avatar')
            ->rules('required|image');

        $form->saving(function (Form $form) {
            //
        });

        $form->saved(function (Form $form) {
            if ($password = request()->input('password')) {
                $form->model()->update([
                    'password' => bcrypt($password),
                ]);
            }
        });

        return $form;
    }
}
