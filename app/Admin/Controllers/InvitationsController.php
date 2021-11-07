<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Teacher;
use App\Mail\InvitationToJoinOurSchool;
use App\Models\Invitation;
use App\Models\School;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Form\Footer;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\DropdownActions;
use Encore\Admin\Grid\Tools as GridTools;
use Encore\Admin\Grid\Tools\BatchActions;
use Encore\Admin\Show;
use Encore\Admin\Show\Tools as ShowTools;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationsController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Invitation';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Invitation());

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
            $grid->model()
                ->where('user_id', $this->teacherId);

            // $grid->disableActions();
            // $grid->disableExport();
            // $grid->disableFilter();
            // $grid->disableCreateButton();
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
        // $grid->column('user_id', __('User id'));
        $grid->column('school_name', __('School Name'));
        $grid->column('email', __('Recipient Email'))->copyable();
        $grid->column('recipient_name', __('Recipient name'))->copyable();
        $grid->column('random_code', __('Random code'))->copyable();
        $grid->column('created_at', __('Sent at'));
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
        $show = new Show(Invitation::findOrFail($id));

        if ($this->isTeacher()) {
            $invitation = $show->getModel();
            if ($invitation->user_id != $this->teacherId) {
                $this->shouldRedirect = true;
                $this->redirectUrl = route('admin.home');
                // return redirect()->route('admin.home');
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
        // $show->field('user_id', __('User id'));
        $show->field('school_name', __('School Name'));
        $show->field('email', __('Recipient Email'));
        $show->field('recipient_name', __('Recipient name'));
        $show->field('random_code', __('Random code'));
        $show->field('created_at', __('Sent at'));
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
        $form = new Form(new Invitation());

        $should_redirect = true;
        if ($this->isTeacher()) {
            if ($form->isCreating()) {
                $should_redirect = false;
                $recipient_id = request()->input('recipient_id');
                $recipient = Teacher::query()
                    ->find($recipient_id);
                $school_options = School::query()
                    ->where('user_id', $this->teacherId)
                    ->get()
                    ->pluck('name', 'id');
                $form->select('school_id', __('School'))->options($school_options);
                $form->hidden('user_id', __('User id'))
                    ->value($this->teacherId);
                if ($recipient) {
                    $form->email('email', __('Email'))
                        ->required()
                        ->rules('required|email')
                        ->value($recipient->email);
                    $form->text('recipient_name', __('Recipient name'))
                        ->required(true)
                        ->value($recipient->name);
                } else {
                    $form->email('email', __('Email'))
                        ->required()
                        ->rules('required|email');
                    $form->text('recipient_name', __('Recipient name'))
                        ->required(true);
                }
                $form->hidden('random_code', __('Random code'))->value(Str::random(8));
            }
        }
        if ($should_redirect) {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.home');
            // return redirect()->route('admin.home');
            // return redirect()->back();
            return false;
        }

        // $form->number('school_id', __('School id'));
        // $form->number('user_id', __('User id'));
        // $form->email('email', __('Email'));
        // $form->text('recipient_name', __('Recipient name'));
        // $form->text('random_code', __('Random code'));

        $form->footer(function (Footer $footer) {
            // disable reset btn
            // $footer->disableReset();

            // disable submit btn
            // $footer->disableSubmit();

            // disable `View` checkbox
            // $footer->disableViewCheck();

            // disable `Continue editing` checkbox
            // $footer->disableEditingCheck();

            // disable `Continue Creating` checkbox
            // $footer->disableCreatingCheck();
        });

        // $form->confirm('Are you sure to send an invitation email ?', 'create');

        $form->saving(function (Form $form) {
            //
        });

        $form->saved(function (Form $form) {
            /*$input = request()->input();

            if ($teacher = User::query()
                ->where('email', $input['email'])
                ->first()) {
                $teacher->update([
                    'random_code' => $input['random_code'],
                ]);
            } else {
                $teacher = User::query()
                    ->create([
                        'name' => $input['recipient_name'],
                        'email' => $input['email'],
                        'password' => User::PASSWORD,
                        'random_code' => $input['random_code'],
                    ]);
            }

            Mail::to($teacher)->send(new InvitationToJoinOurSchool($teacher));*/
        });

        return $form;
    }
}
