<?php

namespace spec\Hangman\Services;

use PhpSpec\ObjectBehavior;

class AllGamesSpec extends ObjectBehavior
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
        $this->shouldHaveType('Hangman\Services\AllGames');
        $this->shouldImplement('Hangman\Services\ServiceInterface');
    }

    public function it_is_possible_to_get_all_the_games_information()
    {
        $this->game_repository->getAll()->willReturn(new \ArrayIterator())->shouldBeCalled();

        $this->execute(array(), array())->shouldHaveType('\ArrayIterator');
    }

}
