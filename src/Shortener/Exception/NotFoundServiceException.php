<?php
namespace App\Shortener\Interface;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundServiceException extends \Exception implements NotFoundExceptionInterface
{
}
