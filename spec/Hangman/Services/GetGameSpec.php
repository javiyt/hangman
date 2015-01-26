<?php

namespace spec\Hangman\Services;

use PhpSpec\ObjectBehavior;

class GetGameSpec extends ObjectBehavior
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
        $this->shouldHaveType('Hangman\Services\GetGame');
        $this->shouldImplement('Hangman\Services\ServiceInterface');
    }

    /**
     * @param Hangman\Entities\Game $game
     */
    public function it_is_possible_to_get_information_of_a_given_game($game)
    {
        $this->game_repository->get(1)->willReturn($game)->shouldBeCalled();

        $this->execute(array('game_id' => 1), array())->shouldHaveType('\Hangman\Entities\Game');
    }

}
