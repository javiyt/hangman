<?php

namespace Hangman\Repositories\MongoDB;

use Hangman\Entities\Word as WordEntity;
use Hangman\Repositories\Word as WordRepositoryInterface;
use MongoDB;

class Word implements WordRepositoryInterface
{
    const DB_COLLECTION = 'words';

    private $db;

    public function __construct(MongoDB $db)
    {
        $this->db = $db;
    }

    private function collection()
    {
        return $this->db->selectCollection(self::DB_COLLECTION);
    }

    public function get($word_id)
    {
        $word = $this->collection()->findOne(array('_id' => $word_id));

        $found = new WordEntity($word_id);
        if (!empty($word)) {
            $found->setWord($word['word_to_guess'], $word['word']);
        }

        return $found;
    }

    public function save(WordEntity $word)
    {
        $word_to_save = array(
            '_id'           => $word->getId(),
            'word_to_guess' => $word->getWordToGuess(),
            'word'          => $word->getWord(),
        );

        return $this->collection()->save($word_to_save);
    }
}
