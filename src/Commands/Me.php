<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 22/07/2016
 * Time: 10:08
 */

namespace Slamp\Web\Commands;


use Slamp\SlackObject\User;
use Slamp\Web\Command;
use Slamp\Web\Helper\{Data, Request};

class Me extends Command
{

    public function execute(Request $request, User $user)
    {
        
        return new Data([
            "id" => $user->getId(),
            "name" => $user->getName(),
            "firstName" => $user->getFirstName(),
             "lastName" => $user->getLastName(),
        ]);
    }
}