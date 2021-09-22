<?php

class Age_Gate_Validation extends GUMP
{

  /**
     * Sanitize the input data.
     *
     * @param array $input
     * @param null  $fields
     * @param bool  $utf8_encode
     *
     * @return array
     */
    public function sanitize(array $input, array $fields = array(), $utf8_encode = true)
    {
        if (empty($fields)) {
            $fields = array_keys($input);
        }

        $return = array();

        foreach ($fields as $field) {
            if (!isset($input[$field])) {
                continue;
            } else {
                $value = $input[$field];
                if (is_array($value)) {
                    $value = $this->sanitize($value);
                }
                if (is_string($value)) {
                    if (strpos($value, "\r") !== false) {
                        $value = trim($value);
                    }

                    if (function_exists('iconv') && function_exists('mb_detect_encoding') && $utf8_encode) {
                        $current_encoding = mb_detect_encoding($value);

                        if ($current_encoding != 'UTF-8' && $current_encoding != 'UTF-16') {
                            $value = iconv($current_encoding, 'UTF-8', $value);
                        }
                    }

                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                }

                $return[$field] = $value;
            }
        }

        return $return;
    }

    /**
     * Process the validation errors and return human readable error messages.
     *
     * @param bool   $convert_to_string = false
     * @param string $field_class
     * @param string $error_class
     *
     * @return array
     * @return string
     */
    public function get_readable_errors($convert_to_string = false, $field_class = 'age-gate-field', $error_class = 'gump-error-message')
    {
        if (empty($this->errors)) {
            return ($convert_to_string) ? null : array();
        }

        $resp = array();

        // Error messages
        $messages = $this->get_messages();

        foreach ($this->errors as $e) {
            $field = ucwords(str_replace($this->fieldCharsToRemove, chr(32), $e['field']));
            $param = $e['param'];

            // Let's fetch explicitly if the field names exist
            if (array_key_exists($e['field'], self::$fields)) {
                $field = self::$fields[$e['field']];

                // If param is a field (i.e. equalsfield validator)
                if (array_key_exists($param, self::$fields)) {
                    $param = self::$fields[$e['param']];
                }
            }

            // Messages
            if (isset($messages[$e['rule']])) {
                if (is_array($param)) {
                    $param = implode(', ', $param);
                }
                $message = str_replace('{param}', $param, str_replace('{field}', $field, $messages[$e['rule']]));

                if (!array_key_exists($e['field'], $resp)) {
                    $resp[$e['field']] = [
                  'rule' => str_replace('validate_', '', $e['rule']),
                  'message' => $message
                ];
                }
            } else {
                throw new \Exception('Rule "'.$e['rule'].'" does not have an error message');
            }
        }

        if (!$convert_to_string) {
            return $resp;
        } else {
            $buffer = '';
            foreach ($resp as $s) {
                $buffer .= "<span class=\"$error_class\">$s</span>";
            }
            return $buffer;
        }
    }

    /**
     * Get error messages.
     *
     * @return array
     */
    protected function get_messages()
    {
        // $lang_file = __DIR__.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$this->lang.'.php';



        // wp_die($this->lang);
        $messages = get_option('wp_age_gate_validation_messages', array());
        $advanced = get_option('wp_age_gate_advanced', array());
        $post = $this->sanitize($_REQUEST);

        if (isset($post['lang']) && isset($messages['lang'][$post['lang']])) {
            if ($advanced['use_default_lang']) {
                foreach ($messages['lang'][$post['lang']] as $key => $value) {
                    if (empty($value)) {
                        $messages['lang'][$post['lang']][$key] = $messages[$key];
                    }
                }
            }
            $messages = $messages['lang'][$post['lang']];
            unset($messages['lang']);
        }

        if (isset($post['lang']) && file_exists(AGE_GATE_PATH . 'vendor'.DIRECTORY_SEPARATOR.'wixel'.DIRECTORY_SEPARATOR.'gump'. DIRECTORY_SEPARATOR .'lang' . DIRECTORY_SEPARATOR . $post['lang'] . '.php')) {
            $validation_methods_errors = include AGE_GATE_PATH . 'vendor'.DIRECTORY_SEPARATOR.'wixel'.DIRECTORY_SEPARATOR.'gump'. DIRECTORY_SEPARATOR .'lang' . DIRECTORY_SEPARATOR . $post['lang'] . '.php';
        } else {
            $validation_methods_errors = self::$validation_methods_errors;
        }


        $messages = array_merge($validation_methods_errors, $messages);
        return $messages;
    }
}
