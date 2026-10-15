<?php


namespace MoTranslator;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Provides a simple gettext replacement that works independently from
 * the system's gettext abilities.
 * It can read MO files and use them for translating strings.
 *
 * It caches ll strings and translations to speed up the string lookup.
 */
class Translator
{
    /**
     * None error.
     */
    const ERROR_NONE = 0;
    /**
     * File does not exist.
     */
    const ERROR_DOES_NOT_EXIST = 1;
    /**
     * File has bad magic number.
     */
    const ERROR_BAD_MAGIC = 2;
    /**
     * Error while reading file, probably too short.
     */
    const ERROR_READING = 3;

    /**
     * Big endian mo file magic bytes.
     */
    const MAGIC_BE = "\x95\x04\x12\xde";
    /**
     * Little endian mo file magic bytes.
     */
    const MAGIC_LE = "\xde\x12\x04\x95";

    /**
     * Parse error code (0 if no error).
     *
     * @var int
     */
    public $error = self::ERROR_NONE;

    /**
     * Cache header field for plural forms.
     *
     * @var string|null
     */
    private $pluralequation = null;
    /**
     * @var ExpressionLanguage|null Evaluator for plurals
     */
    private $pluralexpression = null;
    /**
     * @var int|null number of plurals
     */
    private $pluralcount = null;
    /**
     * Array with original -> translation mapping.
     *
     * @var array
     */
    private $cache_translations = array();

    /**
     * Constructor.
     *
     * @param string $filename Name of mo file to load
     */
    public function __construct($filename)
    {
        if (!is_readable($filename)) {
            $this->error = self::ERROR_DOES_NOT_EXIST;

            return;
        }

        $stream = new StringReader($filename);

        try {
            $magic = $stream->read(0, 4);
            if (strcmp($magic, self::MAGIC_LE) == 0) {
                $unpack = 'V';
            } elseif (strcmp($magic, self::MAGIC_BE) == 0) {
                $unpack = 'N';
            } else {
                $this->error = self::ERROR_BAD_MAGIC;

                return;
            }

            
            $total = $stream->readint($unpack, 8);
            $originals = $stream->readint($unpack, 12);
            $translations = $stream->readint($unpack, 16);

            
            $table_originals = $stream->readintarray($unpack, $originals, $total * 2);
            $table_translations = $stream->readintarray($unpack, $translations, $total * 2);

            
            for ($i = 0; $i < $total; ++$i) {
                $original = $stream->read($table_originals[$i * 2 + 2], $table_originals[$i * 2 + 1]);
                $translation = $stream->read($table_translations[$i * 2 + 2], $table_translations[$i * 2 + 1]);
                $this->cache_translations[$original] = $translation;
            }
        } catch (ReaderException $e) {
            $this->error = self::ERROR_READING;

            return;
        }
    }

    /**
     * Translates a string.
     *
     * @param string $msgid String to be translated
     *
     * @return string translated string (or original, if not found)
     */
    public function gettext($msgid)
    {
        if (array_key_exists($msgid, $this->cache_translations)) {
            return $this->cache_translations[$msgid];
        } else {
            return $msgid;
        }
    }
    public function getNumber($number)
    {
    	$newNumber="";
    	$len=strlen($number);
    	for($i=0;$i<$len;$i++){
    		$newNumber.=$this->gettext($number[$i]);
    	}
    	return $newNumber;
    }
    
    /**
     * Sanitize plural form expression for use in ExpressionLanguage.
     *
     * @param string $expr Expression to sanitize
     *
     * @return string sanitized plural form expression
     */
    public static function sanitizePluralExpression($expr)
    {
                $expr = explode(';', $expr);
        if (count($expr) >= 2) {
            $expr = $expr[1];
        } else {
            $expr = $expr[0];
        }
        $expr =app_trim(strtolower($expr));
                if (substr($expr, 0, 6) === 'plural') {
            $expr = ltrim(substr($expr, 6));
        }
                if (substr($expr, 0, 1) === '=') {
            $expr = ltrim(substr($expr, 1));
        }

        return $expr;
    }

    /**
     * Extracts number of plurals from plurals form expression.
     *
     * @param string $expr Expression to process
     *
     * @return int Total number of plurals
     */
    public static function extractPluralCount($expr)
    {
        $parts = explode(';', $expr, 2);
        $nplurals = explode('=',app_trim($parts[0]), 2);
        if (strtolower(rtrim($nplurals[0])) != 'nplurals') {
            return 1;
        }

        return intval($nplurals[1]);
    }

    /**
     * Parse full PO header and extract only plural forms line.
     *
     * @param string $header Gettext header
     *
     * @return string verbatim plural form header field
     */
    public static function extractPluralsForms($header)
    {
        $headers = explode("\n", $header);
        $expr = 'nplurals=2; plural=n == 1 ? 0 : 1;';
        foreach ($headers as $header) {
            if (stripos($header, 'Plural-Forms:') === 0) {
                $expr = substr($header, 13);
            }
        }

        return $expr;
    }

    /**
     * Get possible plural forms from MO header.
     *
     * @return string plural form header
     */
    private function getPluralForms()
    {
                
                if (is_null($this->pluralequation)) {
            $header = $this->cache_translations[''];
            $expr = $this->extractPluralsForms($header);
            $this->pluralequation = $this->sanitizePluralExpression($expr);
            $this->pluralcount = $this->extractPluralCount($expr);
        }

        return $this->pluralequation;
    }

    /**
     * Detects which plural form to take.
     *
     * @param int $n count of objects
     *
     * @return int array index of the right plural form
     */
    private function selectString($n)
    {
        if (is_null($this->pluralexpression)) {
            $this->pluralexpression = new ExpressionLanguage();
        }
        $plural = $this->pluralexpression->evaluate(
            $this->getPluralForms(), array('n' => $n)
        );

        if ($plural >= $this->pluralcount) {
            $plural = $this->pluralcount - 1;
        }

        return $plural;
    }

    /**
     * Plural version of gettext.
     *
     * @param string $msgid       Single form
     * @param string $msgidPlural Plural form
     * @param int    $number      Number of objects
     *
     * @return string translated plural form
     */
    public function ngettext($msgid, $msgidPlural, $number)
    {
                $key = implode(chr(0), array($msgid, $msgidPlural));
        if (!array_key_exists($key, $this->cache_translations)) {
            return ($number != 1) ? $msgidPlural : $msgid;
        }

                $select = $this->selectString($number);

        $result = $this->cache_translations[$key];
        $list = explode(chr(0), $result);

        return $list[$select];
    }

    /**
     * Translate with context.
     *
     * @param string $msgctxt Context
     * @param string $msgid   String to be translated
     *
     * @return string translated plural form
     */
    public function pgettext($msgctxt, $msgid)
    {
        $key = implode(chr(4), array($msgctxt, $msgid));
        $ret = $this->gettext($key);
        if (strpos($ret, chr(4)) !== false) {
            return $msgid;
        } else {
            return $ret;
        }
    }

    /**
     * Plural version of pgettext.
     *
     * @param string $msgctxt     Context
     * @param string $msgid       Single form
     * @param string $msgidPlural Plural form
     * @param int    $number      Number of objects
     *
     * @return string translated plural form
     */
    public function npgettext($msgctxt, $msgid, $msgidPlural, $number)
    {
        $key = implode(chr(4), array($msgctxt, $msgid));
        $ret = $this->ngettext($key, $msgidPlural, $number);
        if (strpos($ret, chr(4)) !== false) {
            return $msgid;
        } else {
            return $ret;
        }
    }
}
