<?php
namespace JsLog;

class Options {
    public $enabled;
    public $logUncaughtExceptions;
    public $hookConsole;
    public $trackHost;
    public $collectSystemInfo;
    public $trackLaunches;
    public $key;
    public $version;
    public $sessionId;

    public function __construct($options){
        $this->sessionId = uniqid();
        $this->fillDefault($options, 'enabled', true);
        $this->fillDefault($options, 'logUncaughtExceptions', true);
        $this->fillDefault($options, 'hookConsole', true);
        $this->fillDefault($options, 'collectSystemInfo', true);
        $this->fillDefault($options, 'trackLaunches', true);
        $this->fillDefault($options, 'key', '');
        $this->fillDefault($options, 'version', '');
        $this->fillDefault($options, 'sessionId', uniqid());
    }

    private function fillDefault($options, $fieldName, $defaultValue) {
        if (isset($options[$fieldName])) {
            $this->{$fieldName} = $options[$fieldName];
        } else {
            $this->{$fieldName} = $defaultValue;
        }
    }
}