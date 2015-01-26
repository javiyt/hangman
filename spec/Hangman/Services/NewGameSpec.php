<?php

namespace spec\Hangman\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NewGameSpec extends ObjectBehavior
{
    private $word_repository;
    private $game_repository;

    /**
     * @param Hangman\Repositories\MongoDB\Word $word_repository
     * @param Hangman\Repositories\MongoDB\Game $game_repository [description]
     */
    public function let($word_repository, $game_repository)
    {
        $this->word_repository = $word_repository;
        $this->game_repository = $game_repository;

        $this->initialize($word_repository, $game_repository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Hangman\Services\NewGame');
        $this->shouldImplement('Hangman\Services\ServiceInterface');
    }

    public function it_is_possible_to_start_a_new_game()
    {
        $this->game_repository->save(Argument::any())->shouldBeCalled();

        $this->execute(array('word'=> 'test'), array())->shouldHaveType('Hangman\Entities\Game');
    }

}
