<?php

namespace spec\Hangman\Services;

use PhpSpec\ObjectBehavior;

class GuessLetterSpec extends ObjectBehavior
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
        $this->shouldHaveType('Hangman\Services\GuessLetter');
        $this->shouldImplement('Hangman\Services\ServiceInterface');
    }

    /**
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_is_possible_to_guess_a_letter_from_an_existing_game($game, $word)
    {
        $word->guessLetter('a')->willReturn(true);
        $word->isWordGuessed()->willReturn(false);

        $game->getWord()->willReturn($word);
        $game->decrementTries()->shouldNotBeCalled();
        $game->getTriesLeft()->shouldBeCalled()->willReturn(11);
        $game->getStatus()->shouldBeCalled()->willReturn('busy');

        $this->word_repository->save($word)->shouldBeCalled();
        $this->game_repository->get(1)->willReturn($game);
        $this->game_repository->save($game)->shouldBeCalled();

        $this->execute(array('game_id' => 1), array('char' => 'a'))->shouldHaveType('Hangman\Entities\Game');
    }

    /**
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_decrement_tries_when_provided_letter_not_in_word($game, $word)
    {
        $word->guessLetter('a')->willReturn(false);
        $word->isWordGuessed()->shouldNotBeCalled();

        $game->getWord()->willReturn($word);
        $game->decrementTries()->shouldBeCalled()->willReturn($game);
        $game->getTriesLeft()->willReturn(10);
        $game->getStatus()->shouldBeCalled()->willReturn('busy');

        $this->word_repository->save($word)->shouldNotBeCalled();
        $this->game_repository->get(1)->willReturn($game);
        $this->game_repository->save($game)->shouldBeCalled();

        $this->execute(array('game_id' => 1), array('char' => 'a'))->shouldHaveType('Hangman\Entities\Game');
    }

    /**
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_is_not_possible_to_guess_a_letter_from_a_non_existing_game($game, $word)
    {
        $word->guessLetter('a')->shouldNotBeCalled();
        $word->isWordGuessed()->shouldNotBeCalled();

        $game->getWord()->willReturn(null);
        $game->decrementTries()->shouldNotBeCalled();
        $game->getTriesLeft()->shouldBeCalled()->willReturn(11);
        $game->getStatus()->shouldBeCalled()->willReturn('busy');

        $this->word_repository->save($word)->shouldNotBeCalled();
        $this->game_repository->get(1)->willReturn($game);
        $this->game_repository->save($game)->shouldNotBeCalled();

        $this->execute(array('game_id' => 1), array('char' => 'a'))->shouldHaveType('Hangman\Entities\Game');
    }

    /**
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_is_not_possible_to_guess_an_invalid_letter($game, $word)
    {
        $word->guessLetter()->shouldNotBeCalled();
        $word->isWordGuessed()->shouldNotBeCalled();

        $game->getWord()->willReturn($word);
        $game->decrementTries()->shouldNotBeCalled();
        $game->getTriesLeft()->shouldNotBeCalled();
        $game->getStatus()->shouldNotBeCalled();

        $this->word_repository->save($word)->shouldNotBeCalled();
        $this->game_repository->get(1)->willReturn($game);
        $this->game_repository->save($game)->shouldNotBeCalled();

        $this->execute(array('game_id' => 1), array('char' => '1'))->shouldHaveType('Hangman\Entities\Game');
    }

    /**
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_set_a_game_as_failed_when_there_is_not_more_tries_left($game, $word)
    {
        $word->guessLetter('a')->willReturn(false);
        $word->isWordGuessed()->shouldNotBeCalled();

        $game->getWord()->willReturn($word);
        $game->decrementTries()->shouldBeCalled()->willReturn($game);
        $game->getTriesLeft()->willReturn(1, 0);
        $game->getStatus()->shouldBeCalled()->willReturn('busy');
        $game->setStatus('fail')->shouldBeCalled();

        $this->word_repository->save($word)->shouldNotBeCalled();
        $this->game_repository->get(1)->willReturn($game);
        $this->game_repository->save($game)->shouldBeCalled();

        $this->execute(array('game_id' => 1), array('char' => 'a'))->shouldHaveType('Hangman\Entities\Game');
    }

    /**
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_set_a_game_as_success_when_the_word_has_been_guessed($game, $word)
    {
        $word->guessLetter('a')->willReturn(true);
        $word->isWordGuessed()->willReturn(true);

        $game->getWord()->willReturn($word);
        $game->decrementTries()->shouldNotBeCalled()->willReturn($game);
        $game->getTriesLeft()->willReturn(10);
        $game->getStatus()->shouldBeCalled()->willReturn('busy');
        $game->setStatus('success')->shouldBeCalled();

        $this->word_repository->save($word)->shouldBeCalled();
        $this->game_repository->get(1)->willReturn($game);
        $this->game_repository->save($game)->shouldBeCalled();

        $this->execute(array('game_id' => 1), array('char' => 'a'))->shouldHaveType('Hangman\Entities\Game');
    }

    /**
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_is_not_possible_to_guess_a_letter_from_a_failed_game($game, $word)
    {
        $word->guessLetter('a')->shouldNotBeCalled();
        $word->isWordGuessed()->shouldNotBeCalled();

        $game->getWord()->shouldNotBeCalled();
        $game->decrementTries()->shouldNotBeCalled();
        $game->getTriesLeft()->shouldBeCalled()->willReturn(11);
        $game->getStatus()->shouldBeCalled()->willReturn('fail');

        $this->word_repository->save($word)->shouldNotBeCalled();
        $this->game_repository->get(1)->willReturn($game);
        $this->game_repository->save($game)->shouldNotBeCalled();

        $this->execute(array('game_id' => 1), array('char' => 'a'))->shouldHaveType('Hangman\Entities\Game');
    }

}
