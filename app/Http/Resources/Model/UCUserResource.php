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
        return parent::toArray($request);
    }
}
