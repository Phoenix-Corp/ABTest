<?php
namespace AB;

/**
* ABTest Controller
*/
class Test extends AbstractTest
{

    private $callbacks = array();
    private $variants = array();
    private $namedVariants = array();
    private $defaultVariant;
    private $selected;

    public function __construct($name, $default_callback = false)
    {
        parent::__construct($name);

        if ($default_callback) {
            $this->addDefaultVariant($default_callback);
        }
    }

    public function hasStoredVariant()
    {
        return array_key_exists($this->getHash(), $_COOKIE);
    }

    public function getStoredVariant()
    {
        if ( $this->hasStoredVariant() ) {
            return $this->getVariant( $_COOKIE[$this->getHash()] );
        }
    }

    public function setTrigger($callback)
    {
        $this->callbacks['trigger'] = $callback;

        return $this;
    }

    public function hasTrigger()
    {
        return array_key_exists('trigger', $this->callbacks);
    }

    public function evalTrigger()
    {
        return (bool) call_user_func($this->callbacks['trigger']);
    }

    public function hasVariants()
    {
        return (bool) count($this->variants);
    }

    public function addDefaultVariant($callback)
    {
        $this->defaultVariant = $this->addVariant('default', $callback);

        return $this->defaultVariant;
    }

    public function getDefaultVariant()
    {
        $this->defaultVariant = $this->defaultVariant ?: $this->addDefaultVariant(function () {});

        return $this->defaultVariant;
    }

    public function addVariant($name, $callback)
    {
        $variant = new Variant($name, $callback);
        array_push($this->variants, $variant);
        $this->namedVariants[$name] = max(array_keys($this->variants));

        return $variant;
    }

    public function getVariant($name)
    {
        if ( array_key_exists($name, $this->namedVariants) ) {
            return $this->variants[$this->namedVariants[$name]];
        }
    }

    public function pickVariant()
    {
        $variants = array();
        foreach ($this->variants as $variant) {
            $variants = array_merge($variants, array_fill(0, $variant->getWeight(), $variant));
        }

        return $variants[array_rand($variants)];
    }
    
    private function setSelectedVariant($variant)
    {
        return $this->selected = $variant;
    }
    
    public function getSelectedVariant()
    {
        return $this->selected;
    }
    
    public function setReport($callback)
    {
        $this->callbacks['report'] = $callback;
        
        return $this;
    }

    public function proceed()
    {

        if ( $this->hasStoredVariant() ) {
            $this->setSelectedVariant( $this->getStoredVariant() );
        } else {
            if ( $this->hasVariants() && $this->hasTrigger() && $this->evalTrigger() ) {
                $this->setSelectedVariant( $this->pickVariant() );
            } else {
                $this->setSelectedVariant( $this->getDefaultVariant() );
            }
        }

        setcookie($this->getHash(), $this->getSelectedVariant()->getShortName(), time()+60*60*24*30, '/');
        
        if ( array_key_exists('report', $this->callbacks) ) {
            call_user_func($this->callbacks['report'], $this);
        }

        return call_user_func( $this->getSelectedVariant() );

    }

    public function __toString()
    {
        return (string) $this->proceed();
    }

}
