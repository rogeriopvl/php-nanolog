<?php

/**
 * Nanolog - a simple and lightweight logging solution for PHP
 * 
 * Usage:
 * $log = \Nanolog\Nanolog::create(\Nanolog\Nanolog::DEBUG, '/tmp');
 * $log->warning('Disk space is at 90%')
 *
 * For more documentation read the README.md file, or the source :P
 *
 * @author Rogério Vicente <http://rogeriopvl.com>
 */

namespace Nanolog;

class Nanolog
{
    // log levels
    const CRITICAL = 0;
    const ERROR = 1;
    const WARNING = 2;
    const INFO = 3;
    const DEBUG = 4;

    /**
     * The path to the log file
     * @var string
     */
    private $_filePath;

    /**
     * The log file handler
     * @var resource
     */
    private $_handle;

    /**
     * The name of the log instance
     * @var string
     */
    private $_name;

    /**
     * @var integer
     */
    private $_level;

    /**
     * @var array
     */
    private static $_instances;

    /**
     * Constructor method
     *
     * @param integer $level
     * @param string $folder
     * @param string $fileName;
     * @param string $name
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
     * @param string $name, the name of the logger
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
     * @param string $message
     * @param integer $level the level of the message
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
     * @param string $message
     */
    public function critical($message)
    {
        $this->log($message, self::CRITICAL);
    }

    /**
     * Writes an error level message into the log
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->log($message, self::ERROR);
    }

    /**
     * Writes a warning level message into the log
     *
     * @param string $message
     */
    public function warning($message)
    {
        $this->log($message, self::WARNING);
    }

    /**
     * Writes an info level message into the log
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->log($message, self::INFO);
    }

    /**
     * Writes a debug level message into the log
     *
     * @param string $message
     */
    public function debug($message)
    {
        $this->log($message, self::DEBUG);
    }

    /**
     * Sets the level of the log instance
     *
     * @param integer $level the new level to set
     */
    public function setLevel($level) {
        $this->_level = (int)$level;
    }

    /**
     * Get the name of the instance
     *
     * @return string the name of the instance
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Generates the line prefix with date and log level
     *
     * @param integer $level
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
