<?php

/**
 * Injector that auto paragraphs text in the root node based on
 * double-spacing.
 * @todo Ensure all states are unit tested, including variations as well.
 * @todo Make a graph of the flow control for this Injector.
 */
class HTMLPurifier_Injector_AutoParagraph extends HTMLPurifier_Injector
{
    /**
     * @type string
     */
    public $name = 'AutoParagraph';

    /**
     * @type array
     */
    public $needed = array('p');

    /**
     * @return HTMLPurifier_Token_Start
     */
    private function _pStart()
    {
        $par = new HTMLPurifier_Token_Start('p');
        $par->armor['MakeWellFormed_TagClosedError'] = true;
        return $par;
    }

    /**
     * @param HTMLPurifier_Token_Text $token
     */
    public function handleText(&$token)
    {
        $text = $token->data;
        
        if ($this->allowsElement('p')) {
            if (empty($this->currentNesting) || strpos($text, "\n\n") !== false) {
                
                
                
                

                $i = $nesting = null;
                if (!$this->forwardUntilEndToken($i, $current, $nesting) && $token->is_whitespace) {
                    
                    
                    
                } else {
                    if (!$token->is_whitespace || $this->_isInline($current)) {
                        
                        

                        
                        

                        
                        
                        $token = array($this->_pStart());
                        $this->_splitText($text, $token);
                    } else {
                        
                        
                    }
                }
            } else {
                
                

                
                
                if ($this->_pLookAhead()) {
                    
                    
                    
                    
                    
                    
                    $token = array($this->_pStart(), $token);
                } else {
                    
                    

                    
                    
                }
            }
            
        } elseif (!empty($this->currentNesting) &&
            $this->currentNesting[count($this->currentNesting) - 1]->name == 'p') {
            
            

            
            
            $token = array();
            $this->_splitText($text, $token);
            
        } else {
            
            

            
            
        }
    }

    /**
     * @param HTMLPurifier_Token $token
     */
    public function handleElement(&$token)
    {
        
        
        if ($this->allowsElement('p')) {
            if (!empty($this->currentNesting)) {
                if ($this->_isInline($token)) {
                    
                    
                    
                    
                    $i = null;
                    $this->backward($i, $prev);

                    if (!$prev instanceof HTMLPurifier_Token_Start) {
                        
                        if ($prev instanceof HTMLPurifier_Token_Text &&
                            substr($prev->data, -2) === "\n\n"
                        ) {
                            
                            
                            
                            $token = array($this->_pStart(), $token);
                        } else {
                            
                            
                            
                            
                            
                            
                        }
                    } else {
                        
                        
                        
                        if ($this->_pLookAhead()) {
                            
                            
                            $token = array($this->_pStart(), $token);
                        } else {
                            
                            

                            
                            
                        }
                    }
                } else {
                    
                    
                }
            } else {
                if ($this->_isInline($token)) {
                    
                    
                    
                    
                    $token = array($this->_pStart(), $token);
                } else {
                    
                    
                }

                $i = null;
                if ($this->backward($i, $prev)) {
                    if (!$prev instanceof HTMLPurifier_Token_Text) {
                        
                        
                        
                        
                        if (!is_array($token)) {
                            $token = array($token);
                        }
                        array_unshift($token, new HTMLPurifier_Token_Text("\n\n"));
                    } else {
                        
                        
                        
                        
                        
                        
                    }
                }
            }
        } else {
            
            
            
            
        }
    }

    /**
     * Splits up a text in paragraph tokens and appends them
     * to the result stream that will replace the original
     * @param string $data String text data that will be processed
     *    into paragraphs
     * @param HTMLPurifier_Token[] $result Reference to array of tokens that the
     *    tags will be appended onto
     */
    private function _splitText($data, &$result)
    {
        $raw_paragraphs = explode("\n\n", $data);
        $paragraphs = array(); 
        $needs_start = false;
        $needs_end = false;

        $c = count($raw_paragraphs);
        if ($c == 1) {
            
            
            $result[] = new HTMLPurifier_Token_Text($data);
            return;
        }
        for ($i = 0; $i < $c; $i++) {
            $par = $raw_paragraphs[$i];
            if (trim($par) !== '') {
                $paragraphs[] = $par;
            } else {
                if ($i == 0) {
                    
                    if (empty($result)) {
                        
                        
                        
                        
                        $result[] = new HTMLPurifier_Token_End('p');
                        $result[] = new HTMLPurifier_Token_Text("\n\n");
                        
                        
                        
                        
                        
                        $needs_start = true;
                    } else {
                        
                        
                        
                        array_unshift($result, new HTMLPurifier_Token_Text("\n\n"));
                    }
                } elseif ($i + 1 == $c) {
                    
                    
                    $needs_end = true;
                }
            }
        }

        
        
        if (empty($paragraphs)) {
            return;
        }

        
        if ($needs_start) {
            $result[] = $this->_pStart();
        }

        
        foreach ($paragraphs as $par) {
            $result[] = new HTMLPurifier_Token_Text($par);
            $result[] = new HTMLPurifier_Token_End('p');
            $result[] = new HTMLPurifier_Token_Text("\n\n");
            $result[] = $this->_pStart();
        }

        
        
        
        array_pop($result);

        
        
        if (!$needs_end) {
            array_pop($result); 
            array_pop($result); 
        }
    }

    /**
     * Returns true if passed token is inline (and, ergo, allowed in
     * paragraph tags)
     * @param HTMLPurifier_Token $token
     * @return bool
     */
    private function _isInline($token)
    {
        return isset($this->htmlDefinition->info['p']->child->elements[$token->name]);
    }

    /**
     * Looks ahead in the token list and determines whether or not we need
     * to insert a <p> tag.
     * @return bool
     */
    private function _pLookAhead()
    {
        if ($this->currentToken instanceof HTMLPurifier_Token_Start) {
            $nesting = 1;
        } else {
            $nesting = 0;
        }
        $ok = false;
        $i = null;
        while ($this->forwardUntilEndToken($i, $current, $nesting)) {
            $result = $this->_checkNeedsP($current);
            if ($result !== null) {
                $ok = $result;
                break;
            }
        }
        return $ok;
    }

    /**
     * Determines if a particular token requires an earlier inline token
     * to get a paragraph. This should be used with _forwardUntilEndToken
     * @param HTMLPurifier_Token $current
     * @return bool
     */
    private function _checkNeedsP($current)
    {
        if ($current instanceof HTMLPurifier_Token_Start) {
            if (!$this->_isInline($current)) {
                
                
                
                return false;
            }
        } elseif ($current instanceof HTMLPurifier_Token_Text) {
            if (strpos($current->data, "\n\n") !== false) {
                
                
                return true;
            } else {
                
                
            }
        }
        return null;
    }
}


