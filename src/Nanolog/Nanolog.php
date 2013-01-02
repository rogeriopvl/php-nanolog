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

    const OPEN = 1;
    const FAILED = 2;
    const CLOSED = 3;

    private $_status;
    private $_filePath;
    private $_handle;
    private $_name;

    private static $_instances;

    /**
     * Constructor method
     *
     * @param int $level
     * @param String $folder
     * @param String $fileName;
     * @param String $name
     */
    public function __construct($level, $folder, $name = null, $fileName = null)
    {
        if (!is_dir($folder) || !is_writable($folder)) {
            throw new \Exception('Folder does not exist, or is not writable');
        }

        self::$_instances = array();
        $this->_status = self::CLOSED;
        $this->_name = $name;
        $this->_filePath = $folder . DIRECTORY_SEPARATOR;

        $this->_filePath .= $fileName === null ? date('Y-m-d').'.log' : $fileName;

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
}
