<?php
class CachedPDOStatement extends CachingIterator
{
    private $index;
    public function __construct(PDOStatement $statement) {
        parent::__construct(new IteratorIterator($statement), self::FULL_CACHE);
    }
    public function rewind() {
        if (NULL === $this->index) {
            parent::rewind();
        }
        $this->index = 0;
    }
    public function current() {
        if ($this->offsetExists($this->index)) {
            return $this->offsetGet($this->index);
        }
        return parent::current();
    }
    public function key() {
        return $this->index;
    }
    public function next() {
        $this->index++;
        if (!$this->offsetExists($this->index)) {
            parent::next();
        }
    }
    public function valid() {
        return $this->offsetExists($this->index) || parent::valid();
    }
}