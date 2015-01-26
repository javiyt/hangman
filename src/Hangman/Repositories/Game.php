<?php

namespace Hangman\Repositories;

use Hangman\Entities\Game as GameEntity;

interface Game
{
    public function get($game_id);

    public function save(GameEntity $game);

    public function getAll();
}
