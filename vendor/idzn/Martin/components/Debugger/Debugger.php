<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Debugger;

class Debugger
{


    public $config;
    public $appStartTime;
    public $appEndTime;
    public $memoryPeakUsage;
    public $responseCode;
    public $inspectArray;

    public function __construct($config)
    {
        $this->config = $config;
        require CONFIG_PATH . DIRECTORY_SEPARATOR . 'environment.php';
    }

    public function start()
    {
        if (!$this->config['enabled']) return;
        $this->appStartTime = microtime(true);
    }
    public function stop()
    {
        if (!$this->config['enabled']) return;
        $this->appEndTime = $_SERVER['REQUEST_TIME_FLOAT'];
        $this->memoryPeakUsage = memory_get_peak_usage();
        $this->responseCode = http_response_code();
        return $this;
    }
    public function renderInfoPanel()
    {
        if (!$this->config['enabled']) return;
        if (!$this->config['visibled']) return;
        require __DIR__ . '/info-panel.tpl.php';
    }

    public function inspect($data, $name = null)
    {
        if (!$this->config['enabled']) return;
        $name = ($name === null) ? count($this->inspectArray) : $name;
        ob_start();
        echo print_r($data);
        $this->inspectArray[$name] = ob_get_clean();
    }
}