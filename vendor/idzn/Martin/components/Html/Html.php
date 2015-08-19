<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Html;

use application\custom\App;
use application\custom\Register;

class Html
{


    public $validationErrors = [];

    public function __construct()
    {
        if (!isset($_SESSION)) session_start();
    }

    public function foreachTable($data, $headers = [], $htmlOptions = [], callable $callback = null)
    {
        $output = '';
        $totalHtmlOptions = [
            'table' => '',
            'thead' => '',
            'tr' => '',
            'th' => '',
            'td' => '',
            'tbody' => '',
        ];

        $dataCount = count($data);
        if ($dataCount) {

            if (count($htmlOptions)) {
                foreach ($htmlOptions as $tag => $tagOptions) {
                    foreach ($tagOptions as $option => $value) {
                        $totalHtmlOptions[$tag] .= ' ' . $option . '="' . $htmlOptions[$tag][$option] . '" ';
                    }
                }
            }

            $headerHtml = '';
            if (count($headers)) {
                $headerHtml .= '<thead><tr>';
                foreach ($headers as $th) {
                    $headerHtml .= '<th>' . $th . '</th>';
                }
                $headerHtml .= '</tr></thead>';
            }

            if (count($data)) {
                $bodyHtml = '<tbody>';
                foreach ($data as $row) {
                    $rowHtml = '<tr>';
                    foreach ($row as $key => $col) {
                        if ($callback !== null){
                            $col = $callback($row, $key, $col);
                        }
                        $rowHtml .= '<td' . $totalHtmlOptions['td'] . '>' . $col . '</td>';
                    }
                    $rowHtml .= '</tr>';
                    $bodyHtml .= $rowHtml;
                }
                $bodyHtml .= '</tbody>';
            }

            $output = '<table' . $totalHtmlOptions['table'] . '>' . $headerHtml . $bodyHtml . '</table>';
        }

        return $output;
    }

    private function input($name, $params = [], $type = 'text')
    {
        $idStr = ' id="' . $name . '"';
        $nameStr = ' name="' . $name . '"';
        $paramsStr = 'type="' . $type . '"' . $idStr . $nameStr;

        $beforeStr = (isset($params['before'])) ? $params['before'] : '';
        $afterStr = (isset($params['after'])) ? $params['after'] : '';
        $labelStr = (isset($params['label'])) ? '<label for="' . $name . '">' . $params['label'] . '</label>' : '';

        $formGroupClass = '';
        $errorMessagesStr = '';
        if (array_key_exists($name, $this->validationErrors)) {
            $formGroupClass = ' has-error';

            $errorMessagesStr .= '<ul class="text-danger">';
            foreach ($this->validationErrors as $key => $errors) {
                foreach ($errors as $error) {
                    if ($key == $name) $errorMessagesStr .= "<li>$error</li>";
                }
            }
            $errorMessagesStr .= '</ul>';
        }

        foreach ($params as $key => $param) {
            if (in_array($key, ['before', 'after', 'id', 'name', 'label'])) continue;
            $paramsStr .= " $key=\"$param\"";
        }

        return "$beforeStr" .
        "<div class=\"form-group$formGroupClass $name\">" .
        "$labelStr<input $paramsStr>$errorMessagesStr</div>$afterStr";
    }

    public function inputText($name, $params = [])
    {
        return $this->input($name, $params, 'text');
    }

    public function inputEmail($name, $params = [])
    {
        return $this->input($name, $params, 'email');
    }

    public function inputPassword($name, $params = [])
    {
        return $this->input($name, $params, 'password');
    }

    public function inputHidden($name, $params = [])
    {
        return $this->input($name, $params, 'hidden');
    }

    public function inputSubmit($name, $params = [])
    {
        return $this->input($name, $params, 'submit');
    }

    public function beginForm($formName, $htmlParams = [])
    {
        if (isset($_SESSION['validation']['errors'])) {
            $this->validationErrors = $_SESSION['validation']['errors'];
        } else {
            $this->validationErrors = [];
        }

        $htmlParamsString = "name=\"$formName\"";
        foreach ($htmlParams as $key => $param) {
            if (in_array($key, ['name'])) continue;
            $htmlParamsString .= " $key=\"$param\"";
        }

        if (!isset($htmlParams['method'])) {
            $htmlParamsString .= "method=\"post\"";
        }

        echo "<form $htmlParamsString>";
        App::secure()->csrfProtectHere();
    }

    public function endForm()
    {
        echo '</form>';
        unset($_SESSION['validation']['errors']);
    }

}