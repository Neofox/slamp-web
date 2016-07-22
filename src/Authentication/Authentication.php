<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/07/2016
 * Time: 16:08
 */

namespace Slamp\Web\Authentication;


use Slamp\Web\Storage\UserStorage;
use Slamp\WebClient;

class Authentication
{
    /**
     * @var UserStorage
     */
    protected $userStorage;
    /**
     * @var WebClient
     */
    protected $webClient;

    /**
     * Authentication constructor.
     *
     * @param UserStorage $userStorage
     * @param WebClient   $webClient
     */
    public function __construct(UserStorage $userStorage, WebClient $webClient)
    {
        $this->userStorage = $userStorage;
        $this->webClient = $webClient;
    }

    public function authenticateWithToken(string $token)
    {
        $this->webClient->setToken($token);

        //TODO: use Redis for  fetching the user
        $user = yield $this->webClient->users->getMeAsync();

        //throw new RuntimeException("user with valid token, but user record does not exist");

        return $user;
    }
}