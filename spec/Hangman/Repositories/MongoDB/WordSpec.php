<?php

namespace spec\Hangman\Repositories\MongoDB;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WordSpec extends ObjectBehavior
{
    private $db;

    /**
     * @param MongoDB $db
     */
    public function let($db)
    {
        $this->db = $db;

        $this->beConstructedWith($db);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Hangman\Repositories\MongoDB\Word');
        $this->shouldImplement('Hangman\Repositories\Word');
    }

    /**
     * @param MongoCollection $collection
     */
    public function it_is_possible_to_get_a_word_from_database($collection)
    {
        $word_found = array(
            'word_to_guess' => 'test',
            'word'          => 'test',
        );

        $collection->findOne(array('_id' => 1))->willReturn($word_found);
        $this->db->selectCollection('words')->willReturn($collection);

        $found = $this->get(1);

        $found->shouldHaveType('Hangman\Entities\Word');
        $found->getWord()->shouldEqual('test');
    }

    /**
     * @param MongoCollection $collection
     */
    public function it_returns_an_empty_word_when_not_found_in_database($collection)
    {
        $collection->findOne(array('_id' => 1))->willReturn(null);
        $this->db->selectCollection('words')->willReturn($collection);

        $found = $this->get(1);

        $found->shouldHaveType('Hangman\Entities\Word');
        $found->getWord()->shouldEqual(null);
    }

    /**
     * @param MongoCollection       $collection
     * @param Hangman\Entities\Word $word
     */
    public function it_is_possible_to_save_a_word($collection, $word)
    {
        $word->getId()->shouldBeCalled();
        $word->getWordToGuess()->shouldBeCalled();
        $word->getWord()->shouldBeCalled();

        $collection->save(Argument::any())->willReturn(true);
        $this->db->selectCollection('words')->willReturn($collection);

        $this->save($word)->shouldEqual(true);
    }

}
