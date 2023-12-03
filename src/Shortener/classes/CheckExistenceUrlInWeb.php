<?php
namespace App\Shortener\classes;
use App\Shortener\Interface\ICheckInWeb;
use http\Exception\InvalidArgumentException;

class CheckExistenceUrlInWeb implements ICheckInWeb
{

    public function exist(string $url): mixed
    {
        if (get_headers($url)===false){
            throw new InvalidArgumentException("Server does not exist. Make sure you need short URL for this" . PHP_EOL);
        }else{
            $header = get_headers($url);
        }

       return $header[0];
    }
}