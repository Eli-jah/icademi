<?php

namespace App\Admin\Controllers;

// use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Form\Footer;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\DropdownActions;
use Encore\Admin\Grid\Tools as GridTools;
use Encore\Admin\Grid\Tools\BatchActions;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Show\Tools as ShowTools;

class ExampleController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        // 仅对列表页面，禁用页面局部刷新效果
        Admin::disablePjax();
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
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
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
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
        // return redirect()->back();
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form(true)->edit($id));
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
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new YourModel);

        /*禁用*/
        // $grid->disableActions();
        // $grid->disableRowSelector();
        // $grid->disableExport();
        // $grid->disableFilter();
        // $grid->disableCreateButton();
        // $grid->disablePagination();

        $grid->actions(function (DropdownActions $actions) {
            // $current_row_id = $this->getKey();
            // $current_row_id = $this->getRouteKey();
            // $actions->disableAll();
            // $actions->disableView();
            // $actions->disableEdit();
            // $actions->disableDelete();
        });

        /*$grid->tools(function (GridTools $tools) {
            $tools->batch(function (BatchActions $batch) {
                $batch->disableDelete();
            });
            $tools->append('<div class="btn-group pull-right" style="margin-right: 10px">'
                . '<a id="customized-create-button" href="javascript:void(0);" data-href="' . route('admin.platform_stores.create') . '" class="btn btn-sm btn-success" title="新增">'
                . '<i class="fa fa-plus"></i>'
                . '<span class="hidden-xs">&nbsp;&nbsp;新增</span>'
                . '</a>'
                . '</div>');
        });*/


        $grid->id('ID')->sortable();
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(YourModel::findOrFail($id));

        $show->panel()->tools(function (ShowTools $tools) {
            // $tools->disableEdit();
            // $tools->disableList();
            // $tools->disableDelete();
        });

        $show->id('ID');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder
     *
     * @param bool $isEditing
     *
     * @return Form
     */
    protected function form($isEditing = false)
    {
        $form = new Form(new YourModel);

        if ($form->isCreating()) {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.teachers.index');
            // return redirect()->route('admin.teachers.index');
            // return redirect()->back();
        } else if ($form->isEditing()) {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.teachers.index');
            // return redirect()->route('admin.teachers.index');
            // return redirect()->back();
        } else {
            $this->shouldRedirect = true;
            $this->redirectUrl = route('admin.teachers.index');
            // return redirect()->route('admin.teachers.index');
        }

        // if ($form->isUpdating()) { // Deprecated
        if ($form->isEditing()) { // Suggested
            // if ($isEditing) {
            $isPost = request()->isMethod('POST');
            $parameter = request()->route('parameter_name');
            $parameterId = request()->route()->parameter('parameter_id');
            $parameters = request()->route()->parameters();
            return redirect()->back();
        }

        $form->tools(function (Form\Tools $tools) {
            // $tools->disableDelete();
            // $tools->disableList();
            // $tools->disableView();
        });

        $form->display('id', 'ID');
        $form->display('created_at', 'Created At');
        $form->display('updated_at', 'Updated At');

        $form->footer(function (Footer $footer) {

            // disable reset btn
            $footer->disableReset();

            // disable submit btn
            $footer->disableSubmit();

            // disable `View` checkbox
            $footer->disableViewCheck();

            // disable `Continue editing` checkbox
            $footer->disableEditingCheck();

            // disable `Continue Creating` checkbox
            $footer->disableCreatingCheck();

        });

        $form->disableSubmit();
        $form->confirm('Send Email');
        $form->confirm('Send Email', 'edit');
        $form->confirm('Send Email', 'create');

        $form->saving(function (Form $form) {
            //
        });

        $form->saved(function (Form $form) {
            //
        });

        return $form;
    }
}
