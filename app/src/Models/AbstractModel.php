<?php
namespace TWB\Models;

use Illuminate\Support\Pluralizer;
use TWB\Services\DBConnectorService as DB;
use TWB\Utilities\StringUtility;

abstract class AbstractModel
{

    const PRIMARY_KEY = 'id';

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * AbstractModel constructor.
     * @param DB|null $db
     */
    public function __construct(DB $db = null)
    {
        $this->db = $db ?: DB::create();
        $this->tableName = $this->getTableName();
    }

    /**
     * @return DB
     */
    public function getConnector()
    {
        return $this->db;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getBuilder()
    {
        return $this->getConnector()->getConnector()->table($this->tableName);
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->getConnector()->getConnector()->getPdo()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->getConnector()->getConnector()->getPdo()->commit();
    }

    /**
     * @return bool
     */
    public function rollbackTransaction()
    {
        return $this->getConnector()->getConnector()->getPdo()->rollBack();
    }

    /**
     * @param string $key
     * @param string $val
     * @return Object
     */
    public function findByKey($key, $val)
    {
        return $this->getBuilder()
            ->where($key, $val)
            ->whereNull ('deleted_at')
            ->first();
    }

    /**
     * @param array $data
     * @return int
     */
    public function insert($data = [])
    {
        $now = $this->getNowTime();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        return $this->getBuilder()->insertGetId($data);
    }

    /**
     * @param array $data
     * @return int
     */
    public function update($data)
    {
        $data['updated_at'] = $this->getNowTime();
        return $this->getBuilder()->where(self::PRIMARY_KEY, $data[self::PRIMARY_KEY])->update($data);
    }

    /**
     * @param array $search
     * @param array $data
     * @return int
     */
    public function updateOrInsert($search, $data)
    {
        $now = $this->getNowTime();
        $data['updated_at'] = $now;

        if (!$this->getBuilder()->where($search)->exists()) {
            $data['created_at'] = $now;
            return $this->insert(array_merge($search, $data));
        }

        if (empty($data)) {
            return true;
        }

        return (bool) $this->getBuilder()->where($search)->update($data);
    }

    /**
     * @param int $id
     * @return int
     */
    public function delete($id)
    {
        $data = [];
        $now = $this->getNowTime();
        $data['updated_at'] = $now;
        $data['deleted_at'] = $now;

        return $this->getBuilder()->where(self::PRIMARY_KEY, $id)->update($data);
    }

    /**
     * @param array $data
     * @return int
     */
    public function hardDelete($id)
    {
        return $this->getBuilder()->delete($id);
    }

    /**
     * @return string
     */
    public function getNowTime()
    {
        return $this->formatedTime(time());
    }

    /**
     * @param int $timestamp
     * @return string
     */
    public function formatedTime($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * @param $data
     * @return array
     */
    public function objToArray($data)
    {
        return json_decode(json_encode($data), true);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        $className = get_class($this);
        $nameSpaceArray = explode('\\', $className);
        $className = end($nameSpaceArray);
        $className = str_replace('Model', '', $className);
        $className = Pluralizer::plural($className);
        return StringUtility::snakize($className);
    }

    /**
     * @param $colName
     * @return string
     */
    public function fullColName($colName)
    {
        return $this->tableName . '.' . $colName;
    }

    /**
     * @param string $colName
     * @param string $aliasName
     * @param bool $fullName
     * @return string
     */
    public function giveColumnAlias($colName, $aliasName, $fullName)
    {
        $column = ($fullName) ? $this->fullColName($colName) : $colName;
        return $column . ' AS ' . $aliasName;
    }

    /**
     * Get columns
     * @param array $columns, [0] => $colName, [1] => $aliasName, [2] $fulName
     * @return array
     */
    public function giveColumnAliases(array $columns)
    {
        $result = [];
        foreach ($columns as $column) {
            [$colName, $aliasName, $fullName] = $column;
            $result[] = $this->giveColumnAlias($colName, $aliasName, $fullName);
        }

        return $result;
    }
}