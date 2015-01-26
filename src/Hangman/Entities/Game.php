<?php

namespace Hangman\Entities;

class Game
{
    private $valid_statuses = array('busy', 'fail', 'success');

    private $word_to_guess;
    private $tries_left = 11;
    private $status = 'busy';
    private $game_id;

    public function __construct($game_id = null)
    {
        $this->game_id = is_null($game_id) ? uniqid() : $game_id;
    }

    public function getId()
    {
        return $this->game_id;
    }

    public function getWord()
    {
        return $this->word_to_guess;
    }

    public function setWord(Word $word_to_guess)
    {
        $this->word_to_guess = $word_to_guess;

        return $this;
    }

    public function getTriesLeft()
    {
        return $this->tries_left;
    }

    public function setTriesLeft($tries_left)
    {
        $this->tries_left = $tries_left;

        return $this;
    }

    public function decrementTries()
    {
        --$this->tries_left;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($new_status)
    {
        if (in_array($new_status, $this->valid_statuses)) {
            $this->status = $new_status;
        }

        return $this;
    }
}
