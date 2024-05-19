<?php

namespace App\Services\OutlineVPN;

use Exception;
use Illuminate\Http\Response;
use stdClass;

class ApiResponse
{
    public bool $isServerFailure;

    public bool $isValidationError;

    public function __construct(
        /**
         * This field indicates the result of the request whether it was successful or not.
         */
        public bool $succeed,

        /**
         * This field contains the server response status code.
         */
        public int $statusCode,

        /**
         * This field may contain the API response result.
         */
        public mixed $result = null,

        /**
         * This field may set to an error message in case of failure.
         */
        public ?string $message = null,

        /**
         * This field may set to errors in case of failure.
         */
        public array|stdClass|null $errors = null
    ) {
        $this->isServerFailure = $this->statusCode >= Response::HTTP_INTERNAL_SERVER_ERROR;
        $this->isValidationError = $this->statusCode == Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    public static function succeed(int $statusCode, mixed $result = null, ?string $message = null): static
    {
        return new static(
            succeed: true,
            statusCode: $statusCode,
            result: $result,
            message: $message
        );
    }

    public static function error(int $statusCode, ?string $message = null, array|stdClass|null $errors = null): static
    {
        return new static(
            succeed: false,
            statusCode: $statusCode,
            message: $message,
            errors: $errors
        );
    }

    public static function serverFailure(int $statusCode, ?string $message = null, array|stdClass|null $errors = null): static
    {
        return new static(
            succeed: false,
            statusCode: $statusCode,
            message: $message,
            errors: $errors
        );
    }

    public static function notFound(?string $message = ''): void
    {
        abort(Response::HTTP_NOT_FOUND, $message);
    }

    public static function unauthenticated(?string $message = ''): void
    {
        abort(Response::HTTP_UNAUTHORIZED, $message);
    }

    public static function unauthorized(?string $message = ''): void
    {
        abort(Response::HTTP_FORBIDDEN, $message);
    }

    public function throw(): void
    {
        throw new Exception($this->message);
    }

    public function throwWithMessage(string $message): void
    {
        throw new Exception(implode(PHP_EOL, [$message, $this->message]));
    }
}
