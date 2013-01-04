# Nanolog

Small and simple logging solution for PHP >= 5.3

## Install

    composer install Nanolog\Nanolog

## Usage

### One log instance in DEBUG mode (default)

    $log = \Nanolog\Nanolog::create('/tmp');

    $log->critical('Cant connect to server');
    $log->error('Cant open file');
    $log->warning('Disk is 90% full');
    $log->info('User foobar logged in');
    $log->debug(print_r($obj, true));

    $log = null;
    $log = \Nanolog\Nanolog::getInstance();
    $log->info('We have our log instance back again');

### Multiple log instances by name

    $log1 = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG, 'log_one');
    $log2 = \Nanolog\Nanolog::create('/var/log', \Nanolog\Nanolog::DEBUG, 'log_two');

    $log1->info('Our log_one instance was created');
    $log2->info('Our log_two instance was created');

    $log1 = null; // oops

    $log1 = \Nanolog\Nanolog::getInstance('log_one');
    $log1->info('We have our log_one back!');

## Documentation

#### Public Attributes

* `const CRITITAL = 0`
* `const ERROR = 1`
* `const WARNING = 2`
* `const INFO = 3`
* `const DEBUG = 4`

#### Public static methods
* `static getInstance ($name=null)`
* `static create ($folder, $level=self::DEBUG, $name=null, $fileName=null)`

#### Public instance methods
* `critical ($message)`
* `error ($message)`
* `warning ($message)`
* `info ($message)`
* `debug ($message)`
* `setLevel ($level)`
* `getName ()`

## TODO

* Log line prefix (including date format) customization

## License

The MIT License

Copyright (c) 2013 Rogério Vicente

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
