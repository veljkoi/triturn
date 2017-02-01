<?php

class Util {
    
    const STANDINGS_LIMIT = 30;
    
    const FB_APP_ID = '961474663894011';
    const FB_VERSION = 'v2.3';
    
    public static function getHttpRequest($param) {
        if (isset($_REQUEST[$param])) {
            return $_REQUEST[$param];
        } else {
            return '';
        }
    }
    
    public static function getStanding($rating) {
        $db = Db::getInstance();
        $sql = "SELECT
                    COUNT(id) + 1
                FROM
                    players
                WHERE
                    active = 1 AND
                    rating > $rating";
        $statement = $db->prepare($sql);
        $statement->execute();
        return $statement->fetchColumn();
    }
    
    public static function getDemo() {
        $db = Db::getInstance();
        $sql = 'SELECT COUNT(*) FROM `demos`';
        $statement = $db->prepare($sql);
        $statement->execute();
        $rowCount = $statement->fetchColumn();
        
        $randomNumber = rand(0, $rowCount - 1);
        
        $sql = "SELECT * FROM `demos` LIMIT $randomNumber, 1";
        $statement = $db->prepare($sql);
        $statement->execute();
        $response = $statement->fetch(PDO::FETCH_ASSOC);
        
        $response['obstacles'] = json_decode($response['obstacles']);
        $response['moves'] = json_decode($response['moves']);
        $response['raw_moves'] = json_decode($response['raw_moves']);
        return $response;
    }
    
    public static function getStandings($player) {
        
        $rest = array();
        $standing = NULL;
        
        if ($player->loggedIn() && $player->rating > 0) {
            $standing = $player->getStanding();
            if ($standing > 30) {
                $top = self::getPlayers(0, 15);
                $rest = self::getPlayers($standing - 8 - 1, 14, $player->id);
            } else {
                $top = self::getPlayers(0, 30, $player->id);
            }
        } else {
            $top = self::getPlayers(0, 30);
        }
        
        return self::getTable($top, $rest, $standing);
    }
    
    private static function getPlayers($offset, $limit, $id = 0) {
        
        $db = Db::getInstance();
        $sql = "SELECT
                    id,
                    username,
                    rating,
                    IF(id = $id, 'class=\"red\"', '') AS class
                FROM
                    players
                WHERE
                    active = 1 AND
                    rating > 0
                ORDER BY
                    rating DESC, class DESC
                LIMIT $offset, $limit";
        $sth = $db->prepare($sql);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private static function getTable($top, $rest, $standing) {
        
        $table = '<table class="u-full-width"><thead><tr><th class="td-right"></th><th>player</th><th class="td-right">rating</th></tr></thead><tbody>';
        if (empty($top)) {
            $table .= '<tr><td colspan="2">empty standings</td></tr>';
        } else {
            $currentRating = NULL;
            $currentStanding = 0;
            
            foreach ($top as $index => $row) {
                
                if ($currentRating != $row['rating']) {
                    $currentStanding = $index + 1;
                    $currentRating = $row['rating'];
                }
                $table .= '<tr ' . $row['class'] . '><td class="td-right">' . $currentStanding . '.</td><td>' . $row['username'] . '</td><td class="td-right">' . number_format($row['rating'], 2) . '</td></tr>';
            }
            
            if (!empty($rest)) {
                $table .= '<tr><td class="td-right">...</td><td>...</td><td class="td-right">...</td></tr>';
                
                $currentRating = NULL;
                $currentStanding = $standing - 8;
                $count = $currentStanding;
                foreach ($rest as $row) {
                    
                    if ($currentRating != $row['rating']) {
                        $currentStanding = $count;
                        $currentRating = $row['rating'];
                    }
                    $table .= '<tr ' . $row['class'] . '><td class="td-right">' . $currentStanding . '.</td><td>' . $row['username'] . '</td><td class="td-right">' . number_format($row['rating'], 2) . '</td></tr>';
                    $count++;
                }
            }
        }
        $table .= '</tbody></table>';
        return $table;
    }
    
    public static function processStarted() {
        
        $db = Db::getInstance();
        $sql = 'SELECT
                    s.*
                FROM
                    started s
                LEFT JOIN
                    played p
                    ON s.player_id = p.player_id AND s.game_id = p.game_id
                WHERE
                    TIMESTAMPADD(MINUTE, 60, s.start_time_utc) < UTC_TIMESTAMP()
                    AND p.id IS NULL';
        $sth = $db->prepare($sql);
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($rows as $row) {
            if (Game::allowSave($row['player_id'], 0)) {
                $sql = "INSERT INTO
                            played
                            (game_id, player_id, start_time, moves, raw_moves, moves_counter, points)
                        VALUES
                            (:game_id, :player_id, :start_time, '[]', '[]', 0, 0)";
                $statement = $db->prepare($sql);
                $params = array(
                    'game_id' => $row['game_id'],
                    'player_id' => $row['player_id'],
                    'start_time' => $row['start_time']
                );
                $statement->execute($params);
            }
        }
        
        $sql = "DELETE FROM
                    started
                WHERE
                    TIMESTAMPADD(MINUTE, 60, start_time_utc) < UTC_TIMESTAMP()";
        $sth = $db->prepare($sql);
        $sth->execute();
    }
    
    public static function clearPlayed() {
        $db = Db::getInstance();
        
        $sql = 'SELECT
                    GROUP_CONCAT(id)
                FROM
                    played
                GROUP BY
                    player_id
                HAVING
                    COUNT(*) > 20';
        $sth = $db->prepare($sql);
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($rows)) {
            $redundant = array();
            foreach ($rows as $row) {
                $arr = explode(',', $row);
                arsort($arr);
                $arr = array_slice($arr, 20);
                $redundant = array_merge($redundant, $arr);
            }
            
            $sql = 'DELETE FROM
                        played
                    WHERE
                        id IN (' . implode(',', $redundant) . ')';
            $sth = $db->prepare($sql);
            $sth->execute();
        }
    }
    
}

?>
