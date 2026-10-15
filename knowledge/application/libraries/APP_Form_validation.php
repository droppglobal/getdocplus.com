<?php
class APP_Form_validation extends CI_Form_validation
{
    public $dontChangePostValue = false;
    protected $current_model_object;

    protected function _reset_post_array()
    {
        if (!$this->dontChangePostValue) {
            parent::_reset_post_array();
        }
    }

    public function SetCurrentModel($model)
    {
        $this->current_model_object = $model;
    }

    public function reset_validation()
    {
        $this->current_model_object = null;
        return parent::reset_validation();
    }

    public function valid_email($str)
    {
        if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches)) {
            $domain = defined('INTL_IDNA_VARIANT_UTS46')
                ? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46)
                : idn_to_ascii($matches[2]);
            $str = $matches[1] . '@' . $domain;
        }
        return (bool)filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
    {
        
        if (is_array($postdata)) {
            foreach ($postdata as $key => $val) {
                $this->_execute($row, $rules, $val, $key);
            }

            return;
        }

        
        $callback = FALSE;
        if (!in_array('required', $rules) && ($postdata === NULL OR $postdata === '')) {
            
            foreach ($rules as &$rule) {
                if (is_string($rule)) {
                    if (strncmp($rule, 'callback_', 9) === 0) {
                        $callback = TRUE;
                        $rules = array(1 => $rule);
                        break;
                    }
                } elseif (is_callable($rule)) {
                    $callback = TRUE;
                    $rules = array(1 => $rule);
                    break;
                }
            }

            if (!$callback) {
                return;
            }
        }

        
        if (($postdata === NULL OR $postdata === '') && !$callback) {
            if (in_array('isset', $rules, TRUE) OR in_array('required', $rules)) {
                
                $type = in_array('required', $rules) ? 'required' : 'isset';

                
                if (isset($this->_field_data[$row['field']]['errors'][$type])) {
                    $line = $this->_field_data[$row['field']]['errors'][$type];
                } elseif (isset($this->_error_messages[$type])) {
                    $line = $this->_error_messages[$type];
                } elseif (FALSE === ($line = $this->CI->lang->line('form_validation_' . $type))
                    
                    && FALSE === ($line = $this->CI->lang->line($type, FALSE))) {
                    $line = 'The field was not set';
                }

                
                $message = $this->_build_error_msg($line, $this->_translate_fieldname($row['label']));

                
                $this->_field_data[$row['field']]['error'] = $message;

                if (!isset($this->_error_array[$row['field']])) {
                    $this->_error_array[$row['field']] = $message;
                }
            }

            return;
        }

        

        
        foreach ($rules as $rule) {
            $_in_array = FALSE;

            
            
            if ($row['is_array'] === TRUE && is_array($this->_field_data[$row['field']]['postdata'])) {
                
                
                if (!isset($this->_field_data[$row['field']]['postdata'][$cycles])) {
                    continue;
                }

                $postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
                $_in_array = TRUE;
            } else {
                
                
                
                $postdata = is_array($this->_field_data[$row['field']]['postdata'])
                    ? NULL
                    : $this->_field_data[$row['field']]['postdata'];
            }

            
            $callback = $callable = FALSE;
            if (is_string($rule)) {
                if (strpos($rule, 'callback_') === 0) {
                    $rule = substr($rule, 9);
                    $callback = TRUE;
                }
            } elseif (is_callable($rule)) {
                $callable = TRUE;
            } elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1])) {
                
                $callable = $rule[0];
                $rule = $rule[1];
            }

            
            
            $param = FALSE;
            if (!$callable && preg_match('/(.*?)\[(.*)\]/', $rule, $match)) {
                $rule = $match[1];
                $param = $match[2];
            }

            
            if ($callback OR $callable !== FALSE) {
                if ($callback) {
                    if (method_exists($this->current_model_object, $rule)) { 
                        $result = $this->current_model_object->$rule($postdata, $param, $row['field']);
                    } elseif (!method_exists($this->CI, $rule)) {
                        log_message('debug', 'Unable to find callback validation rule: ' . $rule);
                        $result = FALSE;
                    } else {
                        
                        $result = $this->CI->$rule($postdata, $param);
                    }
                } else {
                    $result = is_array($rule)
                        ? $rule[0]->{$rule[1]}($postdata)
                        : $rule($postdata);

                    
                    if ($callable !== FALSE) {
                        $rule = $callable;
                    }
                }

                
                if ($_in_array === TRUE) {
                    $this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
                } else {
                    $this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
                }

                
                if (!in_array('required', $rules, TRUE) && $result !== FALSE) {
                    continue;
                }
            } elseif (!method_exists($this, $rule)) {
                
                
                if (function_exists($rule)) {
                    
                    $result = ($param !== FALSE) ? $rule($postdata, $param) : $rule($postdata);

                    if ($_in_array === TRUE) {
                        $this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
                    } else {
                        $this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
                    }
                } else {
                    log_message('debug', 'Unable to find validation rule: ' . $rule);
                    $result = FALSE;
                }
            } else {
                $result = $this->$rule($postdata, $param);

                if ($_in_array === TRUE) {
                    $this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
                } else {
                    $this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
                }
            }

            
            if ($result === FALSE) {
                
                if (!is_string($rule)) {
                    return;
                }

                
                if (isset($this->_field_data[$row['field']]['errors'][$rule])) {
                    $line = $this->_field_data[$row['field']]['errors'][$rule];
                } elseif (!isset($this->_error_messages[$rule])) {
                    if (FALSE === ($line = $this->CI->lang->line('form_validation_' . $rule))
                        
                        && FALSE === ($line = $this->CI->lang->line($rule, FALSE))) {
                        $line = $this->CI->lang->line('form_validation_error_message_not_set') . '(' . $rule . ')';
                    }
                } else {
                    $line = $this->_error_messages[$rule];
                }

                
                
                if (isset($this->_field_data[$param], $this->_field_data[$param]['label'])) {
                    $param = $this->_translate_fieldname($this->_field_data[$param]['label']);
                }

                
                $message = $this->_build_error_msg($line, $this->_translate_fieldname($row['label']), $param);

                
                $this->_field_data[$row['field']]['error'] = $message;

                if (!isset($this->_error_array[$row['field']])) {
                    $this->_error_array[$row['field']] = $message;
                }

                return;
            }
        }
    }
}