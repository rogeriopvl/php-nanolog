<?php

/**
 * Nanolog - a simple and lightweight logging solution for PHP
 * 
 * @author Rogério Vicente <http://rogeriopvl.com>
 * @version 0.1
 */

namespace Nanolog;

class Nanolog
{
    const CRITICAL = 0;
    const ERROR = 1;
    const WARNING = 2;
    const INFO = 3;
    const DEBUG = 4;

    private $_filePath;
    private $_handle;
    private $_name;
    private $_level;

    private static $_instances;

    /**
     * Constructor method
     *
     * @param int $level
     * @param String $folder
     * @param String $fileName;
     * @param String $name
     */
    private function __construct($level, $folder, $name = null, $fileName = null)
    {
        if (!is_dir($folder) || !is_writable($folder)) {
            throw new \Exception('Folder does not exist, or is not writable');
        }

        self::$_instances = array();
        $this->_name = $name;
        $this->_level = (int)$level;
        $this->_filePath = $folder . DIRECTORY_SEPARATOR;

        $this->_filePath .= $fileName === null ? date('Y-m-d').'.log' : $fileName;
        $this->_handle = fopen($this->_filePath, 'a');

        if (!$this->_handle) {
            throw new \Exception('Error opening log file with path: ' . $this->_filePath);
        }
    }

    /**
     * Destructor method
     */
    public function __destruct()
    {
        if ($this->_handle) {
            fclose($this->_handle);
        }
    }

    /**
     * Returns a log instance
     *
     * @param String $name, the name of the logger
     * @return mixed Nanolog instance or false if no instance matches the name
     */
    public static function getInstance($name = null)
    {
        if (isset(self::$_instances[$name])) {
            return self::$_instances[$name];
        } else {
            return false;
        }
    }

    /**
     * Creates a new Nanolog instance
     */
    public static function create($level, $folder, $name = null, $fileName = null)
    {
        if ($name !== null) {
            if (isset(self::$_instances[$name])) {
                return false; // should we return the instance instead?
            }
        } else {
            // sorry you can't have more than one unamed instance :P
            if (isset(self::$_instances[$name])) {
                return false;
            }
        }
        self::$_instances[$name] = new Nanolog($level, $folder, $name, $fileName);

        return self::$_instances[$name];
    }

    /**
     * Writes a log message of a given level
     *
     * @param String $message
     * @param int $level the level of the message
     * @return boolean true on success, false otherwise
     */
    public function log($message, $level)
    {
        // ignore levels bellow the set default
        if ((int)$level > $this->_level) {
            return true;
        }
        $linePrefix = $this->_generateLinePrefix((int)$level);
        $message = $linePrefix.$message."\n";
        if (fwrite($this->_handle, $message) === false) {
            return false;
        }
        return true;
    }

    /**
     * Writes a critical level message into the log
     *
     * @param String $message
     */
    public function critical($message)
    {
        $this->log($message, self::CRITICAL);
    }

    /**
     * Writes an error level message into the log
     *
     * @param String $message
     */
    public function error($message)
    {
        $this->log($message, self::ERROR);
    }

    /**
     * Writes a warning level message into the log
     *
     * @param String $message
     */
    public function warning($message)
    {
        $this->log($message, self::WARNING);
    }

    /**
     * Writes an info level message into the log
     *
     * @param String $message
     */
    public function info($message)
    {
        $this->log($message, self::INFO);
    }

    /**
     * Writes a debug level message into the log
     *
     * @param String $message
     */
    public function debug($message)
    {
        $this->log($message, self::DEBUG);
    }

    /**
     * Sets the level of the log instance
     *
     * @param int $level the new level to set
     */
    public function setLevel($level) {
        $this->_level = (int)$level;
    }

    /**
     * Get the name of the instance
     *
     * @return String the name of the instance
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Generates the line prefix with date and log level
     *
     * @param int $level
     */
    private function _generateLinePrefix($level)
    {
        $linePrefix = date('Y-m-d H:i:s');

        switch($level) {
            case self::CRITICAL:
                $linePrefix .= ' CRITICAL ';
                break;
            case self::ERROR:
                $linePrefix .= ' ERROR ';
                break;
            case self::WARNING:
                $linePrefix .= ' WARNING ';
                break;
            case self::INFO:
                $linePrefix .= ' INFO ';
                break;
            case self::DEBUG:
                $linePrefix .= ' DEBUG ';
                break;
            default:
                break;
        }
        return $linePrefix;
    }
}
