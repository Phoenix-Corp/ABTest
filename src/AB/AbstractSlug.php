<?php
namespace AB;

/**
 * Slug Abstract class.
 */
abstract class AbstractSlug
{
    private $name;
    private $shortname;

    public function __construct($name)
    {
        $this->name = trim($name);

        $translit = \Transliterator::create('Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();');
        $this->shortname = preg_replace('/\s/', '-', $translit->transliterate($this->name));
    }

    public function getHash($limit = 11)
    {
        return substr(sprintf('%s_%s', __NAMESPACE__, md5($this->getShortName())), 0, $limit);
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
