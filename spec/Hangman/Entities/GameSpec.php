<?php

namespace spec\Hangman\Entities;

use PhpSpec\ObjectBehavior;

class GameSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Hangman\Entities\Game');
    }

    /**
     * @param Hangman\Entities\Word $word
     */
    public function it_is_possible_to_set_an_get_a_word($word)
    {
        $this->setWord($word)->getWord()->shouldEqual($word);
    }

    public function it_initialize_the_number_of_tries()
    {
        $this->getTriesLeft()->shouldEqual(11);
    }

    public function it_should_be_possible_to_decrement_the_number_of_tries()
    {
        $this->decrementTries()->getTriesLeft()->shouldEqual(10);
    }

    public function it_should_be_possible_to_set_tries_left()
    {
        $this->setTriesLeft(5)->getTriesLeft()->shouldEqual(5);
    }

    public function it_should_be_possible_to_change_the_status_of_the_game_to_a_valid_status()
    {
        $valid_status = 'fail';

        $this->setStatus($valid_status)->getStatus()->shouldEqual($valid_status);
    }

    public function it_is_not_possible_to_change_the_status_of_the_game_to_an_invalid_status()
    {
        $busy_status = 'busy';

        $this->getStatus()->shouldEqual($busy_status);

        $this->setStatus('non_existing')->getStatus()->shouldEqual($busy_status);
    }
}
