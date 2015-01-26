<?php

namespace Hangman\Services;

use Hangman\Entities\Word as WordEntity;
use Hangman\Entities\Game as GameEntity;
use Hangman\Repositories\MongoDB\Word as WordRepository;
use Hangman\Repositories\MongoDB\Game as GameRepository;

class NewGame implements ServiceInterface
{
    private $word_repository;
    private $game_repository;

    public function initialize(WordRepository $word_repository, GameRepository $game_repository)
    {
        $this->word_repository = $word_repository;
        $this->game_repository = $game_repository;
    }

    public function execute(array $get_parameters, array $post_parameters)
    {
        $word_entity = new WordEntity();
        $word_entity->setWord($get_parameters['word']);

        $game = new GameEntity();
        $game->setWord($word_entity);

        $this->game_repository->save($game);

        return $game;
    }
}
