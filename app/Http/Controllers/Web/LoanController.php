<?php

namespace App\Http\Controllers\Web;

use App\Components\ApiResponse;
use App\Components\ErrorCode;
use App\Components\Utils;
use App\Http\Controllers\Controller;
use App\Models\LoanInfoExtend;
use App\Models\LoanInfoForm;
use App\Models\LoanProduct;
use Auth;
use Cache;
use Closure;
use DB;
use Illuminate\Http\Request;
use Lang;
use Validator;

/**
 * 借贷
 */
class LoanController extends Controller
{
    /** @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard */
    private $auth;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->auth = Auth::guard('web');

        // 验证登录
        $this->middleware(function($request, Closure $next) {
            if (!$this->auth->check()) {
                return ApiResponse::buildFromArray(ErrorCode::NOT_AUTH);
            }

            return $next($request);
        });

        Validator::extend('id_card', function($attribute, $value, $parameters) {
            $patter = '/^\d{18}$/';
            return (preg_match($patter, $value)) ? true : false;
        });

        // 替换语言包
        Lang::load('*', 'validation', Lang::getLocale());
        $lines = [
            'validation.attributes.name' => '姓名',
            'validation.attributes.id_card' => '身份证号',
            'validation.attributes.loan_amount' => '借款金额',
            'validation.attributes.loan_deadline' => '借款期限',
            'validation.attributes.loan_deadline_type' => '借款期限类型',
            'validation.attributes.use_loan_time' => '用款日期',
            'validation.attributes.required' => '职业信息',
            'validation.id_card' => '身份证号格式错误。',
            'validation.date_format' => ':attribute格式错误。',
        ];
        Lang::addLines($lines, Lang::getLocale());
    }

    /**
     * 分析贷款方案
     * @param Request $request
     * @return ApiResponse
     * @throws \Exception
     */
    public function parse(Request $request)
    {
        $this->validate($request, array(
            'name' => ['required', 'between:2,8'],
            'id_card' => ['required', 'id_card'],
            'loan_amount' => ['required', 'integer'],
            'loan_deadline' => ['required'],
            'loan_deadline_type' => ['required'],
            'use_loan_time' => ['required', 'date_format:Y-m-d'],
            'job' => ['required'],
            'more_info' => ['array']
        ));

        // 构建数据
        $userId = $this->auth->user()->getAuthIdentifier();
        $fields = ['name', 'id_card', 'loan_amount', 'loan_deadline', 'loan_deadline_type', 'use_loan_time', 'job', 'area'];
        $data = $request->only($fields);
        $data['user_id'] = $userId;

        // 计算借款期天数
        $data['loan_deadline_day'] = Utils::convertLoanDeadline($data['loan_deadline'], $data['loan_deadline_type']);

        $extends = [];
        if (is_array($request->input('more_info'))) {
            foreach ($request->input('more_info') as $moreInfo) {
                $extends[] = new LoanInfoExtend([
                    'extend' => $moreInfo
                ]);
            }
        }

        DB::beginTransaction();
        try {
            /** @var LoanInfoForm $form */
            $form = LoanInfoForm::where('user_id', $userId)->first();

            if (empty($form)) {
                // 新建
                $form = LoanInfoForm::create($data);
            } else {
                // 更新
                foreach ($data as $key => $value) {
                    $form->$key = $value;
                }
                $form->saveOrFail();

                // 删除关联表
                $form->_extends()->delete($form->id);
            }

            // 写入扩展信息
            if (count($extends) > 0) {
                $form->_extends()->saveMany($extends);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return ApiResponse::buildFromArray();
    }

    /**
     * 借贷方案
     * @param LoanProduct $products
     * @return ApiResponse
     */
    public function cases(LoanProduct $products)
    {
        // 获取用户信息
        $userId = $this->auth->user()->getAuthIdentifier();
        /** @var LoanInfoForm $form */
        $form = LoanInfoForm::where('user_id', $userId)->first();

        // 判断是否有填写贷款信息
        if (empty($form)) {
            return ApiResponse::buildFromArray(ErrorCode::NOT_HAS_LOAN_INFO);
        }

        $cases = $products->getUserCases($form);
        foreach ($cases as &$case) {
            $case['loaneders'] = $this->randLoaneders($case);
            unset($case['created_at']);
        }
        $cases = array_values($cases);

        // 通过率
        $passRate = $this->getPassRate($userId);

        $data = array(
            'cases' => $cases,
            'pass_rate' => $passRate
        );

        return ApiResponse::buildSuccess($data);
    }

    /**
     * 随机申请人数
     * @param $case
     * @return int
     */
    private function randLoaneders($case)
    {
        $days = (time() - strtotime($case['created_at'])) / 86400;
        $rand = date('d') % 2 == 0 ? 1 : 2;
        $loaneders = (int)data_get($case, 'loaneders');

        return $loaneders + intval(5 + ($case['id'] % 5) + $days + $rand);
    }

    /**
     * 获取通过率
     * @param $userId
     * @return mixed
     */
    private function getPassRate($userId)
    {
        $key = "loan_pass_rate:{$userId}";
        if (!Cache::has($key)) {
            $rate = 80 + rand(0, 5) + $userId % 10;
            Cache::put($key, $rate, 24 * 60);
        }

        return Cache::get($key);
    }

    /**
     * 详情
     * @param $id
     * @return ApiResponse
     */
    public function case($id)
    {
        $case = LoanProduct::with('_extends')->find($id);

        if (empty($case)) {
            return ApiResponse::buildFail(ErrorCode::ERR_PARAM[0], '方案不存在！');
        }

        $case = $case->toArray();
        $case['process'] = $case['process'] ? explode("\n", $case['process']) : null;
        $case['detail'] = $case['detail'] ? str_replace("\n", '<br>', $case['detail']) : null;
        $case['condition'] = $case['condition'] ? str_replace("\n", '<br>', $case['condition']) : null;
        $case['_extends'] = $case['_extends'] ? $case['_extends'] : null;
        $case['loaneders'] = $this->randLoaneders($case);

        $data = array(
            'id' => $id,
            'case' => $case,
        );

        return ApiResponse::buildSuccess($data);
    }

}