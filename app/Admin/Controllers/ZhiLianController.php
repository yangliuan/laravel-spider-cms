<?php

namespace App\Admin\Controllers;

use App\JobZhiLian;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ZhiLianController extends Controller
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
        return $content
            ->header('智联数据列表')
            ->description('智联数据列表')
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
            ->header('智联数据详情')
            ->description('智联数据详情')
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
        return $content
            ->header('智联数据编辑')
            ->description('智联数据编辑')
            ->body($this->form()->edit($id));
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
            ->header('添加智联数据')
            ->description('添加智联数据')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new JobZhiLian());

        $grid->id('Id');
        $grid->job_position('岗位');
        $grid->salary('薪资');
        $grid->summary('工作年限');
        //$grid->job_desc('Job desc');
        $grid->job_address('公司地址');
        $grid->updated_time('更新时间');

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
        $show = new Show(JobZhiLian::findOrFail($id));

        $show->id('Id');
        $show->job_position('Job position');
        $show->salary('Salary');
        $show->summary('Summary');
        $show->job_desc('Job desc');
        $show->job_address('Job address');
        $show->updated_time('Updated time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new JobZhiLian());

        $form->text('job_position', 'Job position');
        $form->text('salary', 'Salary');
        $form->text('summary', 'Summary');
        $form->textarea('job_desc', 'Job desc');
        $form->text('job_address', 'Job address');
        $form->text('updated_time', 'Updated time');

        return $form;
    }
}
