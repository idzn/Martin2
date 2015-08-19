<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Validator;

use application\custom\Register;
use Martin\traits\CommonTrait;

class Validator
{
    use CommonTrait;


    const RULE_REQUIRED     = 'required';
    const RULE_EMAIL        = 'email';
    const RULE_MAX_LENGTH   = 'max_length';
    const RULE_MIN_LENGTH   = 'min_length';
    const RULE_EQUAL        = 'equal';

    private $messages = [
        'required' => '{{name}} is required',
        'email' => '{{name}} is not valid email',
        'max_length' => 'Max length for "{{name}}" must be {{value}} symbols',
        'min_length' => 'Min length for "{{name}}" must be {{value}} symbols',
        'equal' => '{{name}} is not equal {{var}}'
    ];

    private $data;
    private $vars;
    private $errors;
    private $currentVar;

    public function __constructor()
    {
        if (!isset($_SESSION)) session_start();

    }

    public function __destructor()
    {
        unset($_SESSION['validation']['errors']);
    }

    public function validate($dataAssoc)
    {
        $this->data = $dataAssoc;
        $this->currentVar = null;
        $this->vars = [];
        $this->errors = [];
        return $this;
    }

    public function addFor($name, $label)
    {
        $this->vars[$name]['label'] = $label;
        $this->currentVar = $name;
        return $this;
    }

    public function ruleRequired($errorMessage = null)
    {
        $this->vars[$this->currentVar]['rules'][self::RULE_REQUIRED] = [
            'params' => [],
            'errorMessage' => $errorMessage,
        ];
        return $this;
    }

    public function ruleEmail($errorMessage = null)
    {
        $this->vars[$this->currentVar]['rules'][self::RULE_EMAIL] = [
            'params' => [],
            'errorMessage' => $errorMessage,
        ];
        return $this;
    }

    public function ruleMaxLength($length, $errorMessage = null)
    {
        $this->vars[$this->currentVar]['rules'][self::RULE_MAX_LENGTH] = [
            'params' => [$length],
            'errorMessage' => $errorMessage,
        ];
        return $this;
    }

    public function ruleMinLength($length, $errorMessage = null)
    {
        $this->vars[$this->currentVar]['rules'][self::RULE_MIN_LENGTH] = [
            'params' => [$length],
            'errorMessage' => $errorMessage,
        ];
        return $this;
    }

    public function ruleEqual($varName, $errorMessage = null)
    {
        $this->vars[$this->currentVar]['rules'][self::RULE_EQUAL] = [
            'params' => [$varName],
            'errorMessage' => $errorMessage,
        ];
        return $this;
    }

    public function isValidEmail($var)
    {
        return (!filter_var($var, FILTER_VALIDATE_EMAIL)) ? false : true;
    }

    private function registerError($rule, $name, $errorMessage)
    {
        if ($errorMessage === null) {
            $message = $this->messages[$rule];
            $message = str_replace('{{name}}', $this->vars[$name]['label'], $message);
            if (isset($this->vars[$name]['rules'][$rule]['params'][0])) {
                $message = str_replace('{{value}}', $this->vars[$name]['rules'][$rule]['params'][0], $message);
                $message = str_replace('{{var}}', $this->vars[$name]['rules'][$rule]['params'][0], $message);
            }
        } else {
            $message = $errorMessage;
        }
        $this->errors[] = ['var' => $name, 'message' => $message];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function run()
    {
        foreach ($this->vars as $name => $value) {
            foreach ($value['rules'] as $rule => $ruleArray) {
                switch ($rule) {
                    case self::RULE_REQUIRED:
                        if (!isset($this->data[$name]) || $this->data[$name] == '') {
                            $this->registerError($rule, $name, $ruleArray['errorMessage']);
                        }
                        break;
                    case self::RULE_EMAIL:
                        if (!$this->isValidEmail($this->data[$name])) {
                            $this->registerError($rule, $name, $ruleArray['errorMessage']);
                        }
                        break;
                    case self::RULE_MAX_LENGTH:
                        if (mb_strlen($this->data[$name]) > $ruleArray['params'][0]) {
                            $this->registerError($rule, $name, $ruleArray['errorMessage']);
                        }
                        break;
                    case self::RULE_MIN_LENGTH:
                        if (mb_strlen($this->data[$name]) < $ruleArray['params'][0]) {
                            $this->registerError($rule, $name, $ruleArray['errorMessage']);
                        }
                        break;
                    case self::RULE_EQUAL:
                        if ($this->data[$name] != $this->data[$ruleArray['params'][0]]) {
                            $this->registerError($rule, $name, $ruleArray['errorMessage']);
                        }
                        break;
                }
            }
        }
        $this->saveErrors();
        return (count($this->errors)) ? false : true;
    }

    public function saveErrors()
    {
        unset($_SESSION['validation']['errors']);
        foreach ($this->errors as $error) {
            $_SESSION['validation']['errors'][$error['var']][] = $error['message'] ;
        }
    }

    public function showErrorsHere()
    {
        if (!isset($_SESSION['validation']['errors'])) return;
        foreach ($_SESSION['validation']['errors'] as $var) {
            foreach ($var as $message) {
                echo '<div class="alert alert-danger alert-dismissable">';
                echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                echo $message;
                echo '</div>';
            }

        }
    }


}