<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/07/2016
 * Time: 14:20
 */

namespace Slamp\Web\Helper;


use Amp\Struct;

class User
{
    use Struct;

    public $id;
    public $name;
    public $token;

    public function __construct(string $id, string $name, string $token)
    {
        $this->name = $name;
        $this->token = $token;
        $this->id = $id;
    }
}