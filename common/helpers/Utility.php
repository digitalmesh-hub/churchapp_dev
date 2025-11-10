<?php

namespace common\helpers;

use Underscore\Underscore as _;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Utility {
    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays.
     *
     * Below are some usage examples,
     *
     * ~~~
     * // working with array
     * $username = \yii\helpers\ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = \yii\helpers\ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = \yii\helpers\ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = \yii\helpers\ArrayHelper::getValue($users, 'address.street');
     * ~~~
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure $key key name of the array element, or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     * @throws InvalidParamException if $array is neither an array nor an object.
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            if (property_exists($array,$key))
                return $array->$key;
            else if (method_exists($array,$key))
                return call_user_func([$array,$key]);
            else
                return $default;
        } elseif (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }

    public static function applyColumnOptions(&$columns,$columnsOptions)
    {
        foreach($columnsOptions as $attribute => $columnOption)
        {

            foreach($columns as $key => $column)
            {
                $newColumn = null;
                if (is_string($column) && $column == $attribute )
                {
                    $newColumn = ['attribute' => $column];
                }
                else if (is_array($column) && array_key_exists('attribute',$column) && $column['attribute'] == $attribute)
                {
                    $newColumn = $column;
                }
                if ($newColumn) {
                    if (is_bool($columnOption))
                        $newColumn['visible'] = $columnOption;
                    else
                        $newColumn = _::extend($newColumn, $columnOption);
                    $columns[$key] = $newColumn;
                }
            }
        }

    }

    /**
     * Quick test if $key exists in $array and === true
     * @param $array
     * @param $key
     */
    public static function isTrue($array,$key)
    {
        return self::getValue($array,$key) === true;
    }

    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public static function merge(&$a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    \Yii::trace("array $v");
                    $res[$k] = self::merge($res[$k], $v);
                } elseif (is_object($v) && property_exists($res,$k) && is_object($res->$k)) {
                    \Yii::trace("object $v");
                    $res[$k] = (object) self::merge($res[$k], $v);
                }
                else {
                    \Yii::trace("set $k = $v");
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    public static function mappedCopy(&$to,$from,$map,$options = null)
    {
        foreach($map as $f => $t)
        {

            $fromValue = null;
            $hasValue = false;
            if (is_integer($f)) {
                $f = $t;
            }

            if (is_array($from) && array_key_exists($f,$from) )
            {
                $hasValue = true;
                $fromValue = $from[$f];
            }
            else if (is_object($from))
            {
                try
                {
                    $fromValue = $from->$f;
                    $hasValue = true;
                }
                catch (\Exception $ex)
                {

                }

            }
            $toValue = $fromValue;
            $toProp = $t;
            if (is_array($t))
            {
                $toProp = ArrayHelper::getValue($t,'property',$f);
                if (array_key_exists('transform',$t))
                {
                    $toValue = call_user_func($t['transform'],$from,$to,$f,$toProp,$toValue);
                }
            }
            else
                $toProp = $t;
            if ($hasValue)
            {
                if (is_array($to)  )
                {
                    $to[$toProp] = $toValue;
                }
                else if (is_object($to) )
                {
                    try {
                        $to->$toProp = $toValue;
                    }
                    catch(\Exception $ex)
                    {

                    }
                }
            }
        }
        return $to;
    }

    public static function strStartsWith($string,$part)
    {
        return substr($string, 0, strlen($part)) === $part;
    }

    /**
     * Return null if 0 returned
     * @param $time
     * @return null
     */
    public static function strtotime($time)
    {
        $a =  strtotime($time);
        if ($a == 0)
            return null;
        else
            return $a;
    }
    public static function buildDynamicRoute($templateRoute,$model)
    {
        $newRoute = [];
        foreach($templateRoute as $k => $i)
        {
            $newRoute[$k] = $i;
            if (is_string($k))
            {
                if (self::strStartsWith($i,'@'))
                {
                    $modelPath = substr($i,1);
                    $value = ArrayHelper::getValue($model,$modelPath);
                    $newRoute[$k] = $value;
                }
            };
        }
        return $newRoute;

    }

    public static function objectToArray($d) {
        if (is_object($d))
            $d = get_object_vars($d);

        return is_array($d) ? array_map(__METHOD__, $d) : $d;
    }

    public static function arrayToObject($d) {
        return is_array($d) ? (object) array_map(__METHOD__, $d) : $d;
    }

    public static function crypt($text, $salt = null)
    {
        if (!$salt)
        {
            $salt = Yii::$app->getSecurity()->generateRandomString(2);
            $salt = strtr($salt,'_-','Zd');
        }
        return crypt($text,$salt);
    }

    public static function keyExists($array,$key)
    {
        if (is_object($array))
            return property_exists($array,$key);
        else if (is_array($array))
            return ArrayHelper::keyExists($key,$array);
        else
            return false;
    }

    public static function utf8Cleanup($a)
    {
        foreach($a as $k => $v)
        {
            $a[$k] = preg_replace('/[[:^print:]]/', '', utf8_encode($v));
        }
        return $a;
    }


    public static function buildUrl($url, $parts=array(), $flags=false, &$new_url=false)
    {

        define('HTTP_URL_REPLACE', 1);              // Replace every part of the first URL when there's one of the second URL
        define('HTTP_URL_JOIN_PATH', 2);            // Join relative paths
        define('HTTP_URL_JOIN_QUERY', 4);           // Join query strings
        define('HTTP_URL_STRIP_USER', 8);           // Strip any user authentication information
        define('HTTP_URL_STRIP_PASS', 16);          // Strip any password authentication information
        define('HTTP_URL_STRIP_AUTH', 32);          // Strip any authentication information
        define('HTTP_URL_STRIP_PORT', 64);          // Strip explicit port numbers
        define('HTTP_URL_STRIP_PATH', 128);         // Strip complete path
        define('HTTP_URL_STRIP_QUERY', 256);        // Strip query string
        define('HTTP_URL_STRIP_FRAGMENT', 512);     // Strip any fragments (#identifier)
        define('HTTP_URL_STRIP_ALL', 1024);         // Strip anything but scheme and host

        if(!$flags)
            $flags = HTTP_URL_REPLACE;
        // Build an URL
        // The parts of the second URL will be merged into the first according to the flags argument. 
        // 
        // @param   mixed           (Part(s) of) an URL in form of a string or associative array like parse_url() returns
        // @param   mixed           Same as the first argument
        // @param   int             A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
        // @param   array           If set, it will be filled with the parts of the composed url like parse_url() would return 
    
        $keys = array('user','pass','port','path','query','fragment');

        // HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
        if ($flags & HTTP_URL_STRIP_ALL) {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
            $flags |= HTTP_URL_STRIP_PORT;
            $flags |= HTTP_URL_STRIP_PATH;
            $flags |= HTTP_URL_STRIP_QUERY;
            $flags |= HTTP_URL_STRIP_FRAGMENT;
        } else if ($flags & HTTP_URL_STRIP_AUTH) { // HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
        }

        // Parse the original URL
        // - Suggestion by Sayed Ahad Abbas
        //   In case you send a parse_url array as input
        $parse_url = !is_array($url) ? parse_url($url) : $url;

        // Scheme and Host are always replaced
        if (isset($parts['scheme']))
            $parse_url['scheme'] = $parts['scheme'];
        if (isset($parts['host']))
            $parse_url['host'] = $parts['host'];

        // (If applicable) Replace the original URL with it's new parts
        if ($flags & HTTP_URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key]))
                    $parse_url[$key] = $parts[$key];
            }
        } else {
            // Join the original URL path with the new path
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
                if (isset($parse_url['path']))
                    $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
                else
                    $parse_url['path'] = $parts['path'];
            }

            // Join the original query string with the new query string
            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
                if (isset($parse_url['query']))
                    $parse_url['query'] .= '&' . $parts['query'];
                else
                    $parse_url['query'] = $parts['query'];
            }
        }

        // Strips all the applicable sections of the URL
        // Note: Scheme and Host are never stripped
        foreach ($keys as $key) {
            if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key)))
                unset($parse_url[$key]);
        }
        $new_url = $parse_url;

        return 
             ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
            .((isset($parse_url['host'])) ? $parse_url['host'] : '')
            .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            .((isset($parse_url['path'])) ? $parse_url['path'] : '')
            .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '');
    
    }

}