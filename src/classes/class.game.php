<?php

class Game {
    
    const TIME_SELECTION = 60;
    const TIME_MOVE = 30;
    const TIME_TOLERATE = 5;
    
    private $map1;
    private $map2;
    private $map3;
    private $map4;
    private $map5;
    
    public function __construct() {
        global $map1, $map2, $map3, $map4, $map5;
        $this->map1 = $map1;
        $this->map2 = $map2;
        $this->map3 = $map3;
        $this->map4 = $map4;
        $this->map5 = $map5;
    }
    
    public function __set($name, $value) {
        $_SESSION['game'][$name] = $value;
    }
    
    public function __get($name) {
        return $_SESSION['game'][$name];
    }
    
    public function getRandomGame($level, $timezoneOffset, $player) {
        $response = array(
            'level' => $level,
            'obstacles' => array(),
            'message' => ''
        );
        
        $db = Db::getInstance();
        
        $wherePlayed = '';
        if ($player->loggedIn()) {
            $sql = 'SELECT
                        GROUP_CONCAT(game_id)
                    FROM
                        played
                    WHERE
                        player_id = :player_id';
            $statement = $db->prepare($sql);
            $statement->execute(array('player_id' => $player->id));
            $played = $statement->fetchColumn();
            if (!empty($played)) {
                $wherePlayed = " AND id NOT IN ($played) ";
            }
        }
        
        $sql = "SELECT
                    COUNT(*)
                FROM
                    `games`
                WHERE
                    `level` = :level
                    $wherePlayed";
        $statement = $db->prepare($sql);
        $statement->execute(array('level' => $level));
        $rowCount = $statement->fetchColumn();
        if ($rowCount < 1) {
            $response['message'] = "THERE ARE NO GAMES OF LEVEL $level. PLEASE SELECT SOME OTHER LEVEL.";
            return $response;
        }
        
        $randomNumber = rand(0, $rowCount - 1);
        $sql = "SELECT
                    *
                FROM
                    `games`
                WHERE
                    `level` = :level
                    $wherePlayed
                LIMIT $randomNumber, 1";
        $statement = $db->prepare($sql);
        $statement->execute(array('level' => $level));
        $randomGame = $statement->fetch(PDO::FETCH_ASSOC);
        $this->setData($randomGame, $timezoneOffset, $player);
        $response['obstacles'] = json_decode($randomGame['obstacles']);
        
        $response['min_moves'] = $randomGame['min_moves'];
        $response['level'] = $level;
        $response['step'] = $this->step;
        
        return $response;
    }
    
    private function setData($game, $timezoneOffset, $player) {
        $_SESSION['game'] = array();
        
        $this->id = $game['id'];
        $this->obstacles = json_decode($game['obstacles']);
        $this->moves = json_decode($game['moves']);
        $this->moveCounter = 0;
        $this->minMoves = $game['min_moves'];
        $this->numberOfObstacles = $game['number_of_obstacles'];
        $this->level = $game['level'];
        $this->step = $this->getStep($game['level']);
        $this->userMoves = array();
        $this->userMoveCounter = 0;
        $this->player = array();
        $dateObject = new DateTime();
        if (empty($timezoneOffset) || !is_numeric($timezoneOffset) || $timezoneOffset < -720 || $timezoneOffset > 720) {
            $timezoneOffset = 0;
        }
        $timezoneOffset = intval($timezoneOffset);
        $intervalObject = new DateInterval('PT' . abs($timezoneOffset) . 'M');
        if ($timezoneOffset < 0) {
            $dateObject->add($intervalObject);
        } else {
            $dateObject->sub($intervalObject);
        }
        //$this->startTime = date('Y-m-d H:i:s');
        $this->startTime = $dateObject->format('Y-m-d H:i:s');
        $this->time = time();
        
        if ($player->loggedIn()) {
            $this->saveStarted($player->id, $game['id'], $this->startTime);
        }
    }
    
    public function unsetSession() {
        if (isset($_SESSION['game'])) {
            unset($_SESSION['game']);
        }
    }
    
    public function move($playerState) {
        $response = array(
            'valid' => TRUE,
            'message' => '',
            'move' => array()
        );
        
        if ($this->validate($playerState)) {
            if ($this->isEnd()) {
                $response['valid'] = FALSE;
            } else {
                $response['move'] = $this->getNextMove();
            }
        } else {
            $response['valid'] = FALSE;
            $response['message'] = 'INVALID MOVE! THE GAME IS OVER.';
        }
        return $response;
    }
    
