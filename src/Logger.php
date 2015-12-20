<?php

namespace Silalahi\Slim;


/**
 * Class Logger
 *
 * @package Silalahi\Slim
 */
class Logger {

    const EMERGENCY = 1;
    const ALERT     = 2;
    const CRITICAL  = 3;
    const ERROR     = 4;
    const WARN      = 5;
    const NOTICE    = 6;
    const INFO      = 7;
    const DEBUG     = 8;

    const VERSION   = "0.1.0";


    /**
     * @var resource
     */
    protected $resource;
    /**
     * @var array
     */
    protected $settings;


    /**
     * Logger constructor.
     *
     * Preparing Logger. Available settings are:
     *
     * path:
     * (string) The relative or absolute filesystem path to a writable directory.
     *
     * name_format:
     * (string) The log file name format; parsed with `date()`.
     *
     * extension:
     * (string) The file extention to append to the filename`.
     *
     * message_format:
     * (string) The log message format; available tokens are...
     *     %label%      Replaced with the log message level (e.g. FATAL, ERROR, WARN).
     *     %date%       Replaced with a ISO8601 date string for current timezone.
     *     %message%    Replaced with the log message, coerced to a string.
     *
     * @param array $settings Settings
     *
     */
    public function __construct($settings = array())
    {
        // Merge settings
        $this->settings =  array_merge(array(
            'path' => './logs',
            'name_format' => 'Y-m-d',
            'extension' => 'log',
            'message_format' => '[%label%] %date%%message%'
        ), $settings);

        // Remove trailing slash from log path
        $this->settings['path'] = rtrim($this->settings['path'], DIRECTORY_SEPARATOR);
    }


    /**
     * Logger Middleware for Slim framework
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        // Begin of time
        $start = time();
        // URL accessed
        $path = $request->getPath();

        // Call next middleware
        $response = $next($request, $response);

        // End of time
        $end = time();
        // Latency
        $latency = $end - $start;
        // Client IP address
        $clientIP = "0.0.0.0";
        // Method access
        $method = $request->getMethod();

        $this->write(sprintf("|%d|%13v|%s|%s %s", $response->getStatus(), $latency, $clientIP, $method, $path), self::INFO);

        return $response;
    }

    /**
     * Write to log
     *
     * @param mixed $object Object
     * @param int   $level  Level
     *
     * @return void
     */
    public function write($object, $level)
    {
        // Determine label
        $label = "DEBUG";
        switch($level) {
            case self::FATAL:
                $label = 'FATAL';
                break;
            case self::ERROR:
                $label = 'ERROR';
                break;
            case self::WARN:
                $label = 'WARN';
                break;
            case self::INFO:
                $label = 'INFO';
                break;
        }

        // Get formatted log message
        $message = str_replace(
            array("%label%", "%date%", "%message%"),
            array($label, date("c"), (string)$object),
            $this->settings['message_format']
        );

        if ( ! $this->resource) {
            $filename = date($this->settings['name_format']);
            if (! empty($this->settings['extension'])) {
                $filename .= '.' . $this->settings['extension'];
            }
            $this->resource = fopen($this->settings['path'] . DIRECTORY_SEPARATOR . $filename, 'a');
        }

        // Output to resource
        fwrite($this->resource, $message . PHP_EOL);
    }

}
