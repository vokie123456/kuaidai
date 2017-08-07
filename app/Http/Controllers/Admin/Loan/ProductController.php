<?php

namespace App\Http\Controllers\Admin\Loan;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\LoanProduct;
use App\Models\LoanProductExtend;
use App\Models\LoanProductJob;
use DB;
use Illuminate\Http\Request;

/**
 * 贷款产品管理
 * @menu index 贷款产品管理
 * @nodeTitle 贷款产品管理
 * @nodeName index 列表
 * @nodeName store 保存
 * @nodeName show 详情
 * @nodeName update 更新
 * @nodeName status 修改状态
 * @nodeName recommend 修改推荐
 * @nodeName destroy 删除
 * @nodeName uploadLogo 上传Logo
 */
class ProductController extends Controller
{

    public function index()
    {
        $data = array(
            'paginate' => LoanProduct::orderBy('id', 'desc')->paginate(),
            'baseData' => config('loan'),
        );

        return view('admin.loan.product.index', $data);
    }

    /**
     * 保存
     * @param Request $request
     * @return ApiResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'name' => ['required', 'between:1,100'],
            'go_url' => ['required', 'between:1,255', 'url'],
            'logo' => ['required'],
            'loan_limit_min' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'loan_limit_max' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'deadline_min' => ['required', 'integer', 'min:1', 'max:1000'],
            'deadline_max' => ['required', 'integer', 'min:1', 'max:1000'],
            'deadline_type' => ['required'],
            'rate_min' => ['required', 'numeric', 'max:99'],
            'rate_max' => ['required', 'numeric', 'max:99'],
            'rate_type' => ['required'],
            'audit_type' => ['required'],
            'audit_cycle' => ['required', 'integer'],
            'loan_time' => ['required', 'integer'],
            'loan_give_type' => ['required'],
            'condition' => ['required', 'between:1,1000'],
            'process' => ['required', 'between:1,1000'],
            'detail' => ['required', 'between:1,1000'],

            'extend' => ['required', 'array'],
            'jobs' => ['required', 'array'],
        ));

        $data = $request->only([
            'name', 'go_url', 'logo', 'loan_limit_min', 'loan_limit_max', 'deadline_min', 'deadline_max', 'deadline_type', 'rate_min', 'rate_max', 'rate_type', 'audit_type', 'audit_cycle', 'loan_time', 'loan_give_type', 'condition', 'process', 'detail'
        ]);
        $data['condition'] = str_replace(["\r", "\r\n"], "\n", $data['condition']);
        $data['process'] = str_replace(["\r", "\r\n"], "\n", $data['process']);
        $data['detail'] = str_replace(["\r", "\r\n"], "\n", $data['detail']);

        $extends = [];
        foreach ($request->input('extend') as $extend) {
            $extends[] = new LoanProductExtend(array(
                'extend' => $extend
            ));
        }

        $jobs = [];
        foreach ($request->input('jobs') as $job) {
            $jobs[] = new LoanProductJob(array(
                'job' => $job
            ));
        }

        DB::beginTransaction();
        try {
            $model = LoanProduct::create($data);
            $model->_extends()->saveMany($extends);
            $model->jobs()->saveMany($jobs);
            DB::commit();

            return ApiResponse::buildSuccess($model->toArray());
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * 上传Logo
     * @param Request $request
     * @return ApiResponse
     */
    public function uploadLogo(Request $request)
    {
        $this->validate($request, array(
            'logo' => ['required', 'image']
        ));

        $file = $request->file('logo');
        $fileName = md5(serialize([uniqid(), $file->getFilename(), $_SERVER]));
        $fileName = substr($fileName, 8, 16) . ".{$file->getClientOriginalExtension()}";
        $file->move(\App::publicPath() . '/upload', $fileName);

        return ApiResponse::buildSuccess(array(
            'path' => "/upload/{$fileName}"
        ));
    }

