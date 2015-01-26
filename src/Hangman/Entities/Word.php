<?php

namespace Hangman\Entities;

class Word
{
    const CHARS_NOT_HIDDEN = 2;

    private $word_to_guess;
    private $word;
    private $word_id;

    public function __construct($word_id = null)
    {
        $this->word_id = is_null($word_id) ? uniqid() : $word_id;
    }

    public function getId()
    {
        return $this->word_id;
    }

    public function setWord($word_to_guess, $word = null)
    {
        $this->word_to_guess = $word_to_guess;
        $this->word = (is_null($word)) ? $this->initializeWordWithHiddenChars() : $word;

        return $this;
    }

    private function initializeWordWithHiddenChars()
    {
        $letters = $this->getWordLetters();

        shuffle($letters);

        $letters_to_hide = array_slice($letters, self::CHARS_NOT_HIDDEN);

        return $this->hideGivenChars($letters_to_hide);
    }

    private function hideGivenChars($letters_to_hide)
    {
        return str_replace($letters_to_hide, '.', $this->word_to_guess);
    }

    private function getWordLetters($word = null)
    {
        if (is_null($word)) {
            $word = $this->word_to_guess;
        }

        return array_unique(str_split(preg_replace('/[^a-z]/i', '', $word)));
    }

    public function getWordToGuess()
    {
        return $this->word_to_guess;
    }

    public function getWord()
    {
        return $this->word;
    }

    public function guessLetter($letter)
    {
        if (strpos($this->word_to_guess, $letter)) {
            $this->setLetterGuessed($letter);

            return true;
        }

        return false;
    }

    private function setLetterGuessed($letter)
    {
        $word_letters = $this->getWordLetters($this->word);
        $word_letters[] = $letter;

        $letters_to_hide = array_diff($this->getWordLetters(), $word_letters);

        $this->word = $this->hideGivenChars($letters_to_hide);
    }

    public function isWordGuessed()
    {
        return $this->word_to_guess === $this->word;
    }
}
