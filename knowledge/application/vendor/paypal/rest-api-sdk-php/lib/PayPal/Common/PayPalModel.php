<?php

namespace PayPal\Common;

use PayPal\Validation\JsonValidator;

/**
 * Generic Model class that all API domain classes extend
 * Stores all member data in a Hashmap that enables easy
 * JSON encoding/decoding
 */
class PayPalModel
{

    private $_propMap = array();

    /**
     * OAuth Credentials to use for this call
     *
     * @var \PayPal\Auth\OAuthTokenCredential $credential
     */
    protected static $credential;

    /**
     * Sets Credential
     *
     * @deprecated Pass ApiContext to create/get methods instead
     * @param \PayPal\Auth\OAuthTokenCredential $credential
     */
    public static function setCredential($credential)
    {
        self::$credential = $credential;
    }

    /**
     * Default Constructor
     *
     * You can pass data as a json representation or array object. This argument eliminates the need
     * to do $obj->fromJson($data) later after creating the object.
     *
     * @param array|string|null $data
     * @throws \InvalidArgumentException
     */
    public function __construct($data = null)
    {
        switch (gettype($data)) {
            case "NULL":
                break;
            case "string":
                JsonValidator::validate($data);
                $this->fromJson($data);
                break;
            case "array":
                $this->fromArray($data);
                break;
            default:
        }
    }

    /**
     * Returns a list of Object from Array or Json String. It is generally used when your json
     * contains an array of this object
     *
     * @param mixed $data Array object or json string representation
     * @return array
     */
    public static function getList($data)
    {
                if ($data === null) {
            return null;
        }

        if (is_a($data, get_class(new \stdClass()))) {
                        return new static(json_encode($data));
        }

        $list = array();

        if (is_array($data)) {
            $data = json_encode($data);
        }

        if (JsonValidator::validate($data)) {
                        $decoded = json_decode($data);
            if ($decoded === null) {
                return $list;
            }
            if (is_array($decoded)) {
                foreach ($decoded as $k => $v) {
                    $list[] = self::getList($v);
                }
            }
            if (is_a($decoded, get_class(new \stdClass()))) {
                                $list[] = new static(json_encode($decoded));
            }
        }

        return $list;
    }

    /**
     * Magic Get Method
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->__isset($key)) {
            return $this->_propMap[$key];
        }
        return null;
    }

    /**
     * Magic Set Method
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if (!is_array($value) && $value === null) {
            $this->__unset($key);
        } else {
            $this->_propMap[$key] = $value;
        }
    }

    /**
     * Converts the input key into a valid Setter Method Name
     *
     * @param $key
     * @return mixed
     */
    private function convertToCamelCase($key)
    {
        return str_replace(' ', '', ucwords(str_replace(array('_', '-'), ' ', $key)));
    }

    /**
     * Magic isSet Method
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->_propMap[$key]);
    }

    /**
     * Magic Unset Method
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->_propMap[$key]);
    }

    /**
     * Converts Params to Array
     *
     * @param $param
     * @return array
     */
    private function _convertToArray($param)
    {
        $ret = array();
        foreach ($param as $k => $v) {
            if ($v instanceof PayPalModel) {
                $ret[$k] = $v->toArray();
            } elseif (is_array($v) && sizeof($v) <= 0) {
                $ret[$k] = array();
            } elseif (is_array($v)) {
                $ret[$k] = $this->_convertToArray($v);
            } else {
                $ret[$k] = $v;
            }
        }
                                if (sizeof($ret) <= 0) {
            $ret = new PayPalModel();
        }
        return $ret;
    }

    /**
     * Fills object value from Array list
     *
     * @param $arr
     * @return $this
     */
    public function fromArray($arr)
    {
        if (!empty($arr)) {
                        foreach ($arr as $k => $v) {
                                if (is_array($v)) {
                                        if (($clazz = ReflectionUtil::getPropertyClass(get_class($this), $k)) != null) {
                                                if (empty($v)) {
                            if (ReflectionUtil::isPropertyClassArray(get_class($this), $k)) {
                                                                $this->assignValue($k, array());
                                continue;
                            }
                            $o = new $clazz();
                            $this->assignValue($k, $o);
                        } elseif (ArrayUtil::isAssocArray($v)) {
                            /** @var self $o */
                            $o = new $clazz();
                            $o->fromArray($v);
                            $this->assignValue($k, $o);
                        } else {
                                                        $arr = array();
                                                        foreach ($v as $nk => $nv) {
                                if (is_array($nv)) {
                                    $o = new $clazz();
                                    $o->fromArray($nv);
                                    $arr[$nk] = $o;
                                } else {
                                    $arr[$nk] = $nv;
                                }
                            }
                            $this->assignValue($k, $arr);
                        }
                    } else {
                        $this->assignValue($k, $v);
                    }
                } else {
                    $this->assignValue($k, $v);
                }
            }
        }
        return $this;
    }

    private function assignValue($key, $value)
    {
        $setter = 'set'. $this->convertToCamelCase($key);
                if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            $this->__set($key, $value);
        }
    }

    /**
     * Fills object value from Json string
     *
     * @param $json
     * @return $this
     */
    public function fromJson($json)
    {
        return $this->fromArray(json_decode($json, true));
    }

    /**
     * Returns array representation of object
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_convertToArray($this->_propMap);
    }

    /**
     * Returns object JSON representation
     *
     * @param int $options http://php.net/manual/en/json.constants.php
     * @return string
     */
    public function toJSON($options = 0)
    {
                                if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($this->toArray(), $options | 64);
        }
        return str_replace('\\/', '/', json_encode($this->toArray(), $options));
    }

    /**
     * Magic Method for toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJSON(128);
    }
}
