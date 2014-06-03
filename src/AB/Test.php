<?php
namespace AB;

/**
* ABTest Controller
*/
class Test extends AbstractABTest
{

    private $trigger;
    private $variations = array();
    private $namedVariations = array();
    private $defaultVariation;

    public function __construct($name, $default_callback = false)
    {
        parent::__construct($name);

        if ($default_callback) {
            $this->addDefaultVariation($default_callback);
        }
    }

    public function trigger($callback)
    {
        $this->trigger = $callback;
    }

    public function hasTrigger()
    {
        return (bool) $this->trigger;
    }

    public function evalTrigger()
    {
        return (bool) call_user_func($this->trigger);
    }

    public function addDefaultVariation($callback)
    {
        $this->defaultVariation = $this->addVariation('default', $callback);

        return $this->defaultVariation;
    }

    public function getDefaultVariation()
    {
        $this->defaultVariation = $this->defaultVariation ?: $this->addDefaultVariation(function () {});

        return $this->defaultVariation;
    }

    public function addVariation($name, $callback)
    {
        $variation = new Variation($name, $callback);
        array_push($this->variations, $variation);
        $this->namedVariations[$name] = max(array_keys($this->variations));

        return $variation;
    }

    public function getVariation($name)
    {
        if ( array_key_exists($name, $this->namedVariations) ) {
            return $this->variations[$this->namedVariations[$name]];
        }
    }

    public function pickVariation()
    {
        $variations = array();
        foreach ($this->variations as $variation) {
            $variations = array_merge($variations, array_fill(0, $variation->getWeight(), $variation));
        }

        return $variations[array_rand($variations)];
    }

    public function proceed()
    {

        if ( $this->hasStoredVariation() ) {
            $selected = $this->getVariation( $this->storedVariation() );
        } else {

            if ( $this->hasVariations() && $this->hasTrigger() && $this->evalTrigger() ) {
                $selected = $this->pickVariation();
            } else {
                $selected = $this->getDefaultVariation();
            }
        }

        return call_user_func($selected);

    }

    public function __toString()
    {
        return (string) $this->proceed();
    }

}
