<?php
namespace App\Shortener\classes;
use App\Shortener\Interface\IUrlEncoder;

class EncodeUrl implements IUrlEncoder
{

    /**
     * @param string $prefix
     * @param int $max_length
     */
    public function __construct(protected string $prefix, protected int $max_length)
    {

    }

    public function encode(string $url): string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid Long URL' . PHP_EOL);
        }
        $n = $this->max_length - strlen($this->prefix);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $random_string .= $characters[$index];
        }
        return 'https://'.$this->prefix.'/'.$random_string;
    }

}