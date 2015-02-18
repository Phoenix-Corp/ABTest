<?php
namespace AB;

/**
 * ABTest Variation.
 */
class Variant extends AbstractVariant
{
    private $weight = 1;
    private $callback;

    public function __construct($name, $callback)
    {
        parent::__construct($name);
        $this->callback = $callback;
    }

    public function withWeight($weight)
    {
        $this->weight = (int) $weight;

        return $this;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function __invoke()
    {
        return call_user_func($this->callback);
    }
}
