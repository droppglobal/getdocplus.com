<?php

/**
 * Validates the HTML attribute style, otherwise known as CSS.
 * @note We don't implement the whole CSS specification, so it might be
 *       difficult to reuse this component in the context of validating
 *       actual stylesheet declarations.
 * @note If we were really serious about validating the CSS, we would
 *       tokenize the styles and then parse the tokens. Obviously, we
 *       are not doing that. Doing that could seriously harm performance,
 *       but would make these components a lot more viable for a CSS
 *       filtering solution.
 */
class HTMLPurifier_AttrDef_CSS extends HTMLPurifier_AttrDef
{

    /**
     * @param string $css
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($css, $config, $context)
    {
        $css = $this->parseCDATA($css);

        $definition = $config->getCSSDefinition();
        $allow_duplicates = $config->get("CSS.AllowDuplicates");


        
        
        
        
        $len = strlen($css);
        $accum = "";
        $declarations = array();
        $quoted = false;
        for ($i = 0; $i < $len; $i++) {
            $c = strcspn($css, ";'\"", $i);
            $accum .= substr($css, $i, $c);
            $i += $c;
            if ($i == $len) break;
            $d = $css[$i];
            if ($quoted) {
                $accum .= $d;
                if ($d == $quoted) {
                    $quoted = false;
                }
            } else {
                if ($d == ";") {
                    $declarations[] = $accum;
                    $accum = "";
                } else {
                    $accum .= $d;
                    $quoted = $d;
                }
            }
        }
        if ($accum != "") $declarations[] = $accum;

        $propvalues = array();
        $new_declarations = '';

        /**
         * Name of the current CSS property being validated.
         */
        $property = false;
        $context->register('CurrentCSSProperty', $property);

        foreach ($declarations as $declaration) {
            if (!$declaration) {
                continue;
            }
            if (!strpos($declaration, ':')) {
                continue;
            }
            list($property, $value) = explode(':', $declaration, 2);
            $property = trim($property);
            $value = trim($value);
            $ok = false;
            do {
                if (isset($definition->info[$property])) {
                    $ok = true;
                    break;
                }
                if (ctype_lower($property)) {
                    break;
                }
                $property = strtolower($property);
                if (isset($definition->info[$property])) {
                    $ok = true;
                    break;
                }
            } while (0);
            if (!$ok) {
                continue;
            }
            
            if (strtolower(trim($value)) !== 'inherit') {
                
                $result = $definition->info[$property]->validate(
                    $value,
                    $config,
                    $context
                );
            } else {
                $result = 'inherit';
            }
            if ($result === false) {
                continue;
            }
            if ($allow_duplicates) {
                $new_declarations .= "$property:$result;";
            } else {
                $propvalues[$property] = $result;
            }
        }

        $context->destroy('CurrentCSSProperty');

        
        
        

        foreach ($propvalues as $prop => $value) {
            $new_declarations .= "$prop:$value;";
        }

        return $new_declarations ? $new_declarations : false;

    }

}


