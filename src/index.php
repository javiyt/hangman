<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\Slim();

$app->container->singleton('mongodb', function () {
    $mongo_client = new MongoClient('mongodb://localhost:27017');

    return new MongoDB($mongo_client, 'hangman');
});

$app->container->singleton('word_repository', function () use ($app) {
    return new Hangman\Repositories\MongoDB\Word($app->mongodb);
});

$app->container->singleton('game_repository', function () use ($app) {
    return new Hangman\Repositories\MongoDB\Game($app->mongodb, $app->word_repository);
});

$app->gameParser = $app->container->protect(function ($game) {
    return array(
        'word'          => (null !== ($word = $game->getWord())) ? $word->getWord() : null,
        'game_id'       => $game->getId(),
        'tries_left'    => $game->getTriesLeft(),
        'status'        => $game->getStatus(),
    );
});

$app->selectWordFromFile = $app->container->protect(function () {
    $random_line = null;
    $line = null;
    $count = 0;
    $file = new SplFileObject(__DIR__ . '/../words.english');
    while ((false === $file->eof()) && (($line = $file->fgets()) !== false)) {
        $count++;
        if ((rand() % $count) == 0) {
          $random_line = $line;
        }
    }

    return trim($random_line);
});

$app->post('/games', function () use ($app) {
    $game_service = new Hangman\Services\NewGame();

    $game_service->initialize($app->word_repository, $app->game_repository);

    $new_game = $game_service->execute(array(
        'word'  => call_user_func($app->selectWordFromFile)
    ), array());

    echo json_encode(call_user_func($app->gameParser, $new_game));
});

$app->get('/games', function () use ($app) {
    $game_service = new Hangman\Services\AllGames();

    $game_service->initialize($app->word_repository, $app->game_repository);

    $all_games = $game_service->execute(array(), array());

    $parsed_games = array();
    foreach ($all_games as $game) {
        $parsed_games[] = call_user_func($app->gameParser, $game);
    }

    echo json_encode($parsed_games);
});

$app->get('/games/:id', function ($id) use ($app) {
    $game_service = new Hangman\Services\GetGame();

    $game_service->initialize($app->word_repository, $app->game_repository);

    $game = $game_service->execute(array('game_id' => $id), array());

    echo json_encode(call_user_func($app->gameParser, $game));
});

$app->post('/games/:id', function ($id) use ($app) {
    $game_service = new Hangman\Services\GuessLetter();

    $game_service->initialize($app->word_repository, $app->game_repository);

    parse_str($app->request->getBody());
    $game = $game_service->execute(array('game_id' => $id), array('char' => $char));

    echo json_encode(call_user_func($app->gameParser, $game));
});

$app->run();
