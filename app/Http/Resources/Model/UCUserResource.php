<?php

namespace Shengyouai\App\Http\Resources\Model;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shengyouai\App\UCModels\UCUser;

/**
 * 用户数据模型视图
 * Class UCUserResource
 * @package Shengyouai\App\Http\Resources\Model
 */
class UCUserResource extends JsonResource
{
    public function __construct(UCUser $user)
    {
        parent::__construct($user);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->resource->id,
            'disabled' => $this->resource->disabled,
            'debug' => $this->resource->debug,
        ];

        $oauth = $this->resource->oauth;
        if ($oauth) {
            $data['oauth'] = [
                'accessDateTime' => $oauth->accessDateTime,
                'accessDeadline' => $oauth->accessDeadline,
                'sessionKey' => $oauth->sessionKey,
            ];
        }
        return $data;
    }
}
