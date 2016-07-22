<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/07/2016
 * Time: 14:28
 */

namespace Slamp\Web;


use Slamp\SlackObject\User;
use Slamp\Web\Helper\{Request};
use Slamp\WebClient;

abstract class Command
{
    /**
     * @var WebClient
     */
    protected $webClient;

    public function __construct(WebClient $webClient)
    {
        $this->webClient = $webClient;
    }

    public function getName(): string
    {
        $base = self::class . "s\\";
        $sub = str_replace($base, "", get_class($this));

        return strtolower(str_replace("\\", "/", $sub));
    }

    public abstract function execute(Request $request, User $user);
}