<?php

namespace App\Services\OutlineVPN;

class ApiAccessKey
{
    public int $id;

    public string $name;

    public string $password;

    public int $port;

    public string $method;

    public string $accessUrl;

    public ?int $dataLimitInBytes;

    public static function fromObject(?object $input): static
    {
        $name = str($input->name)->length() > 0 ? $input->name : "Key #$input->id";

        $key = new static;
        $key->id = intval($input->id);
        $key->name = $name;
        $key->password = $input->password;
        $key->port = $input->port;
        $key->method = $input->method;
        $key->accessUrl = $input->accessUrl;

        if (isset($input->dataLimit)) {
            $key->dataLimitInBytes = $input->dataLimit->bytes;
        } else {
            $key->dataLimitInBytes = null;
        }

        return $key;
    }
}
