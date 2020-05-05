<?php

namespace App\Message;

use App\Entity\Player;

final class Update
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

     private $playerId;

     public function __construct(Player $player)
     {
         $this->playerId = $player->getId();
     }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }
}
