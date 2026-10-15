<?php

/**
 * Validate all attributes in the tokens.
 */

class HTMLPurifier_Strategy_ValidateAttributes extends HTMLPurifier_Strategy
{

    /**
     * @param HTMLPurifier_Token[] $tokens
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return HTMLPurifier_Token[]
     */
    public function execute($tokens, $config, $context)
    {
        
        $validator = new HTMLPurifier_AttrValidator();

        $token = false;
        $context->register('CurrentToken', $token);

        foreach ($tokens as $key => $token) {

            
            
            if (!$token instanceof HTMLPurifier_Token_Start && !$token instanceof HTMLPurifier_Token_Empty) {
                continue;
            }

            
            if (!empty($token->armor['ValidateAttributes'])) {
                continue;
            }

            
            $validator->validateToken($token, $config, $context);
        }
        $context->destroy('CurrentToken');
        return $tokens;
    }
}


