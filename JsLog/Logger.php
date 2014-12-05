<?php
namespace JsLog;

class Logger {

    const LOGGER_URL = 'http://jslog.me/log';

    public $options;
    public $systemInfoSent = false;

    public function __construct($options) {
        $this->options = new Options($options);
        $this->systemInfoSent = false;
        if (!extension_loaded('curl')) {
            error_log('JsLog: ERROR - curl extension is not installed');
            $this->options->enabled = false;
        }
        $this->init();
    }

    public function __destruct() {
        if ($this->options->logUncaughtExceptions) {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function init(){
        if (!$this->options->enabled)  return;
        if ($this->options->hookConsole) {
            $this->hookConsole();
        }
        if ($this->options->logUncaughtExceptions) {
            $this->logUncaughtExceptions();
        }
        if ($this->options->trackLaunches) {
            $this->trackLaunches();
        }
    }

    public function hookConsole() {

    }

    public function logUncaughtExceptions() {
        set_error_handler(array($this, 'errorHandler'));
        set_exception_handler(array($this, 'exceptionHandler'));
    }

    public function errorHandler($errno , $errstr, $errfile, $errline, $errcontext) {
        $errorObject = array(
            'code' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'context' => $errcontext);
        $this->sendToServer($this->options->key, 'uncaughtException', $errorObject);
    }

    public function exceptionHandler(\Exception $e){
        $this->sendToServer($this->options->key, 'exception', $e);
    }

    private function getEpochTime(){
        return ceil(microtime(true)*1000);
    }

    public function sendToServer($key, $eventType, $data) {
        if (!$this->options->enabled)  return;
        $rawData = array(
            'key' => $key,
            'sessionId' => $this->options->sessionId,
            'hostId' => isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'',
            'time' => $this->getEpochTime(),
            'type' => $eventType,
            'data' => $data
        );
        $content = json_encode($rawData);
        $curl = curl_init(Logger::LOGGER_URL);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        curl_exec($curl);
//        $json_response = curl_exec($curl);
//        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
    }

    private function initialSystemInfo() {
        if (!$this->systemInfoSent) {
            $this->systemInfo($this->options->collectSystemInfo);
        }
    }

    private function systemInfo($collectSystemInfo) {
        $systemInfoData = array(
            'version' => $this->options->version,
            'userAgent' => '',
            'platform' => '',
            'ip' => isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:''
        );
        if ($collectSystemInfo) {
            $systemInfoData['userAgent'] = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        }
        $this->systemInfoSent = true;
        $this->sendToServer($this->options->key, 'systemInfo', $systemInfoData);
    }

    public function log($message) {
        try {
            if (!$this->options->enabled)  return;
            $this->initialSystemInfo();
            $this->sendToServer($this->options->key, 'log', $message);
        } catch(\Exception $e) {
        }
    }

    public function info($message) {
        try {
            if (!$this->options->enabled)  return;
            $this->initialSystemInfo();
            $this->sendToServer($this->options->key, 'info', $message);
        } catch(\Exception $e) {
        }
    }

    public function warn($message) {
        try {
            if (!$this->options->enabled)  return;
            $this->initialSystemInfo();
            $this->sendToServer($this->options->key, 'warn', $message);
        } catch(\Exception $e) {
        }
    }

    public function error($message) {
        try {
            if (!$this->options->enabled)  return;
            $this->initialSystemInfo();
            $this->sendToServer($this->options->key, 'error', $message);
        } catch(\Exception $e) {
        }
    }

    public function exception($message) {
        try {
            if (!$this->options->enabled)  return;
            $this->initialSystemInfo();
            $this->sendToServer($this->options->key, 'exception', $message);
        } catch(\Exception $e) {
        }
    }

    private function trackLaunches() {
        $this->systemInfo($this->options->collectSystemInfo);
    }

}