    private function validate($player, $save = FALSE) {
        $timePassed = time() - $this->time;
        
        if ($this->userMoveCounter < 1) {
            if ($timePassed > self::TIME_SELECTION + self::TIME_TOLERATE) {
                return FALSE;
            }
            
            foreach ($player as $field) {
                if (!isset($this->map1[$field]) || $field > 3000) {
                    return FALSE;
                    break;
                }
            }
        } else {
            $diffA = array_diff($this->player, $player);
            if (!empty($diffA)) {
                if ($timePassed > self::TIME_MOVE + self::TIME_TOLERATE) {
                    return FALSE;
                }
                
                $diffB = array_diff($player, $this->player);
                $countValuesA = array_count_values($this->player);
                $countValuesB = array_count_values($player);
                foreach ($countValuesB as $value => $count) {
                    if ($count > 1 && isset($countValuesA[$value]) && $count > $countValuesA[$value]) {
                        $diffB[] = $value;
                    }
                }
                $diffB = array_unique($diffB);
                
                sort($diffA);
                sort($diffB);
                
                switch (count($diffA)) {
                    case 1:
                        if (!isset($this->map1[$diffA[0]]) || !in_array($diffB, $this->map1[$diffA[0]])) {
                            return FALSE;
                        }
                        break;
                    case 2:
                        if (!isset($this->map2[$diffA[0]][$diffA[1]]) || !in_array($diffB, $this->map2[$diffA[0]][$diffA[1]])) {
                            return FALSE;
                        }
                        break;
                    case 3:
                        if (!isset($this->map3[$diffA[0]][$diffA[1]][$diffA[2]]) || !in_array($diffB, $this->map3[$diffA[0]][$diffA[1]][$diffA[2]])) {
                            return FALSE;
                        }
                        break;
                    case 4:
                        if (!isset($this->map4[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]]) || !in_array($diffB, $this->map4[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]])) {
                            return FALSE;
                        }
                        break;
                    case 5:
                        if (!isset($this->map5[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]][$diffA[4]]) || !in_array($diffB, $this->map5[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]][$diffA[4]])) {
                            return FALSE;
                        }
                        break;
                }
            }
        }
        
        $this->player = $player;
        $temp = $this->userMoves;
        $temp[] = $player;
        $this->userMoves = $temp;
        $this->userMoveCounter++;
        $this->time = time();
        return TRUE;
    }
    
    private function isEnd() {
        if ($this->level > 8) {
            return round(3 * $this->level - ($this->userMoveCounter - $this->minMoves - 1) * ($this->level / 2 - 2.5)) <= 0;
        } else {
            return round(3 * $this->level - ($this->userMoveCounter - $this->minMoves - 1) * $this->step) <= 0;
        }
    }
    
    private function getNextMove() {
        if ($this->moveCounter > count($this->moves) - 1) {
            $this->moveCounter++;
            return FALSE;
        }
        $nextMove = $this->moves[$this->moveCounter];
        $this->moveCounter++;
        return $nextMove;
    }
    
    public function setTime() {
        $this->time = time();
    }
    
    public function save($player, $validate = FALSE, $playerState = array()) {
        
        if ($validate && !$this->validate($playerState)) {
            $response = array(
                'valid' => FALSE,
                'points' => 0,
                'message' => 'INVALID MOVE! THE GAME IS OVER.'
            );
            return $response;
        }
        
        $response = array(
            'valid' => TRUE,
            'error' => FALSE,
            'saved' => FALSE,
            'points' => 0,
            'message' => ''
        );
        $points = $this->getPoints();
        
        if ($player->loggedIn()) {
            $db = Db::getInstance();
            
            if (self::allowSave($player->id, $points)) {
                $sql = 'INSERT INTO
                            played
                            (game_id, player_id, start_time, moves, raw_moves, moves_counter, points)
                        VALUES
                            (:game_id, :player_id, :start_time, :moves, :raw_moves, :moves_counter, :points)';
                $statement = $db->prepare($sql);
                $params = array(
                    'game_id' => $this->id,
                    'player_id' => $player->id,
                    'moves' => $this->prepareMoves($this->userMoves),
                    'raw_moves' => json_encode($this->userMoves, JSON_NUMERIC_CHECK),
                    'moves_counter' => $this->userMoveCounter - 1,
                    'points' => $points,
                    'start_time' => $this->startTime
                );
                if ($statement->execute($params)) {
                    $response['saved'] = TRUE;
                    $response['message'] = 'GAME OVER - SCORE: ' . $points . '/' . 3 * $this->level;
                    $response['points'] = $points;
                    $player->updateRating();
                    //$response['rating'] = $player->rating;
                    //$response['standing'] = $player->getStanding();
                } else {
                    $response['error'] = TRUE;
                    $response['message'] = 'AN ERROR OCCURRED WHILE SAVING GAME! SORRY, YOUR SCORE IS NOT SAVED.';
                }
            } else {
                $response['saved'] = TRUE;
                $response['message'] = 'GAME OVER - SCORE: ' . $points . '/' . 3 * $this->level;
                $response['points'] = $points;
            }
            
            $this->deleteStarted($player->id, $this->id);
        } else {
            $response['message'] = 'GAME OVER - SCORE: ' . $points . '/' . 3 * $this->level;
            $response['points'] = $points;
        }
        
        $this->unsetSession();
        
        return $response;
    }
    
    private function getPoints() {
        if ($this->level > 8) {
            $points = round(3 * $this->level - ($this->userMoveCounter - $this->minMoves - 1) * ($this->level / 2 - 2.5));
        } else {
            $points = round(3 * $this->level - ($this->userMoveCounter - $this->minMoves - 1) * $this->step);
        }
        if ($points < 0) {
            $points = 0;
        }
        return $points;
    }
    
    private function prepareMoves($moves) {
        $prepared = array();
        
        if (!empty($moves)) {
            $prepared[] = $moves[0];
            
            for ($i = 1; $i < count($moves); $i++) {
                if (empty($moves[$i - 1])) {
                    $prepared[] = $moves[$i];
                    continue;
                }
                
                $temp = array();
                $diffA = array_diff($moves[$i - 1], $moves[$i]);
                if (empty($diffA)) {
                    $prepared[] = array();
                    continue;
                }
                $diffB = array_diff($moves[$i], $moves[$i - 1]);
                
                $countValuesA = array_count_values($moves[$i - 1]);
                $countValuesB = array_count_values($moves[$i]);
                foreach ($countValuesB as $value => $count) {
                    if ($count > 1 && isset($countValuesA[$value]) && $count > $countValuesA[$value]) {
                        $diffB[] = $value;
                    }
                }
                
                $diffB = array_unique($diffB);
                
                sort($diffA);
                sort($diffB);
                
                switch (count($diffA)) {
                    case 1:
                    case 2:
                        $prepared[] = array_merge($diffA, array($diffB[0]));
                        $maps = FALSE;
                        break;
                    case 3:
                        $maps = $this->map3[$diffA[0]][$diffA[1]][$diffA[2]];
                        break;
                    case 4:
                        $maps = $this->map4[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]];
                        break;
                    case 5:
                        $maps = $this->map5[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]][$diffA[4]];
                        break;
                }
                
                if (!$maps) {
                    continue;
                }
                
                $fields = array();
                foreach ($maps as $map) {
                    $fields = array_merge($fields, $map);
                }
                $countFields = array_count_values($fields);
                
                foreach ($diffB as $field) {
                    if (isset($countFields[$field]) && $countFields[$field] == 1) {
                        $prepared[] = array_merge($diffA, array($field));
                        break;
                    }
                }
            }
        }
        
        return json_encode($prepared, JSON_NUMERIC_CHECK);
    }
    
    public function quit($player) {
        if ($this->level > 8) {
            $this->userMoveCounter = ceil(6 * $this->level / ($this->level - 5) + $this->minMoves) + 1;
        } else {
            $this->userMoveCounter = ceil(3 * $this->level / $this->step + $this->minMoves) + 1;
        }
        return $this->save($player, FALSE, array());
    }
    
    public static function allowSave($playerId, $points) {
        $db = Db::getInstance();
        $allow = 1;
        if (empty($points)) {
            $sql = 'SELECT
                        IF(SUM(p.points) > 0, 1, 0)
                    FROM
                        (
                            SELECT
                                points
                            FROM
                                played
                            WHERE
                                player_id = :player_id
                            ORDER BY
                                id DESC
                            LIMIT
                                20
                        ) p';
            $sth = $db->prepare($sql);
            $sth->execute(array('player_id' => $playerId));
            $allow = $sth->fetchColumn();
        }
        return $allow;
    }
    
    private function saveStarted($playerId, $gameId, $startTime) {
        $db = Db::getInstance();
        $sql = 'INSERT INTO
                    started
                    (player_id, game_id, start_time, start_time_utc)
                VALUES
                    (:player_id, :game_id, :start_time, UTC_TIMESTAMP())';
        $sth = $db->prepare($sql);
        $params = array(
            'player_id' => $playerId,
            'game_id' => $gameId,
            'start_time' => $startTime
        );
        $sth->execute($params);
    }
    
    private function deleteStarted($playerId, $gameId) {
        $db = Db::getInstance();
        $sql = 'DELETE FROM
                    started
                WHERE
                    player_id = :player_id AND game_id = :game_id';
        $sth = $db->prepare($sql);
        $params = array(
            'player_id' => $playerId,
            'game_id' => $gameId
        );
        $sth->execute($params);
    }
    
    private function getStep($level) {
        if ($level > 4) {
            return 2;
        } else if ($level > 2) {
            return 1.5;
        } else if ($level == 2) {
            return 1;
        } else {
            return 0.5;
        }
    }
    
}

?>
