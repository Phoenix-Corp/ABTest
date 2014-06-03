<?php
namespace AB;

/**
* ABTest Abstract class
*/
abstract class AbstractABTest
{
    private $name;
    private $shortname;

    public function __construct($name)
    {
        $this->name = trim($name);

        $translit = \Transliterator::create('Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();');
        $this->shortname = preg_replace('/\s/', '-', $translit->transliterate($this->name));
    }

    public function getShortName()
    {
        return $this->shortname;
    }

    public function getName()
    {
        return $this->name;
    }

}
