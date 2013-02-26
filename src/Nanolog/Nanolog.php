<?php

/**
 * Nanolog - a simple and lightweight logging solution for PHP
 *
 * PHP version 5.3
 * 
 * Usage:
 * $log = \Nanolog\Nanolog::create(\Nanolog\Nanolog::DEBUG, '/tmp');
 * $log->warning('Disk space is at 90%')
 *
 * For more documentation read the README.md file, or the source :P
 *
 * @category Logging
 * @package  Nanolog
 * @author   Rogério Vicente <http://rogeriopvl.com>
 * @license  MIT https://github.com/rogeriopvl/php-nanolog/blob/master/LICENSE
 * @version  GIT: v0.0.4
 * @link     https://github.com/rogeriopvl/php-nanolog
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
     * Levels description
     * @var array
     */
    private static $levels = array(
        self::CRITICAL => 'CRITICAL',
        self::ERROR => 'ERROR',
        self::WARNING => 'WARNING',
        self::INFO => 'INFO',
        self::DEBUG => 'DEBUG'
    );

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
     * Date string format compatible with date()' php function
     * @var string
     */
    private $_dateFormat;

    /**
     * @var array
     */
    private static $_instances;

    /**
     * Constructor method
     *
     * @param string  $folder   the folder where logs are created
     * @param integer $level    the level to start writing to log, default is DEBUG
     *                          (all messages bellow this level will be ignored)
     * @param string  $name     (optional) the name of the log instance (if you
     *                          need multiple instances you have to set this param)
     * @param string  $fileName (optional) the name of the log file
     * @param string  $dateFormat (optional) the format of the date string
     *
     * @return void
     */
    private function __construct($folder, $level = self::DEBUG, $name = null, $fileName = null, $dateFormat = null)
    {
        if (!is_dir($folder) || !is_writable($folder)) {
            throw new \Exception('Folder does not exist, or is not writable');
        }

        self::$_instances = array();
        $this->_name = $name;
        $this->_level = (int)$level;
        $this->_filePath = $folder . DIRECTORY_SEPARATOR;
        $this->_dateFormat = $dateFormat === null ? 'Y-m-d H:i:s' : $dateFormat;

        $this->_filePath .= $fileName === null ? $this->_name.'_'.date('Y-m-d').'.log' : $fileName;
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
     * @param string $name the name of the log instance
     *
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
     *
     * @param string  $folder   the folder where logs are created
     * @param integer $level    the level to start writing to log, default is DEBUG
     *                          (all messages bellow this level will be ignored)
     * @param string  $name     (optional) the name of the log instance (if you
     *                          need multiple instances you have to set this param)
     * @param string  $fileName (optional) the name of the log file
     * @param string  $dateFormat (optional) the date format string
     *
     * @return \Nanolog\Nanolog
     */
    public static function create($folder, $level = self::DEBUG, $name = null, $fileName = null, $dateFormat = null)
    {
        if ($name !== null) {
            if (isset(self::$_instances[$name])) {
                return self::$_instances[$name];
            }
        } else {
            // sorry you can't have more than one unamed instance :P
            if (isset(self::$_instances[$name])) {
                return false;
            }
        }
        self::$_instances[$name] = new Nanolog($folder, $level, $name, $fileName, $dateFormat);

        return self::$_instances[$name];
    }

    /**
     * Writes a log message of a given level
     *
     * @param string  $message the log message
     * @param integer $level   the level of the message
     *
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
     * @param string $message the log message
     *
     * @return boolean the log message
     */
    public function critical($message)
    {
        return $this->log($message, self::CRITICAL);
    }

    /**
     * Writes an error level message into the log
     *
     * @param string $message the log message
     *
     * @return boolean
     */
    public function error($message)
    {
        return $this->log($message, self::ERROR);
    }

    /**
     * Writes a warning level message into the log
     *
     * @param string $message the log message
     *
     * @return boolean
     */
    public function warning($message)
    {
        return $this->log($message, self::WARNING);
    }

    /**
     * Writes an info level message into the log
     *
     * @param string $message the log message
     *
     * @return boolean
     */
    public function info($message)
    {
        return $this->log($message, self::INFO);
    }

    /**
     * Writes a debug level message into the log
     *
     * @param string $message the log message
     *
     * @return boolean
     */
    public function debug($message)
    {
        return $this->log($message, self::DEBUG);
    }

    /**
     * Sets the date format
     *
     * @param string $format the new date format (date() compatible)
     *
     * @return void
     */
    public function setDateFormat($format)
    {
        $this->_dateFormat = $format;
    }

    /**
     * Sets the level of the log instance
     *
     * @param integer $level the new level to set
     *
     * @return void
     */
    public function setLevel($level)
    {
        $this->_level = (int)$level;
    }

    /**
     * Get the name of the instance
     *
     * @return string the name of the instance
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Generates the line prefix with date and log level
     *
     * @param integer $level the log level
     *
     * @return string the line prefix with date and level
     */
    private function _generateLinePrefix($level)
    {
        return date($this->_dateFormat) . ' ' . self::$levels[$level]. ' ';
    }
}
