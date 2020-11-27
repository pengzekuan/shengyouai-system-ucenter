<?php


namespace Shengyouai\App\UCModels;


use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(array $where)
 * @method static find($id)
 */
class UCModel extends Model
{
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

    public static function getAll()
    {

    }

    public function add($data)
    {
        $model = new $this($data);
        $model->save();

        return $model;
    }
}
