<?php

namespace spec\Hangman\Repositories\MongoDB;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ArrayIterator;

class GameSpec extends ObjectBehavior
{
    private $db;
    private $word_repository;

    /**
     * @param MongoDB                           $db
     * @param Hangman\Repositories\MongoDB\Word $word_repository
     */
    public function let($db, $word_repository)
    {
        $this->db = $db;
        $this->word_repository = $word_repository;

        $this->beConstructedWith($db, $word_repository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Hangman\Repositories\MongoDB\Game');
        $this->shouldImplement('Hangman\Repositories\Game');
    }

    /**
     * @param MongoCollection       $collection
     * @param Hangman\Entities\Word $word_entity
     */
    public function it_is_possible_to_get_a_game_from_database($collection, $word_entity)
    {
        $game_found = array(
            'word_id'       => 1,
            'tries_left'    => 5,
            'status'        => 'busy',
        );

        $collection->findOne(array('_id' => 1))->willReturn($game_found);
        $this->db->selectCollection('games')->willReturn($collection);

        $this->word_repository->get(1)->willReturn($word_entity);

        $found = $this->get(1);

        $found->shouldHaveType('Hangman\Entities\Game');
        $found->getStatus()->shouldEqual('busy');
    }

    /**
     * @param MongoCollection $collection
     */
    public function it_returns_an_empty_game_when_not_found_in_database($collection)
    {
        $collection->findOne(array('_id' => 1))->willReturn(null);
        $this->db->selectCollection('games')->willReturn($collection);

        $found = $this->get(1);

        $found->shouldHaveType('Hangman\Entities\Game');
        $found->getWord()->shouldEqual(null);
    }

    /**
     * @param MongoCollection       $collection
     * @param Hangman\Entities\Game $game
     * @param Hangman\Entities\Word $word
     */
    public function it_is_possible_to_save_a_game($collection, $game, $word)
    {
        $word->getId()->willReturn(1);

        $game->getId()->shouldBeCalled();
        $game->getWord()->willReturn($word);
        $game->getTriesLeft()->shouldBeCalled();
        $game->getStatus()->shouldBeCalled();

        $collection->save(Argument::any())->willReturn(true);
        $this->db->selectCollection('games')->willReturn($collection);

        $this->word_repository->save(Argument::any())->shouldBeCalled();

        $this->save($game)->shouldEqual(true);
    }

    /**
     * @param MongoCollection       $collection
     * @param Hangman\Entities\Word $word
     */
    public function it_is_possible_to_get_all_the_games($collection, $word)
    {
        $games = array(
            array(
                '_id'       => 1,
                'word_id'       => 1,
                'tries_left'    => 1,
                'status'        => 'busy',
            )
        );

        $collection->find()->willReturn(new ArrayIterator($games));
        $this->db->selectCollection('games')->willReturn($collection);

        $this->word_repository->get(1)->willReturn($word)->shouldBeCalled();

        $games = $this->getAll();
        $games->shouldHaveType('\ArrayIterator');
        $games->current()->shouldHaveType('Hangman\Entities\Game');
    }

}
