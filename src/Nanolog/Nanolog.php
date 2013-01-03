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

    public function log($message, $level)
    {
        $linePrefix = date('Y-m-d H:i:s').' ';
        $message = $linePrefix.$message."\n";
        // we cant throw here an exception if its not able to write
        // because its not practical to use try-catch everytime we log
        fwrite($this->_handle, $message);
    }

    public function critical($message)
    {
        $this->log('CRITICAL '.$message, self::CRITICAL);
    }

    public function error($message)
    {
        $this->log('ERROR '.$message, self::ERROR);
    }

    public function warning($message)
    {
        $this->log('WARNING '.$message, self::WARNING);
    }

    public function info($message)
    {
        $this->log('INFO '.$message, self::INFO);
    }

    public function debug($message)
    {
        $this->log('DEBUG '.$message, self::DEBUG);
    }

    /**
     * Get the name of the instance
     *
     * @return String the name of the instance
     */
    public function getName() {
        return $this->_name;
    }
}
