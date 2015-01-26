<?php

namespace spec\Hangman\Entities;

use PhpSpec\ObjectBehavior;

class WordSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Hangman\Entities\Word');
    }

    public function it_is_possible_to_set_up_a_word_to_guess()
    {
        $word = 'testing';

        $this->setWord($word)->getWordToGuess()->shouldEqual($word);
    }

    public function it_generates_a_word_with_hidden_letters_when_a_word_to_guess_is_set_up()
    {
        $this->setWord('testing')->getWord()->shouldBeString();
    }

    public function it_is_possible_to_set_up_a_word_with_hidden_letters_to_guess()
    {
        $word_with_hidden_letters = 't..t.n.';
        $this->setWord('testing', $word_with_hidden_letters)->getWord()->shouldEqual($word_with_hidden_letters);
    }

    public function it_is_possible_to_guess_letters_from_the_word()
    {
        $this->setWord('testing')->guessLetter('e')->shouldEqual(true);
    }

    public function it_fails_when_a_letter_is_not_found_in_the_word()
    {
        $this->setWord('testing')->guessLetter('z')->shouldEqual(false);
    }

    public function it_is_possible_to_know_when_the_word_has_been_guessed()
    {
        $this->setWord('testing', 'testing')->isWordGuessed()->shouldEqual(true);
    }
}
