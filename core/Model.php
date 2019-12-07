<?php

namespace BeeJeeTest\Core;

use PDO;

class Model {

    protected static $table;
    protected static $columns;

    private static $pdo;

    protected $data;
    protected $update;

    protected static function getSQL(array $params)
    {
        $result = [];

        if (isset($params['order']))
        {
            $orderSQL = '';

            foreach ($params['order'] as $item)
            {
                $orderSQL .= ",{$item['field']} {$item['dir']}";
            }

            if (empty($orderSQL))
            {
                throw new \Exception('Order is empty');
            }

            $result[] = 'ORDER BY ' . substr($orderSQL, 1);
        }

        if (isset($params['offset']))
        {
            if (!isset($params['offset']['limit'])) throw new \Exception('Limit should be set for offset param');

            $limitSQL = 'LIMIT ';

            if (isset($params['offset']['from'])) $limitSQL .= $params['offset']['from'] . ',';
            $limitSQL .= $params['offset']['limit'];

            $result[] = $limitSQL;
        }

        return implode(' ', $result);
    }

    public static function sanitizeData(array $data, $excludeId = true): array
    {
        $columns = static::$columns;

        if ($excludeId) unset($columns['id']);

        foreach ($data as $key => $value)
        {
            // if (!isset($columns[ $key ])) unset($data[ $key ]);
            if (!in_array($key, $columns)) unset($data[ $key ]);
        }

        return $data;
    }

    public static function pdo()
    {
        if (!isset(self::$pdo))
        {
            $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8';
            $opt = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
                PDO::MYSQL_ATTR_FOUND_ROWS   => true,
            ];

            self::$pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'), $opt);
        }

        return self::$pdo;
    }

    public function field($name, $default = null)
    {
        return isset($this->data[ $name ]) ? $this->data[ $name ] : $default;
    }

    public function set($name, $value)
    {
        if (!isset($this->update)) $this->update = [];
        $this->update[ $name ] = $value;
    }

    public function save()
    {
        if (!isset($this->update) || empty($this->update)) return false;

        if (!isset($this->data['id']))
        {
            throw new \Exception('The model is not loaded');
        }

        $setSQL = [];
        $values = [];

        foreach ($this->update as $key => $value)
        {
            $setSQL[] = $key . ' = ?';
            $values[] = $value;
        }

        $values[] = $this->data['id'];

        $setSQL = implode(',', $setSQL);

        $stmt = self::pdo()->prepare("
            UPDATE " . static::$table . " SET
                {$setSQL}
            WHERE id = ?
        ");

        $stmt->execute($values);

        return $stmt->rowCount() > 0;
    }

}

