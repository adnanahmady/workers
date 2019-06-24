<?php
namespace Workers\Core;

class Model {
    protected $connection = 'mongo';

    protected $database;

    protected $collection;

    protected static $model;

    /**
     * @return mixed
     */
    private static function collection()
    {
        if (static::get()->collection === null) {
            $className = end(explode('\\', get_called_class()));
            preg_match_all('/[A-Z][a-z]*/', $className, $matches);
            static::get()->collection = strtolower(implode('_', current($matches)) . 's');
        }

        return static::get()->collection;
    }

    public function __call($method, $args)
    {
        static::get()->database = static::get()->Connect();

        return call_user_func_array([static::get()->database, $method], $args);
    }

    public static function __callStatic($method, $args)
    {
        static::get()->database = static::get()->Connect();

        return call_user_func_array([static::get()->database, $method], $args);
    }

    protected function Connect()
    {
        return Connection::connect(static::get()->connection)
            ->{app(static::get()->connection . '.db')}
            ->{static::collection()};
    }

    /**
     * @return mixed
     */
    private static function get()
    {
        if (static::$model === null) {
            static::$model = new static;
        }

        return static::$model;
    }
}
