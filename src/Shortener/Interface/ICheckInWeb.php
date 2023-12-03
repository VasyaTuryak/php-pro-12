<?php

namespace App\Shortener\Interface;

interface ICheckInWeb
{
public function exist(string $url): mixed;
}