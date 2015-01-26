<?php

namespace Hangman\Repositories\MongoDB;

use Hangman\Entities\Game as GameEntity;
use Hangman\Repositories\Game as GameRepositoryInterface;
use MongoDB;
use ArrayIterator;

class Game implements GameRepositoryInterface
{
    const DB_COLLECTION = 'games';

    private $db;

    public function __construct(MongoDB $db, Word $word_repository)
    {
        $this->db = $db;
        $this->word_repository = $word_repository;
    }

    private function collection()
    {
        return $this->db->selectCollection(self::DB_COLLECTION);
    }

    public function get($game_id)
    {
        $game = $this->collection()->findOne(array('_id' => $game_id));

        $found = new GameEntity($game_id);
        if (!empty($game)) {
            $found->setWord($this->word_repository->get($game['word_id']));
            $found->setTriesLeft($game['tries_left']);
            $found->setStatus($game['status']);
        }

        return $found;
    }

    public function save(GameEntity $game)
    {
        $game_to_save = array(
            '_id'           => $game->getId(),
            'word_id'       => $game->getWord()->getId(),
            'tries_left'    => $game->getTriesLeft(),
            'status'        => $game->getStatus(),
        );

        $this->word_repository->save($game->getWord());

        return $this->collection()->save($game_to_save);
    }

    public function getAll()
    {
        $games_found = $this->collection()->find();

        $games = new ArrayIterator();

        foreach ($games_found as $game_found) {

            $game_entity = new GameEntity($game_found['_id']);
            $game_entity->setWord($this->word_repository->get($game_found['word_id']));
            $game_entity->setTriesLeft($game_found['tries_left']);
            $game_entity->setStatus($game_found['status']);

            $games->append($game_entity);
        }

        return $games;
    }
}
