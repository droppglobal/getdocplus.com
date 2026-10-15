<?php


namespace MoTranslator;

/**
 * Simple wrapper around string buffer for
 * random access and values parsing.
 */
class StringReader
{
    private $_str;
    private $_len;

    /**
     * Constructor.
     *
     * @param string $filename Name of file to load
     */
    public function __construct($filename)
    {
        $this->_str = file_get_contents($filename);
        $this->_len = strlen($this->_str);
    }

    /**
     * Read number of bytes from given offset.
     *
     * @param int $pos   Offset
     * @param int $bytes Number of bytes to read
     *
     * @return string
     */
    public function read($pos, $bytes)
    {
        if ($pos + $bytes > $this->_len) {
            throw new ReaderException('Not enough bytes!');
        }

        return substr($this->_str, $pos, $bytes);
    }

    /**
     * Reads a 32bit integer from the stream.
     *
     * @param string $unpack Unpack string
     * @param int    $pos    Position
     *
     * @return int Ingerer from the stream
     */
    public function readint($unpack, $pos)
    {
        $data = unpack($unpack, $this->read($pos, 4));

        return $data[1];
    }

    /**
     * Reads an array of integers from the stream.
     *
     * @param string $unpack Unpack string
     * @param int    $pos    Position
     * @param int    $count  How many elements should be read
     *
     * @return array Array of Integers
     */
    public function readintarray($unpack, $pos, $count)
    {
        return unpack($unpack . $count, $this->read($pos, 4 * $count));
    }
}
