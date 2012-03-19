<?php
namespace ical\parser;

class Row
{
    public $raw;
    public $name;
    public $value;
    public $params;

    /**
     * @param string $data
     * @todo very naive splitting, not compatible with quoted strings
     *       nor is encodings such as quoted-printable detected.
     */
    public function __construct($data) {
        $name   = null;
        $value  = null;
        $params = null;
        $data   = trim($data);

        list($name, $value) = explode(":", $data, 2);

        if (strpos($name, ";") !== false) {
            list($name, $params) = explode(";", $name);
        }

        if (! is_null($params)) {
            $this->params = array();

            foreach (explode(';', $params) as $param) {
                list($paramName, $paramValue) = explode('=', $param, 2);
                $this->params[$paramName] = $paramValue;
            }
        }

        $this->raw = $data;
        $this->name = trim($name);
        $this->value = trim($value);
        $this->params = $params;
    }

    public function getParam($key) {
        if (is_array($this->params) && array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return null;
    }
}