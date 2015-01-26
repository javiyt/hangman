<?php

namespace Hangman\Services;

use Hangman\Repositories\MongoDB\Word as WordRepository;
use Hangman\Repositories\MongoDB\Game as GameRepository;

interface ServiceInterface
{
    public function initialize(WordRepository $word_repository, GameRepository $game_repository);

    public function execute(array $get_parameters, array $post_parameters);
}
