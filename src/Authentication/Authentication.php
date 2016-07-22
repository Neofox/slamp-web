<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/07/2016
 * Time: 16:08
 */

namespace Slamp\Web\Authentication;


use Slamp\Web\Helper\User;
use Slamp\Web\Storage\UserStorage;
use Slamp\WebClient;

class Authentication
{
    /**
     * @var UserStorage
     */
    private $userStorage;

    /**
     * Authentication constructor.
     *
     * @param UserStorage $userStorage
     */
    public function __construct(UserStorage $userStorage)
    {
        $this->userStorage = $userStorage;
    }

    public function authenticateWithToken(string $token) {

        $userData = yield $this->userStorage->getByToken($token);

        if (!$userData) {
            $webclient = new WebClient($token);
            $slackUser = yield $webclient->users->getMeAsync();
            $user = new User($slackUser['id'], $slackUser['name'], $token);

            yield $this->userStorage->add($user);

            //throw new RuntimeException("user with valid token, but user record does not exist");
        }else{
            $user = new User($userData->slackId, $userData->name, $userData->token);
        }

        return $user;
    }
}