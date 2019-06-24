<?php
namespace Workers\Core;

class Model {
    protected $connection = 'mongo';

    protected $database;

    protected $collection;

    protected static $model;

    private function collection()
    {
        return static::get()->collection;
    }

    private function setCollection()
    {
        $className = end(explode('\\', get_called_class()));
        preg_match_all('/[A-Z][a-z]*/', $className, $matches);
        static::get()->collection = strtolower(implode('_', current($matches)) . 's');
    }

    private function connection()
    {
        return static::get()->connection;
    }

    private function setConnection()
    {
        static::get()->connection = get_class_vars(static::class)['connection'];
    }

    public function __call($method, $args)
    {
        static::setConnection();
        static::setCollection();
        static::get()->database = static::get()->Connect();

        return call_user_func_array([static::get()->database, $method], $args);
    }

    public static function __callStatic($method, $args)
    {
        static::setConnection();
        static::setCollection();
        static::get()->database = static::get()->Connect();

        return call_user_func_array([static::get()->database, $method], $args);
    }

    private function Connect()
    {
        return Connection::connect(static::connection())
            ->{app(static::connection() . '.db')}
            ->{static::get()->collection()};
    }

    private static function get()
    {
        if (static::$model === null) {
            static::$model = new static;
        }

        return static::$model;
    }
}