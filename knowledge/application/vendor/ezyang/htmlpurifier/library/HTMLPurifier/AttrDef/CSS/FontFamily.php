<?php

/**
 * Validates a font family list according to CSS spec
 */
class HTMLPurifier_AttrDef_CSS_FontFamily extends HTMLPurifier_AttrDef
{

    protected $mask = null;

    public function __construct()
    {
        
        $l = range('a', 'z');
        
        $u = range('A', 'Z');
        
        $d = range('0', '9');
        
        $b = array_map('chr', range(0x80, 0xFF));
        
        $c = array_merge($l, $u, $d, $b);
        
        
        $this->mask = array_reduce($c, function ($carry, $value) {
            return $carry . $value;
        }, '_- ');

        
        
    }

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        static $generic_names = array(
            'serif' => true,
            'sans-serif' => true,
            'monospace' => true,
            'fantasy' => true,
            'cursive' => true
        );
        $allowed_fonts = $config->get('CSS.AllowedFonts');

        
        $fonts = explode(',', $string);
        $final = '';
        foreach ($fonts as $font) {
            $font = trim($font);
            if ($font === '') {
                continue;
            }
            
            if (isset($generic_names[$font])) {
                if ($allowed_fonts === null || isset($allowed_fonts[$font])) {
                    $final .= $font . ', ';
                }
                continue;
            }
            
            if ($font[0] === '"' || $font[0] === "'") {
                $length = strlen($font);
                if ($length <= 2) {
                    continue;
                }
                $quote = $font[0];
                if ($font[$length - 1] !== $quote) {
                    continue;
                }
                $font = substr($font, 1, $length - 2);
            }

            $font = $this->expandCSSEscape($font);

            

            if ($allowed_fonts !== null && !isset($allowed_fonts[$font])) {
                continue;
            }

            if (ctype_alnum($font) && $font !== '') {
                
                $final .= $font . ', ';
                continue;
            }

            
            
            $font = str_replace(array("\n", "\t", "\r", "\x0C"), ' ', $font);

            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            

            
            
            
            if (strspn($font, $this->mask) !== strlen($font)) {
                continue;
            }

            
            
            
            
            
            
            
            
            
            
            

            
            $final .= "'$font', ";
        }
        $final = rtrim($final, ', ');
        if ($final === '') {
            return false;
        }
        return $final;
    }

}