    /**
     * 修改状态
     * @param Request $request
     * @param $id
     * @return ApiResponse
     */
    public function status(Request $request, $id)
    {
        /** @var LoanProduct $model */
        $model = LoanProduct::findOrFail($id);
        $model->status = $request->input('status');
        $model->saveOrFail();

        return ApiResponse::buildSuccess($model->toArray());
    }

    /**
     * 修改推荐
     * @param Request $request
     * @param $id
     * @return ApiResponse
     */
    public function recommend(Request $request, $id)
    {
        /** @var LoanProduct $model */
        $model = LoanProduct::findOrFail($id);
        $model->recommend = $request->input('recommend');
        $model->saveOrFail();

        return ApiResponse::buildSuccess($model->toArray());
    }

    /**
     * 详情
     * @param $id
     * @return ApiResponse
     */
    public function show($id)
    {
        $model = LoanProduct::with('_extends', 'jobs')->findOrFail($id);

        $model = $model->toArray();
        $model['extend'] = array_column(array_pull($model, '_extends'), 'extend');
        $model['jobs'] = array_column(array_pull($model, 'jobs'), 'job');

        return ApiResponse::buildSuccess($model);
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return ApiResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, array(
            'name' => ['required', 'between:1,100'],
            'go_url' => ['required', 'between:1,255', 'url'],
            'logo' => ['required'],
            'loan_limit_min' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'loan_limit_max' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'deadline_min' => ['required', 'integer', 'min:1', 'max:1000'],
            'deadline_max' => ['required', 'integer', 'min:1', 'max:1000'],
            'deadline_type' => ['required'],
            'rate_min' => ['required', 'numeric', 'max:99'],
            'rate_max' => ['required', 'numeric', 'max:99'],
            'rate_type' => ['required'],
            'audit_type' => ['required'],
            'audit_cycle' => ['required', 'integer'],
            'loan_time' => ['required', 'integer'],
            'loan_give_type' => ['required'],
            'condition' => ['required', 'between:1,1000'],
            'process' => ['required', 'between:1,1000'],
            'detail' => ['required', 'between:1,1000'],

            'extend' => ['required', 'array'],
            'jobs' => ['required', 'array'],
        ));

        /** @var LoanProduct $model */
        $model = LoanProduct::findOrFail($id);
        $data = $request->only([
            'name', 'go_url', 'logo', 'loan_limit_min', 'loan_limit_max', 'deadline_min', 'deadline_max', 'deadline_type', 'rate_min', 'rate_max', 'rate_type', 'audit_type', 'audit_cycle', 'loan_time', 'loan_give_type', 'condition', 'process', 'detail'
        ]);
        $data['condition'] = str_replace(["\r", "\r\n"], "\n", $data['condition']);
        $data['process'] = str_replace(["\r", "\r\n"], "\n", $data['process']);
        $data['detail'] = str_replace(["\r", "\r\n"], "\n", $data['detail']);

        $extends = [];
        foreach ($request->input('extend') as $extend) {
            $extends[] = new LoanProductExtend(array(
                'extend' => $extend
            ));
        }

        $jobs = [];
        foreach ($request->input('jobs') as $job) {
            $jobs[] = new LoanProductJob(array(
                'job' => $job
            ));
        }

        DB::beginTransaction();

        try {
            foreach ($data as $key => $value) {
                $model->$key = $value;
            }
            $model->saveOrFail();

            // 重新生成关联信息
            $model->_extends()->delete($model->id);
            $model->_extends()->saveMany($extends);

            $model->jobs()->delete($model->id);
            $model->jobs()->saveMany($jobs);

            DB::commit();

            return ApiResponse::buildSuccess($model->toArray());
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * 删除
     * @param $id
     * @return ApiResponse
     */
    public function destroy($id)
    {
        $model = LoanProduct::findOrFail($id);
        $model->delete();

        return ApiResponse::buildFromArray();
    }

}