<?php

namespace BeeJeeTest\Model;

use BeeJeeTest\Core\Model;

class Task extends Model {

    protected static $table = 'tasks';

    protected static $columns = [
        'id',
        'status',
        'user_name',
        'user_email',
        'content',
        'edited_by_admin',
        'created_at',
        'updated_at',
    ];

    public static function count()
    {
        $stmt = self::pdo()->prepare("
            SELECT COUNT(*) FROM " . self::$table . "
        ");

        $stmt->execute();

        return intval($stmt->fetchColumn());
    }

    public static function getList(array $params = null, array $values = [])
    {
        $stmt = self::pdo()->prepare("
            SELECT * FROM " . self::$table . "
            " . self::getSql($params) . "
        ");

        $stmt->execute($values);

        $models = [];

        while ($row = $stmt->fetch())
        {
            $model = new Task();
            $model->data = $row;
            $models[] = $model;
        }

        return $models;
    }

    public static function getById($id)
    {
        if (!isset($id)) return null;

        $stmt = self::pdo()->prepare("
            SELECT * FROM " . self::$table . "
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([ $id ]);

        $data = $stmt->fetch();

        if (!$data) return null;

        $model = new Task();
        $model->data = $data;

        return $model;
    }

    public static function create(array $data)
    {
        $data = self::sanitizeData($data);
        $keys = array_keys($data);

        if (empty($keys))
        {
            throw new \Exception('Data is empty');
        }

        $placeholders = substr(str_repeat(',?', count($keys)), 1);
        $keys = implode(',', $keys);

        $stmt = self::pdo()->prepare("
            INSERT INTO " . self::$table . " ({$keys}) VALUES ({$placeholders})
        ");

        $values = array_values($data);

        $stmt->execute($values);

        $data['id'] = self::pdo()->lastInsertId();

        $model = new Task();
        $model->data = $data;

        return $model;
    }

}
