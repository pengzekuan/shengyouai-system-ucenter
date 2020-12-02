<?php

namespace Shengyouai\App\Http\Controllers\UCenter;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Shengyouai\App\Http\Resources\ApiResource;
use Shengyouai\App\Services\SMS\QCloudSMSService;
use Shengyouai\App\UCModels\UCUser;

class SMSController extends UCenterController
{

    private $smsService;

    public function __construct()
    {
        $this->smsService = new QCloudSMSService(
            config('ucenter.sms.appId'),
            config('ucenter.sms.appKey'),
            config('ucenter.sms.appSign'),
            config('ucenter.sms.tempId'),
            config('ucenter.sms.expires_in')
        );
    }

    /**
     * 短信发送
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function send(Request $request, Response $response)
    {
        $validated = Validator::make($request->input(), [
            'cellphone' => [
                'required',
                'regex:' . UCUser::CELLPHONE_PATTERN
            ],
        ], [
            'cellphone.required' => '手机号不能为空',
            'cellphone.regex' => '请输入正确的手机号',
            'type.required' => '请指定短信类型',
            'type.in' => '不支持的短信类型',
        ]);

        if ($validated->fails()) {
            return ApiResource::warning($response, $validated->errors()->first());
        }

        $cellphone = $request->input('cellphone');

        try {
            $res = $this->smsService->sendCode($cellphone);

            return ApiResource::success($response, $res);
        } catch (Exception $e) {
            return ApiResource::warning($response, $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 短信验证码校验
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function verify(Request $request, Response $response)
    {
        $params = $request->input();
        $validated = Validator::make($params, [
            'cellphone' => [
                'required',
                'regex:' . UCUser::CELLPHONE_PATTERN
            ],
            'code' => [
                'required'
            ],
        ], [
            'cellphone.required' => '接口参数有误：缺少必要参数',
            'cellphone.regex' => '接口参数格式有误',
            'code.required' => '接口参数有误：缺少必要参数',
        ]);

        if ($validated->fails()) {
            return ApiResource::warning($response, $validated->errors()->first());
        }

        $valid = $this->smsService->verifyCode($params['cellphone'], $params['code']);

        if ($valid) {
            return ApiResource::success($response, null);
        }

        return ApiResource::warning($response, '验证码有误或已失效，请重新获取验证码');
    }
}
