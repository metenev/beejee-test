<?php

namespace BeeJeeTest\Helper;

use BeeJeeTest\Core\Session;

class FormAlerts {

    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';

    const FIELD_GLOBAL = '__global__';
    const SESSION_FIELD_FORM_ALERTS = '__form_alerts__';

    protected $alerts;

    public static function getClassFromType($type)
    {
        switch ($type)
        {
            case self::TYPE_INFO:
                return 'alert-info';

            case self::TYPE_SUCCESS:
                return 'alert-success';

            case self::TYPE_WARNING:
                return 'alert-warning';

            case self::TYPE_ERROR:
                return 'alert-danger';

            default:
                return '';
        }
    }

    public function __construct()
    {
        $this->alerts = [];
    }

    public function add($message, $type = self::TYPE_ERROR, $field = self::FIELD_GLOBAL)
    {
        if (!isset($this->alerts[ $field ])) $this->alerts[ $field ] = [];

        $this->alerts[ $field ][] = [
            'message' => $message,
            'type' => $type,
            'type_class' => self::getClassFromType($type),
        ];
    }

    public function fillFromSession(Session $session, $clear = true)
    {
        $alerts = $session->get(self::SESSION_FIELD_FORM_ALERTS);

        if (!isset($alerts)) return;

        foreach ($alerts as $field => $data)
        {
            if (!isset($this->alerts[ $field ])) $this->alerts[ $field ] = $data;
        }

        if ($clear) $session->remove(self::SESSION_FIELD_FORM_ALERTS);
    }

    public function saveToSession(Session $session)
    {
        $session->set(self::SESSION_FIELD_FORM_ALERTS, $this->alerts);
    }

    public function has($field = self::FIELD_GLOBAL)
    {
        return isset($this->alerts[ $field ]);
    }

    public function isEmpty()
    {
        return empty($this->alerts);
    }

    public function get($field = self::FIELD_GLOBAL)
    {
        return $this->has($field) ? $this->alerts[ $field ] : null;
    }

    public function getJSON()
    {
        $result = [];

        foreach ($this->alerts as $field => $alerts)
        {
            foreach ($alerts as $item)
            {
                $resultItem = [
                    'message' => $item['message'],
                    'type' => $item['type'],
                ];

                if ($field != self::FIELD_GLOBAL) {
                    $resultItem['field'] = $field;
                }

                $result[] = $resultItem;
            }
        }

        return $result;
    }

}
