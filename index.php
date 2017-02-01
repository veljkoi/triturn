<?php

date_default_timezone_set('UTC');

spl_autoload_register(function($class) {
    require 'classes/class.' . strtolower($class) . '.php';
});

$action = Util::getHttpRequest('action');

if (in_array($action, array('move', 'save_game', 'quit'))) {
    require 'maps/map_1.php';
    require 'maps/map_2.php';
    require 'maps/map_3.php';
    require 'maps/map_4.php';
    require 'maps/map_5.php';
}

session_start();

$anchor = '';
$message = '';
$register = FALSE;
$resetPassword = FALSE;
$profile = FALSE;

$player = new Player();

switch ($action) {
    case 'login':
        $username = Util::getHttpRequest('username');
        $password = Util::getHttpRequest('password');
        $message = $player->login($username, $password);
        $anchor = 'login';
        break;
    case 'logout':
        $player->logout();
        $anchor = 'login';
        break;
    case 'register':
        $username = Util::getHttpRequest('username');
        $password = Util::getHttpRequest('password');
        $repeatPassword = Util::getHttpRequest('repeat_password');
        $email = Util::getHttpRequest('email');
        $recaptcha = Util::getHttpRequest('g-recaptcha-response');
        $message = $player->register($username, $password, $repeatPassword, $email, $recaptcha);
        if (empty($message)) {
            $anchor = 'login';
        } else {
            $anchor = 'register';
        }
        $register = TRUE;
        break;
    case 'password_forgotten':
        $username = Util::getHttpRequest('username');
        $password = Util::getHttpRequest('password');
        $repeatPassword = Util::getHttpRequest('repeat_password');
        $email = Util::getHttpRequest('email');
        $message = $player->passwordForgotten($username, $password, $repeatPassword, $email);
        $anchor = 'reset-password';
        $resetPassword = TRUE;
        break;
    case 'activate':
        $hash = Util::getHttpRequest('hash');
        $message = $player->activate($hash);
        $anchor = 'login';
        $profile = TRUE;
        break;
    case 'profile':
        $username = Util::getHttpRequest('username');
        $password = Util::getHttpRequest('password');
        $repeatPassword = Util::getHttpRequest('repeat_password');
        $email = Util::getHttpRequest('email');
        $message = $player->profile($username, $password, $repeatPassword, $email);
        $anchor = 'login';
        $profile = TRUE;
        break;
    case 'fb_login':
        $id = Util::getHttpRequest('id');
        $firstName = Util::getHttpRequest('first_name');
        $lastName = Util::getHttpRequest('last_name');
        $response = $player->fbLogin($id, $firstName, $lastName);
        echo json_encode($response);
        exit();
        break;
    case 'get_game':
        $level = Util::getHttpRequest('level');
        $timezoneOffset = Util::getHttpRequest('timezoneOffset');
        $game = new Game();
        $response = $game->getRandomGame($level, $timezoneOffset, $player);
        echo json_encode($response);
        exit();
        break;
    case 'move':
        $playerState = Util::getHttpRequest('player');
        if (empty($playerState)) {
            $playerState = array();
        }
        $game = new Game();
        $response = $game->move($playerState);
        echo json_encode($response);
        exit();
        break;
    case 'set_time':
        $game = new Game();
        $game->setTime();
        exit();
        break;
    case 'save_game':
        $playerState = Util::getHttpRequest('player');
        $validate = Util::getHttpRequest('validate');
        $game = new Game();
        $response = $game->save($validate, $playerState, $player);
        echo json_encode($response);
        exit();
        break;
    case 'quit':
        $game = new Game();
        $response = $game->quit($player);
        echo json_encode($response);
        exit();
        break;
    case 'unset_game_session':
        $game = new Game();
        $game->unsetSession();
        exit();
        break;
    case 'get_played':
        $id = Util::getHttpRequest('id');
        $response = $player->getPlayed($id);
        echo json_encode($response);
        exit();
        break;
    case 'get_played_list':
        $response = $player->getPlayedList();
        echo $response;
        exit();
        break;
    case 'get_demo':
        $response = Util::getDemo();
        echo json_encode($response);
        exit();
        break;
    case 'get_standings':
        $response = Util::getStandings($player);
        echo $response;
        exit();
        break;
    default :
        $player->init();
        break;
}

$levels = $player->getLevels();
$standings = Util::getStandings($player);
$played = $player->getPlayedList();

$loggedIn = $player->loggedIn();

include ('table.php');

?>
