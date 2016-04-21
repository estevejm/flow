<?php

namespace EJM\Flow\Common;

class Set
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @param $id
     * @param $value
     */
    public function add($id, $value)
    {
        $this->values[$id] = $value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ElementNotFoundException($id);
        }

        return $this->values[$id];
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->values[$id]);
    }
}
