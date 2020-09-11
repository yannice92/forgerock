<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Traits;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

/**
 * The PageableTrait is used to get page limit from query url
 */
trait LoggingTrait
{

    public function logging(string $title, string $filename, $message, string $logginType = 'INFO')
    {
        if (env("APP_ENV") != "prod") {
            $logger = new Logger($title);
            $handler = new RotatingFileHandler(storage_path('logs/' . str_replace(' ', '-', strtolower($filename)) . '.log'),
                0, Logger::INFO);
            $handler->setFilenameFormat('{filename}-{date}', 'Y-m-d');
            $logger->pushHandler($handler);

            if ($logginType === "INFO") {
                $logger->info($message);
            } else if ($logginType === "ERROR") {
                $logger->error($message);
            } else {
                $logger->notice($message);
            }
        }
    }
}
