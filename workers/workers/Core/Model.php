<?php
/**
 * contains main model class
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker\Core;

use MongoDB\Driver\Exception\ConnectionException;

/**
 * Class Model
 *
 * Connects Models to Database Collections
 *
 * @package Worker\Core
 * @property $connection
 * @property $database
 * @property $collection
 * @property $model
 * @method __construct()
 * @method static array|object find(array $filter, array $options = [])
 * @method static array|object findOne(array $filter, array $options = [])
 * @method static object updateMany(array $filter, array $set, array $options = [])
 * @method static object updateOne(array $filter, array $set, array $options = [])
 * @method static array|object aggregate(array $options = [])
 * @method static bulkWrite()
 * @method static number count(array $filter = [])
 * @method static createIndex()
 * @method static createIndexes()
 * @method static boolean deleteMany(array $filter)
 * @method static boolean deleteOne(array $filter)
 * @method static distinct()
 * @method static boolean drop()
 * @method static dropIndex()
 * @method static dropIndexes()
 * @method static array|object findOneAndDelete(array $filter)
 * @method static array|object findOneAndReplace(array $filter, array $options = [])
 * @method static array|object findOneAndUpdate(array $filter, array $options = [])
 * @method static string getCollectionName()
 * @method static string getDatabaseName()
 * @method static getManager()
 * @method static getNamespace()
 * @method static getReadConcern()
 * @method static getReadPreference()
 * @method static getTypeMap()
 * @method static getWriteConcern()
 * @method static object insertMany(array $filter, array $options = [])
 * @method static object insertOne(array $filter, array $options = [])
 * @method static array|object listIndexes()
 * @method static mapReduce()
 * @method static object replaceOne(array $filter, array $options = [])
 * @method static withOptions()
 */
class Model {
    /**
     * @var string Connection name
     */
    protected $connection = 'mongo';

    /**
     * @var Connection An instance of database connection
     */
    protected $database;

    /**
     * @var string The collection that calling model is pointing to
     */
    protected $collection;

    /**
     * @static $model
     * @var Model An static instance of Model class
     */
    protected static $model;

    /**
     * returns calling Models collection field
     *
     * @return string
     * @private access
     */
    private function collection()
    {
        return static::init()->collection;
    }

    /**
     * specifies calling collection
     * based on calling Models Name
     * or collection field value
     */
    private function setCollection()
    {
        if (get_class_vars(static::class)['collection'] === null) {
            $className = end(explode('\\', get_called_class()));
            preg_match_all('/[A-Z][a-z]*/', $className, $matches);
            static::init()->collection = strtolower(implode('_', current($matches)) . 's');
        } else {
            static::init()->collection = get_class_vars(static::class)['collection'];
        }
    }

    /**
     * returns value of connection property
     *
     * @return string
     */
    private function connection()
    {
        return static::init()->connection;
    }

    /**
     * specify connection based on calling class connection property
     */
    private function setConnection()
    {
        static::init()->connection = get_class_vars(static::class)['connection'];
    }

    /**
     * if Call a instantiable method that class doesn't have
     * it will be called from database connection
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        static::setConnection();
        static::setCollection();
        static::init()->database = static::init()->Connect();

        return call_user_func_array([static::init()->database, $method], $args);
    }

    /**
     * if Call a static method that class doesn't have
     * it will be called from database connection
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        static::setConnection();
        static::setCollection();
        static::init()->database = static::init()->Connect();

        return call_user_func_array([static::init()->database, $method], $args);
    }

    /**
     * returns Database Connection with collection
     *
     * @return mixed
     */
    protected function Connect()
    {
        try {
            $connection = Connection::connect(static::connection())
                ->{config('database.' . static::connection() . '.db')}
                ->{static::init()->collection()};
        } catch (\Throwable $exception) {
            throw new ConnectionException($exception->getMessage());
        }

        return $connection;
    }

    /**
     * creates an instance of Model if not exists
     * and returns it
     *
     * @return mixed
     */
    private static function init()
    {
        if (static::$model === NULL) {
            static::$model = new static;
        }

        return static::$model;
    }
}
