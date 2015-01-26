<?php

namespace Hangman\Repositories;

use Hangman\Entities\Word as WordEntity;

interface Word
{
    public function get($word_id);

    public function save(WordEntity $word);
}
