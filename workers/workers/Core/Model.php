<?php
/**
 * contains main model class
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker\Core;

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
 * @method array|object find(array $filter, array $options = [])
 * @method array|object findOne(array $filter, array $options = [])
 * @method object updateMany(array $filter, array $set, array $options = [])
 * @method object updateOne(array $filter, array $set, array $options = [])
 * @method array|object aggregate(array $options = [])
 * @method bulkWrite()
 * @method number count(array $filter = [])
 * @method createIndex()
 * @method createIndexes()
 * @method boolean deleteMany(array $filter)
 * @method boolean deleteOne(array $filter)
 * @method distinct()
 * @method boolean drop()
 * @method dropIndex()
 * @method dropIndexes()
 * @method array|object findOneAndDelete(array $filter)
 * @method array|object findOneAndReplace(array $filter, array $options = [])
 * @method array|object findOneAndUpdate(array $filter, array $options = [])
 * @method string getCollectionName()
 * @method string getDatabaseName()
 * @method getManager()
 * @method getNamespace()
 * @method getReadConcern()
 * @method getReadPreference()
 * @method getTypeMap()
 * @method getWriteConcern()
 * @method object insertMany(array $filter, array $options = [])
 * @method object insertOne(array $filter, array $options = [])
 * @method array|object listIndexes()
 * @method mapReduce()
 * @method object replaceOne(array $filter, array $options = [])
 * @method withOptions()
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
        return static::get()->collection;
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
            static::get()->collection = strtolower(implode('_', current($matches)) . 's');
        } else {
            static::get()->collection = get_class_vars(static::class)['collection'];
        }
    }

    /**
     * returns value of connection property
     *
     * @return string
     */
    private function connection()
    {
        return static::get()->connection;
    }

    /**
     * specify connection based on calling class connection property
     */
    private function setConnection()
    {
        static::get()->connection = get_class_vars(static::class)['connection'];
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
        static::get()->database = static::get()->Connect();

        return call_user_func_array([static::get()->database, $method], $args);
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
        static::get()->database = static::get()->Connect();

        return call_user_func_array([static::get()->database, $method], $args);
    }

    /**
     * returns Database Connection with collection
     *
     * @return mixed
     */
    protected function Connect()
    {
        return Connection::connect(static::connection())
            ->{app(static::connection() . '.db')}
            ->{static::get()->collection()};
    }

    /**
     * creates an instance of Model if not exists
     * and returns it
     *
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