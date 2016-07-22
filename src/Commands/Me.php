<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 22/07/2016
 * Time: 10:08
 */

namespace Slamp\Web\Commands;


use Slamp\Web\Command;
use Slamp\Web\Helper\Data;
use Slamp\Web\Helper\Request;
use Slamp\Web\Helper\User;
use Slamp\WebClient;

class Me extends Command
{

    public function execute(Request $request, User $user)
    {
        $webClient = new WebClient($user->token);
        /** @var \Slamp\SlackObject\User $me */
        $me = yield $webClient->users->getMeAsync();

        return new Data([
            "id" => $user->id,
            "name" => $user->name,
            "token" => $user->token,
            "slackUser" => [
                "firstName" => $me->getFirstName(),
                "lastName" => $me->getLastName(),
            ]
        ]);
    }
}