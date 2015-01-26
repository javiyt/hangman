<?php

namespace Hangman\Services;

use Hangman\Repositories\MongoDB\Word as WordRepository;
use Hangman\Repositories\MongoDB\Game as GameRepository;

class GuessLetter implements ServiceInterface
{
    const FAILED_GAME = 'fail';
    const PLAYING_GAME = 'busy';
    const FINISHED_GAME = 'success';

    private $word_repository;
    private $game_repository;

    public function initialize(WordRepository $word_repository, GameRepository $game_repository)
    {
        $this->word_repository = $word_repository;
        $this->game_repository = $game_repository;
    }

    public function execute(array $get_parameters, array $post_parameters)
    {
        $game = $this->game_repository->get($get_parameters['game_id']);

        if ($this->isValidLetter($post_parameters['char']) && $this->isValidGame($game)) {
            $game = $this->guessLetter($post_parameters['char'], $game);
        }

        return $game;
    }

    private function isValidLetter($letter)
    {
        return preg_match('/\b[a-z]\b/', $letter);
    }

    private function isValidGame($game)
    {
        return (
            0 < $game->getTriesLeft()
            && self::PLAYING_GAME === $game->getStatus()
            && null !== $game->getWord()
        );
    }

    private function guessLetter($letter, $game)
    {
        $word = $game->getWord();

        if (true === $word->guessLetter($letter)) {
            $this->word_repository->save($word);
            $game = $this->checkIfGameHasFinished($game);
        } else {
            $game = $this->checkIfGameHasFailed($game);
        }

        $this->game_repository->save($game);

        return $game;
    }

    private function checkIfGameHasFinished($game)
    {
        if ($game->getWord()->isWordGuessed()) {
            $game->setStatus(self::FINISHED_GAME);
        }

        return $game;
    }

    private function checkIfGameHasFailed($game)
    {
        if (0 === $game->decrementTries()->getTriesLeft()) {
            $game->setStatus(self::FAILED_GAME);
        }

        return $game;
    }
}
