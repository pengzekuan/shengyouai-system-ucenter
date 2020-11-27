<?php

namespace Shengyouai\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 列表分页数据模型
 * Class PaginatorResource
 * @package Shengyouai\App\Http\Resources
 */
class PaginatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
