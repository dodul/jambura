<?php
class jflash {
    public function __construct() {
        // empty
    }

    public function success($message) {
        try {
            $_SESSION["flashMsg"]  = $message;
            $_SESSION["flashType"] = 'success';
        } catch (Exception $e) {
            // do nothing. Log locally in future
        }
    }

    public function error($message) {
        try {
            $_SESSION["flashMsg"]  = $message;
            $_SESSION["flashType"] = 'danger';
        } catch (Exception $e) {
            // do nothing. Log locally in future
        }
    }

    public function warning($message) {
        try {
            $_SESSION["flashMsg"]  = $message;
            $_SESSION["flashType"] = 'warning';
        } catch (Exception $e) {
            // do nothing. Log locally in future
        }
    }

    public function info($message) {
        try {
            $_SESSION["flashMsg"]  = $message;
            $_SESSION["flashType"] = 'info';
        } catch (Exception $e) {
            // do nothing. Log locally in future
        }
    }

    public function getType() {
        if (isset($_SESSION["flashType"])) {
            return $_SESSION["flashType"];
        }
        return false;
    }

    public function getMsg() {
        if (isset($_SESSION["flashMsg"])) {
            return $_SESSION["flashMsg"];
        }
        return false;
    }

    public function clear() {
        unset($_SESSION["flashMsg"]);
        unset($_SESSION["flashType"]);
    }
}
