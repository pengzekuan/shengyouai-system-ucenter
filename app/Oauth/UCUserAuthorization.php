<?php


namespace Shengyouai\App\Oauth;


use Carbon\Carbon;
use EasyWeChat\Factory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Shengyouai\App\Http\Resources\ApiResource;
use Shengyouai\App\Http\Resources\Model\UCUserResource;
use Shengyouai\App\UCModels\UCUser;
use Shengyouai\App\UCModels\UCUserFeature;
use Shengyouai\App\UCModels\UCUserOauth;
use Shengyouai\App\UCModels\UCUserPlatform;

class UCUserAuthorization extends JWTAuthorization
{
    protected $deadline;

    /**
     * UCUserAuthorization constructor.
     * @param string $key
     * @param int $deadline
     */
    public function __construct($key = 'CXtLM5ECe2dYySFoFWnL2OaUvKiuUxM5', $deadline = 7200)
    {
        $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;
        parent::__construct('HS256', $key);

        $this->deadline = $deadline;
    }

    /**
     * @param $uid
     * @param $pid
     * @param $accessDateTime
     * @return string
     */
    public function getAccessToken($uid, $pid, $accessDateTime)
    {
        $payload = [
            'uid' => $uid,
            'pid' => $pid,
            'accessDateTime' => $accessDateTime,
            'accessDeadline' => $this->getAccessDeadLine($accessDateTime)
        ];
        return parent::encode($payload);
    }

    public function getAccessDeadLine($accessDateTime)
    {
        return Carbon::parse($accessDateTime)->addSeconds($this->deadline)->format(Carbon::DEFAULT_TO_STRING_FORMAT);
    }

    public function decode($token)
    {
        return parent::decode($token);
    }

    /**
     * 平台授权登录
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function oauth(Request $request, Response $response)
    {
        $message = [];
        $params = $request->all();
        Log::debug('params', $params);

        $platform = isset($params['p']) ? intval($params['p']) : 0;
        $params['pid'] = $platform;
        $params['clientIp'] = ApiResource::getClientIp($request);
        $params['device'] = ApiResource::getDevice($request);
        $params['network'] = ApiResource::getNetwork($request);

        Log::info('用户授权通道:' . $platform);

        $rules = [
            'cId' => ['string', 'max:128'], // 用户来源渠道id
            'ct' => ['string', 'max:64'], // 来源类型
            'scene' => 'string|max:128', // 来源场景值
        ];

        if ($platform === UCUserOauth::PLATFORM_WX_MINI) {
            return $this->wxMiniOauth(
                config('wechat.mini.app_id'),
                config('wechat.mini.secret'),
                $request,
                $response,
                $params,
                $rules,
                $message
            );
        }

        if ($platform === UCUserOauth::PLATFORM_WX_OFFICIAL) {
            return $this->wxOfficialOauth(
                config('wechat.official.app_id'),
                config('wechat.official.app_id'),
                $request,
                $response,
                $params,
                $rules,
                $message
            );
        }

        return $this->oauthByCellphone($response, $params, $rules, $message);
    }

    /**
     * @param $appId
     * @param $secret
     * @param Request $request
     * @param Response $response
     * @param $params
     * @param $rules
     * @param $message
     * @return array|Response
     */
    public function wxMiniOauth($appId, $secret, Request $request, Response $response, $params, $rules, $message)
    {

        /**
         * 必要参数 授权code
         */
        $rules = array_merge($rules, [
            'code' => 'required' // 小程序授权code
        ]);

        $validated = Validator::make($params, $rules, $message);

        if ($validated->fails()) {
            return ApiResource::warning($response, $validated->errors()->first());
        }

        try {

            $app = Factory::miniProgram([
                'app_id' => $appId,
                'secret' => $secret
            ]);

            $res = $app->auth->session($params['code']);

            if (array_key_exists('errcode', $res)) {
                $errCode = $res['errcode'];
                $errMsg = $res['errmsg'];

                if (intval($errCode) !== 0) {
                    return ApiResource::warning($response, $errMsg, $errCode);
                }
            }

            if (!array_key_exists('openid', $res) || !array_key_exists('session_key', $res)) {
                return ApiResource::warning($response, '授权失败，请稍后重试', -1);
            }

            // 获取用户授权信息
            $openId = $res['openid'];
            $sessionKey = $res['session_key'];

            DB::beginTransaction();
            // 查询授权平台账户
            $pUser = UCUserPlatform::findByOpenId($openId);

            if (!$pUser) { // 去注册
                $user = new UCUser();
                $user = $user->registry(null, $params);

                $pUser = new UCUserPlatform();

                $pUser->registry($user->id, $appId, $openId, $sessionKey, $options = [
                    'platformId' => $params['pid'],
                ]);
            }

            $ucUser = UCUser::find($pUser->uid);

            $ucUser->pid = $params['pid'];

            if ($pUser->disabled) {
                DB::rollBack();
                return ApiResource::warning($response, '您已经被该平台禁用，请联系客服');
            }

            // 检测平台授权
            $oauth = UCUserOauth::check($pUser->uid, $ucUser->pid);

            $ucUser->oauth = $oauth;

            if ($oauth) {
                DB::rollBack();
                return ApiResource::success($response, new UCUserResource($ucUser));
            }

            // 重新授权
            $oauth = new UCUserOauth();
            $oauth->add(
                $ucUser->id,
                $appId,
                $params['clientIp'],
                $params['device'],
                $params['network']
            );

            $ucUser->oauth = $oauth;

            DB::commit();

            return ApiResource::success($response, new UCUserResource($ucUser));

        } catch (Exception $exception) {
            return ApiResource::error($request, $response, $exception);
        }
    }

