<?php

namespace Core\Logging;

class Logger
{
    private string $logFile;
    private int $logLevel;

    /**
     * Log levels.
     * @notice The number ordering is reversed in comparison to the rfc5424 standard.
     *
     * @var array|int[]
     */
    public array $levels = [
        'debug' => 0,
        'info' => 1,
        'notice' => 2,
        'warning' => 3,
        'error' => 4,
        'critical' => 5,
        'alert' => 6,
        'emergency' => 7,
    ];

    /**
     * Logger constructor.
     *
     * @param int|string $logLevel
     * @param string $logFile
     */
    public function __construct(mixed $logLevel, string $logFile)
    {
        if (!file_exists($logFile)) {
            touch($logFile);
        }
        if (is_string($logLevel)) {
            $logLevel = $this->levels[$logLevel];
        }
        $this->logFile = $logFile;
        $this->logLevel = $logLevel;
    }

    /**
     * Log a debug message to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an info message to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a notice to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an error message to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a critical message to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an alert message to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an emergency message to the logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Write a log message to the log file.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    private function writeLog(string $level, string $message, array $context): void
    {
        if ($this->levels[$level] >= $this->logLevel) {
            $logMessage = $this->formatMessage($message, $level, $context);
            file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        }
    }

    /**
     * Format a log message.
     *
     * @param string $message
     * @param string $level
     * @param array $context
     * @return string
     */
    private function formatMessage(string $message, string $level, array $context): string
    {
        $logMessage = date('Y-m-d H:i:s') . ' '. strtoupper($level) . ': '. $message . PHP_EOL;
        if (!empty($context)) {
            $logMessage .= 'Context: ' . json_encode($context) . PHP_EOL;
        }
        return $logMessage;
    }
}