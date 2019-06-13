<?php
/**
 * 2019-06-13.
 */

declare(strict_types=1);

namespace App\Support;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler;

class LoggerErrorHandler extends ErrorHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoggerErrorHandler constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param LoggerInterface          $logger
     */
    public function __construct(ResponseFactoryInterface $responseFactory, LoggerInterface $logger)
    {
        parent::__construct($responseFactory);
        $this->logger = $logger;
    }

    protected function logError(string $error): void
    {
        $this->logger->error($error);
    }
}