    /**
     * 公众号登录
     * @param $appId
     * @param $secret
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function wxOfficialOauth($appId, $secret, Request $request, Response $response, $params, $rules, $message)
    {
        $params['scope'] = !empty($params['scope']) ? $params['scope'] : 'snsapi_base';
        /**
         * 必要参数 跳转链接还是回调，
         */
        $rules = array_merge($rules, [
            'state' => '', // 授权状态
            'scope' => '', // 授权作用域
            'code' => '', // 公众号授权code
            't' => '', // 回调地址
            'r' => '', // 是否跳转
        ]);

        $validated = Validator::make($params, $rules, $message);

        if ($validated->fails()) {
            return ApiResource::warning($response, $validated->errors()->first());
        }

        Log::debug('oauth params', $params);

        Log::debug('path', [
            'base' => self::getBasePath($request),
            'path' => $request->path()
        ]);

        $redirect_url = self::getBasePath($request) . '/' . $request->path();
        $redirect_url .= '?p=' . $params['pid'];
        $redirect_url .= '&r=' . (!empty($params['r']) ? $params['r'] : 0);

        if (!empty($params['t'])) {
            $redirect_url .= '&t=' . $params['t'];
        }

        $state = isset($params['state']) ? intval($params['state']) : 0;

        $redirect_url .= '&state=' . $state;

        $redirect_url .= '&scope=' . $params['scope'];

        $redirect_url = rawurlencode($redirect_url);

        Log::debug('redirect_url => ' . $redirect_url);
        // 参数校验
        $url = $this->wxOfficialOauthUrl($appId, $redirect_url, $params['scope']);

        if (!$state) {
            return ApiResource::redirect($response, $url);
        }

        try {
            $app = Factory::officialAccount(config('wechat.official'));
            $oauth = $app->oauth;
            $oauthUser = $oauth->user();

            if (!$oauthUser || !$oauthUser->getOriginal()) {
                return ApiResource::redirect($response, $url);
            }

            $options = [];
            $original = (array)$oauthUser->getOriginal();

            Log::debug('original', $original);
            $openId = $original['openid'];
            $sessionKey = $original['access_token'];
            $options['accessDateTime'] = Carbon::now();
            $options['accessDeadline'] = Carbon::now()->addSeconds(($original['expires_in'] - 10 * 60));
            if (isset($original['nickname'])) {
                $options['nickName'] = $original['nickname'];
                $options['avatar'] = !empty($original['headimgurl']) ? $original['headimgurl'] : '';
                $options['sex'] = isset($original['sex']) ? $original['sex'] : UCUserFeature::SEX_UNKNOWN;
            }

            // 授权用户处理
            $up = UCUserPlatform::findByOpenId($oauthUser->getId());

            if (!$up) {
                DB::beginTransaction();
                $user = (new UCUser())->registry(null, $params);
                $up = (new UCUserPlatform())->registry($user->id, $appId, $openId, $sessionKey, $options);
                DB::commit();
            } else {
                $user = UCUser::find($up->uid);
            }

            $auth = UCUserOauth::findByUserId($up->uid, $params['pid']);

            if (!$auth) {
                $auth = (new UCUserOauth())->add($up->uid, $params['pid'], $params['clientIp'], $params['device'], $params['network']);
            }

            Log::debug('授权', ['auth' => $auth->toArray()]);

            if (isset($params['t'])) {
                $target = $params['t'];

                $target .= (preg_match("/\\?/", $target) ? '&' : '?') . 'token=' . $auth->sessionKey;

                return ApiResource::redirect($response, $target);
            }

            return ApiResource::success($response, $oauth->user());
        } catch (Exception $exception) {
            Log::error('授权失败', [
                'errcode' => $exception->getCode(),
                'errmsg' => $exception->getMessage()
            ]);
            return ApiResource::redirect($response, $url);
        }
    }

    public static function getBasePath(Request $request)
    {
        return substr($request->url(), 0, strlen($request->url()) - strlen($request->path()) - 1);
    }

    public function wxOfficialOauthUrl($appId, $redirect_uri, $scope)
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize";
        $url .= '?appid=' . $appId;
        $url .= '&redirect_uri=' . $redirect_uri;
        $url .= '&response_type=code';
        $url .= '&scope=' . $scope;
        $url .= '&state=1';
        $url .= '#wechat_redirect';

        return $url;
    }

    public function oauthByCellphone(Response $response, $params, $rules, $message)
    {
        $rules = array_merge($rules, [
            'cellphone' => [
                'required',
                'regex:' . UCUser::CELLPHONE_PATTERN
            ], // 手机号
            'code' => [
                'required'
            ] // 短信验证码
        ]);

        $validated = Validator::make($params, $rules, $message);

        if ($validated->fails()) {
            return ApiResource::warning($response, $validated->errors()->first());
        }

        // 手机号登录
        $cellphone = $params['cellphone'];

        unset($params['cellphone']);

        // 手机号正则验证
        if (!UCUser::validCellphone($cellphone)) {
            return ApiResource::warning($response, '手机号格式有误');
        }

        // TODO 短信验证

        // 查询用户
        $find = UCUser::findByCellphone($cellphone);

        // 手机号登录即注册
        if (!$find) {
            $user = new UCUser();
            $user = $user->registry($cellphone, $params);
            return ApiResource::success($response, new UCUserResource($user));
        }

        // 查询登录状态
        $oauth = UCUserOauth::check($find->id);

        if (!$oauth) {
            // 授权
            $oauth = new UCUserOauth();
            $oauth->add(
                $find->id,
                0,
                $params['clientIp'],
                $params['device'],
                $params['network']
            );
        }

        $find->oauth = $oauth;

        return ApiResource::success($response, new UCUserResource($find));
    }
}
