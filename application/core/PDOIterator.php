<?php
abstract class Record_Iterator_Abstract implements Iterator, Countable
{
    /**
     *
     * @var string The record class (model)
     */
    protected $recordClass;
    public function __construct($recordClass)
    {
        $this->recordClass = $recordClass;
    }
    /**
     * Casts a document to a new instance specified in the $recordClass
     * @param array $item
     * @return Record_Abstract|false
     */
    protected function cast($item)
    {
        if (!is_array($item))
            return false;
        $rc = $this->recordClass;
        return new $rc($item, true);
    }
    /**
     * Returns the first record
     * @return Record_Abstract|false
     */
    abstract public function getFirst();
    /**
     * Return the next object to which this cursor points, and advance the cursor
     * @return Record_Abstract|false
     */
    abstract public function getNext();
    /**
     * Alias for current()
     * @return Record_Abstract|false
     */
    abstract public function getCurrent();
    /**
     * Returns the collection of records
     * @return array[Record_Abstract]
     */
    abstract public function getAll();
}

class Record_Iterator_PDO extends Record_Iterator_Abstract
{
    /**
     *
     * @var PDOStatement
     */
    protected $pdoStatement;
    /**
     *
     * @var int
     */
    protected $cursor = -1;
    /**
     *
     * @var array
     */
    protected $current = false;
    public function __construct($recordClass, PDOStatement $pdoStatement)
    {
        parent::__construct($recordClass);
        $this->pdoStatement = $pdoStatement;
    }
    /**
     * Counts found records
     * @return int
     */
    public function count()
    {
        return $this->pdoStatement->rowCount();
    }
    /**
     * Fetches record at current cursor. Alias for current()
     * @return Record_PDO|false
     */
    public function current()
    {
        return $this->current ? $this->cast($this->current) : false;
    }
    /**
     * Returns the current result's _id
     * @return string The current result's _id as a string.
     */
    public function key()
    {
        return $this->cursor;
    }
    /**
     * Advances the cursor to the next result
     * @return boolean
     */
    public function next()
    {
        if ($this->hasNext()) {
            $this->cursor++;
            $this->current = $this->pdoStatement->fetch(PDO::FETCH_ASSOC);
            if (empty($this->current))
                $this->current = false;
            else
                return true;
        }else
            $this->current = false;
        return false;
    }
    /**
     * Moves the cursor to the beginning of the result set
     */
    public function rewind()
    {
        if ($this->cursor != -1) {
            $this->cursor = -1;
            $this->pdoStatement->execute();
        } else {
            $this->next();
        }
    }
    /**
     * Checks if the cursor is reading a valid result.
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->current != false) || (($this->cursor == -1) && ($this->count() > 0));
    }
    /**
     * Fetches first record and rewinds the cursor
     * @return Record_PDO|false
     */
    public function getFirst()
    {
        $this->rewind();
        return $this->getNext();
    }
    /**
     *
     * @return boolean
     */
    public function hasNext()
    {
        return ($this->count() > 0) && (($this->cursor + 1) < $this->count());
    }
    /**
     * Return the next record to which this cursor points, and advance the cursor
     * @return Record_PDO|false Next record or false if there's no more records
     */
    public function getNext()
    {
        if ($this->next()) {
            return $this->cast($this->current);
        } else {
            $this->rewind();
        }
        return false;
    }
    /**
     * Fetches the record at current cursor. Alias for current()
     * @return Record_PDO|false
     */
    public function getCurrent()
    {
        return $this->current();
    }
    /**
     * Fetches all records (this could impact into your site performance) and rewinds the cursor
     * @param boolean $asRecords Bind into record class?
     * @return array[Record_PDO|array] Array of records or arrays (depends on $asRecords)
     */
    public function getAll($asRecords = true)
    {
        $all = array();
        $this->rewind();
        foreach ($this->pdoStatement as $id => $doc) {
            if ($asRecords)
                $all[$id] = $this->cast($doc);
            else
                $all[$id] = $doc;
        }
        return $all;
    }
    /**
     *
     * @return PDOStatement
     */
    public function getPDOStatement()
    {
        return $this->pdoStatement;
    }
}