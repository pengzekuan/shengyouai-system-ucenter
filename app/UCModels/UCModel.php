<?php

namespace Shengyouai\App\UCModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(array $where)
 * @method static find($id)
 */
class UCModel extends Model
{

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('timeStampOrder', function (Builder $builder) {
            $builder->orderByDesc('updated_at')
                ->orderByDesc('created_at');
        });
    }

    public static function findById($id)
    {
        return self::find($id);
    }

    public static function count($where)
    {
        return self::where($where)->select(1)->count();
    }

    public static function getOne($where = [])
    {
        return self::where($where)->first();
    }

    public static function getAll($where)
    {
        return self::where($where)->get();
    }
}
