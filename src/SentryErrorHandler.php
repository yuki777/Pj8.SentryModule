<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Sunday\Extension\Error\ErrorInterface;
use BEAR\Sunday\Extension\Router\RouterMatch as Request;
use Exception;
use Ray\Di\Di\Named;

use function Sentry\captureException;

class SentryErrorHandler implements ErrorInterface
{
    private ErrorInterface $originalError;

    /**
     * @Named("original=original")
     */
    public function __construct(ErrorInterface $original)
    {
        $this->originalError = $original;
    }

    /**
     * @return ErrorInterface
     */
    public function handle(Exception $e, Request $request) // phpcs:disable SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
    {
        captureException($e);

        return $this->originalError->handle($e, $request);
    }

    public function transfer(): void
    {
        $this->originalError->transfer();
    }
}
