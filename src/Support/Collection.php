<?php
// +----------------------------------------------------------------------
// | Collection.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------

/**
 * @see      https://github.com/overtrue
 */

namespace limx\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

/**
 * Class Collection.
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable
{
    /**
     * The collection data.
     *
     * @var array
     */
    protected $items = [];

    /**
     * set data.
     *
     * @param mixed $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Return all items.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Return specific items.
     *
     * @param array $keys
     *
     * @return array
     */
    public function only(array $keys)
    {
        $return = [];

        foreach ($keys as $key) {
            $value = $this->get($key);

            if (!is_null($value)) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param mixed $keys
     *
     * @return static
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(Arr::except($this->items, $keys));
    }

    /**
     * Merge data.
     *
     * @param Collection|array $items
     *
     * @return array
     */
    public function merge($items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }

        return $this->all();
    }

    /**
     * To determine Whether the specified element exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return !is_null(Arr::get($this->items, $key));
    }

    /**
     * Retrieve the first item.
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * Retrieve the last item.
     *
     * @return bool
     */
    public function last()
    {
        $end = end($this->items);

        reset($this->items);

        return $end;
    }

    /**
     * add the item value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function add($key, $value)
    {
        Arr::set($this->items, $key, $value);
    }

    /**
     * Set the item value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        Arr::set($this->items, $key, $value);
    }

    /**
     * Retrieve item from Collection.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    /**
     * Remove item form Collection.
     *
     * @param string $key
     */
    public function forget($key)
    {
        Arr::forget($this->items, $key);
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  callable|null $callback
     * @return static
     */
    public function filter(callable $callback = null)
    {
        if ($callback) {
            return new static(Arr::where($this->items, $callback));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return static
     */
    public function where($key, $operator, $value = null)
    {
        if (func_num_args() == 2) {
            $value = $operator;

            $operator = '=';
        }

        return $this->filter($this->operatorForWhere($key, $operator, $value));
    }

    /**
     * Group an associative array by a field or using a callback.
     *
     * @param  callable|string $groupBy
     * @param  bool            $preserveKeys
     * @return static
     */
    public function groupBy($groupBy, $preserveKeys = false)
    {
        $groupBy = $this->valueRetriever($groupBy);

        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKeys = $groupBy($value, $key);

            if (!is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }

            foreach ($groupKeys as $groupKey) {
                $groupKey = is_bool($groupKey) ? (int)$groupKey : $groupKey;

                if (!array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = new static;
                }

                $results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
            }
        }

        return new static($results);
    }

    /**
     * Get an operator checker callback.
     *
     * @param  string $key
     * @param  string $operator
     * @param  mixed  $value
     * @return \Closure
     */
    protected function operatorForWhere($key, $operator, $value)
    {
        return function ($item) use ($key, $operator, $value) {
            $retrieved = Arr::dataGet($item, $key);

            switch ($operator) {
                default:
                case '=':
                case '==':
                    return $retrieved == $value;
                case '!=':
                case '<>':
                    return $retrieved != $value;
                case '<':
                    return $retrieved < $value;
                case '>':
                    return $retrieved > $value;
                case '<=':
                    return $retrieved <= $value;
                case '>=':
                    return $retrieved >= $value;
                case '===':
                    return $retrieved === $value;
                case '!==':
                    return $retrieved !== $value;
            }
        };
    }

    /**
     * Get a value retrieving callback.
     *
     * @param  string $value
     * @return callable
     */
    protected function valueRetriever($value)
    {
        if ($this->useAsCallable($value)) {
            return $value;
        }

        return function ($item) use ($value) {
            return Arr::dataGet($item, $value);
        };
    }

    /**
     * Determine if the given value is callable, but not a string.
     *
     * @param  mixed $value
     * @return bool
     */
    protected function useAsCallable($value)
    {
        return !is_string($value) && is_callable($value);
    }

    /**
     * Build to array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * Build to json.
     *
     * @param int $option
     *
     * @return string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->all(), $option);
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON.
     *
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object.
     *
     * @see http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator.
     *
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *                     <b>Traversable</b>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object.
     *
     * @see http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *             </p>
     *             <p>
     *             The return value is cast to an integer
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object.
     *
     * @see  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return mixed|void
     */
    public function unserialize($serialized)
    {
        return $this->items = unserialize($serialized);
    }

    /**
     * Get a data by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Assigns a value to the specified data.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Whether or not an data exists by key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Unsets an data by key.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        $this->forget($key);
    }

    /**
     * var_export.
     *
     * @return array
     */
    public function __set_state()
    {
        return $this->all();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return bool true on success or false on failure.
     *              The return value will be casted to boolean if non-boolean was returned
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->forget($offset);
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->get($offset) : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
}
