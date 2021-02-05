<?php

/**
 * Player class
 * @author Veljko Ilic
 * @since december 2014
 */
class Player {
    
    /**
     * Number of last played games that serves for calculating rating of a player
     */
    const PLAYED_NUMBER = 20;
    
    /**
     * Lifetime of a session - one month
     */
    const SESSION_LIFETIME = 2592000;
    
    /**
     * The string that serves for encrypting passwords
     */
    const SALT = '3#&ihG98l33_';
    
    /**
     * Recaptcha parameters
     */
    const RECAPTCHA_URL = 'https://www.google.com/recaptcha/api/siteverify';
    const RECAPTCHA_SITE_KEY = '6LcOkwUTAAAAAMyKBF8oFQ1QtTNUgd_D41hUo8b9';
    const RECAPTCHA_SECRET = '6LcOkwUTAAAAANzpb13gxJxcu_NVCJuz_4kkR1N8';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->autoLogin();
    }
    
    /**
     * Parameter setter, parameters are kept in a session
     * @param string $name Parameter name
     * @param mixed $value Parameter value
     */
    public function __set($name, $value) {
        $_SESSION['player'][$name] = $value;
    }
    
    /**
     * Parameter getter
     * @param string $name Name of a parameter
     * @return mixed Value of a parameter
     */
    public function __get($name) {
        return $_SESSION['player'][$name];
    }
    
    /**
     * Get player's standing
     * @return int
     */
    public function getStanding() {
        return Util::getStanding($this->rating);
    }
    
    /**
     * Check if a player is logged-in
     * @return bool
     */
    public function loggedIn() {
        return isset($_SESSION['player']);
    }
    
    /**
     * Set parameters
     * @param array $data Player's data to set [id, username, rating]
     */
    private function setData($data) {
        $_SESSION['player'] = array();
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->rating = $data['rating'];
    }
    
    /**
     * Prolong duration of a session up to one month
     */
    public function prolongSession() {
        setcookie(session_name(), session_id(), time() + self::SESSION_LIFETIME, '/');
    }
    
    private function saveSessionId() {
        $db = Db::getInstance();
        $sql = 'UPDATE
                    players
                SET
                    session_id = :session_id
                WHERE
                    id = :id';
        $statement = $db->prepare($sql);
        $statement->execute(array('session_id' => session_id(), 'id' => $this->id));
    }
    
    private function autoLogin() {
        
        if (!$this->loggedIn() && isset($_COOKIE[session_name()])) {
            
            $db = Db::getInstance();
            $sql = 'SELECT
                        *
                    FROM
                        players
                    WHERE
                        session_id = :session_id';
            $statement = $db->prepare($sql);
            $statement->execute(array('session_id' => $_COOKIE[session_name()]));
            $data = $statement->fetch(PDO::FETCH_ASSOC);
            
            if (!empty($data) && $data['active']) {
                $this->setData($data);
                $this->saveSessionId();
            }
        }
    }
    
    public function init() {
        if ($this->loggedIn()) {
            $this->prolongSession();
            $this->checkStarted();
        }
    }
    
    /**
     * Register a new player
     * @param string $username Username
     * @param string $password Password
     * @param string $repeatPassword Password repeated
     * @param string $email Email
     * @return array [saved, message, username]
     */
    public function register($username, $password, $repeatPassword, $email, $recaptcha) {
        
        $message = '';
        if (empty($username) || empty($password)) {
            $message = 'Username and password are required!';
            return $message;
        }
        if (strlen($username) > 255) {
            return 'Username can have max 255 characters!';
        }
        
        // Check if there is a player with the same username
        if (!$this->usernameFree($username)) {
            $message = 'Username you entered already exists. Please change username and try again.';
            return $message;
        }
        
        if ($password != $repeatPassword) {
            $message = 'Passwords you entered do not match. Please change passwords and try again.';
            return $message;
        }
        // If email is provided, check its validity
        if (!empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
                $message = 'Email you entered is not a valid email address. Please change email and try again.';
                return $message;
            }
        }
        
        $recaptchaResponse = file_get_contents(self::RECAPTCHA_URL . '?secret=' . self::RECAPTCHA_SECRET . '&response=' . $recaptcha . '&remoteip=' .$_SERVER['REMOTE_ADDR']);
        $recaptchaResponse = json_decode($recaptchaResponse, TRUE);
        if (empty($recaptchaResponse) || !isset($recaptchaResponse['success']) || !$recaptchaResponse['success']) {
            $message = 'Incorrect captcha! Try again.';
            return $message;
        }
        
        $password = md5(self::SALT . $password);
        $db = Db::getInstance();
        $sql = 'INSERT INTO
                    players
                    (username, password, email)
                VALUES
                    (:username, :password, :email)';
        $statement = $db->prepare($sql);
        $saved = $statement->execute(array('username' => $username, 'password' => $password, 'email' => $email));
        
        if ($saved) {
            $this->setData(
                array(
                    'id' => $db->lastInsertId(),
                    'username' => $username,
                    'email' => $email,
                    'rating' => 0
                )
            );
        }
        
        return $message;
    }
    
    /**
     * Login a player
     * @param string $username Username
     * @param string $password Password
     * @return array [logged_in, message, username, rating, standing]
     */
    public function login($username, $password) {
        $message = '';
        
        $password = md5(self::SALT . $password);
        
        $db = Db::getInstance();
        $sql = 'SELECT
                    *
                FROM
                    players
                WHERE
                    username = :username AND
                    password = :password';
        $statement = $db->prepare($sql);
        $statement->execute(array('username' => $username, 'password' => $password));
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            if ($data['active']) {
                $this->setData($data);
                $this->saveSessionId();
                $this->prolongSession();
            } else {
                $message = 'Your account is not active. An email has been sent to you with a link for activation of new password. You need just to click on the link and new password will be active.';
            }
        } else {
            $message = 'Username and password do not match! Change them and try again.';
        }
        return $message;
    }
    
    /**
     * Logout a player
     */
    public function logout() {
        setcookie(session_name(), session_id(), time() - 86500, '/');
        $this->unsetSession();
        $game = new Game();
        $game->unsetSession();
        session_destroy();
    }
    
    /**
     * Unset a player's session
     */
    private function unsetSession() {
        if (isset($_SESSION['player'])) {
            unset($_SESSION['player']);
        }
    }
    
    /**
     * Process a 'password forgotten' form
     * @param string $username Username
     * @param string $password Password
     * @param string $repeatPassword Password repeated
     * @param string $email Email
     * @return array [saved, message]
     */
    public function passwordForgotten($username, $password, $repeatPassword, $email) {
        $db = Db::getInstance();
        // Check if there is a player with entered username and email
        $sql = 'SELECT
                    *
                FROM
                    players
                WHERE
                    username = :username AND
                    email = :email';
        $statement = $db->prepare($sql);
        $statement->execute(array('username' => $username, 'email' => $email));
        $player = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($player)) {
            return 'Username and email you entered do not match. Please check the values you entered and try again.';
        }
        // Check if new passwords match
        if ($password != $repeatPassword) {
            return 'Passwords you entered do not match. Please change passwords and try again.';
        }
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            return 'Email you entered is not a valid email address. Please change email and try again.';
        }
        // Save new password, create and save a hash for account activation and set account inactive
        $password = md5(self::SALT . $password);
        $hash = md5(self::SALT . $email . $password);
        
        $sql = 'UPDATE
                    players
                SET
                    password = :password,
                    active = 0,
                    `hash` = :hash
                WHERE
                    id = :id';
        $statement = $db->prepare($sql);
        
        $saved = $statement->execute(array(
            'password' => $password,
            'id' => $player['id'],
            'hash' => $hash
        ));
        
        if ($saved) {
            return $this->sendMail($email, $hash);
        } else {
            return 'An error occurred while resetting password! Please try again.';
        }
    }
    
    /**
     * Activate a player (account)
     * @param string $hash Activation hash
     * @return string Message to player
     */
    public function activate($hash) {
        $message = '';
        
        $db = Db::getInstance();
        // Check if provided hash exists
        $sql = 'SELECT
                    *
                FROM
                    players
                WHERE
                    `hash` = :hash';
        $statement = $db->prepare($sql);
        $statement->execute(array('hash' => $hash));
        $player = $statement->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($player)) {
            // Activate account and login player
            $sql = 'UPDATE
                        players
                    SET
                        active = 1,
                        `hash` = NULL
                    WHERE id = :id';
            $statement = $db->prepare($sql);
            if ($statement->execute(array('id' => $player['id']))) {
                $this->setData($player);
                $this->saveSessionId();
                $this->prolongSession();
                $message = 'New password activated! You are logged in.';
            } else {
                $message = 'An error occurred while activating new password! Please try again.';
            }
        } else {
            $message = 'New password not activated!';
        }
        
        return $message;
    }
    
    /**
     * Fetch a list of played games (last self::PLAYED_NUMBER)
     * @return array List of played games
     */
    public function getPlayedList() {
        $db = Db::getInstance();
        if ($this->loggedIn()) {
            $sql = "SELECT
                        p.id,
                        DATE_FORMAT(p.start_time, '%Y-%m-%d %H:%i') AS start_time,
                        p.points,
                        g.`level` * 3 AS max_points,
                        g.`level`
                    FROM
                        played p
                    INNER JOIN
                        games g
                        ON p.game_id = g.id
                    WHERE
                        p.player_id = :player_id
                    ORDER BY
                        p.start_time DESC
                    LIMIT " . self::PLAYED_NUMBER;
            $statement = $db->prepare($sql);
            $statement->execute(array('player_id' => $this->id));
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            $table = '<table class="u-full-width"><thead><tr><th>time</th><th class="td-right">level</th><th class="td-right">score</th><th class="td-right"> </th></tr></thead><tbody>';
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $table .= '<tr><td>' . $row['start_time'] . '</td><td class="td-right">' . $row['level'] . '</td><td class="td-right">' . $row['points']
                            . ' / ' . $row['max_points'] . '</td><td class="td-right"><a href="#" onclick="javascript: Demo.startPlayed(' . $row['id'] . ');return false;">replay</a></td></tr>';                    
                }
            } else {
                $table .= '<tr><td colspan="5">empty history</td></tr>';
            }
            $table .= '</tbody></table>';
            return $table;
        } else {
            return '';
        }
    }
    
    /**
     * Get a played game
     * @param int $id
     * @return array A played game properties, used to recreate the game
     */
    public function getPlayed($id = NULL) {
        $db = Db::getInstance();
        if (!empty($id) && $this->loggedIn()) {
            $sql = "SELECT
                        p.moves AS a_moves,
                        p.raw_moves AS a_raw_moves,
                        DATE_FORMAT(p.start_time, '%Y-%m-%d %H:%i') AS start_time,
                        p.points,
                        g.obstacles,
                        g.moves AS b_moves,
                        g.raw_moves AS b_raw_moves,
                        g.level,
                        g.level * 3 AS max_points
                    FROM
                        played p
                    INNER JOIN
                        games g
                        ON p.game_id = g.id
                    WHERE
                        p.id = :id AND
                        p.player_id = :player_id";
            $statement = $db->prepare($sql);
            $statement->execute(array('id' => $id, 'player_id' => $this->id));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $result['obstacles'] = json_decode($result['obstacles']);
            $result['a_moves'] = json_decode($result['a_moves']);
            $result['a_raw_moves'] = json_decode($result['a_raw_moves']);
            $result['b_moves'] = json_decode($result['b_moves']);
            $result['b_raw_moves'] = json_decode($result['b_raw_moves']);
            return $result;
        }
        return array();
    }
    
    /**
     * Update player's rating (last [self::PLAYED_NUMBER] played games count for the score)
     */
    public function updateRating() {
        $db = Db::getInstance();
        
        $sql = 'SELECT
                    ROUND(SUM(points) / ' . self::PLAYED_NUMBER . ', 2)
                FROM
                    (
                        SELECT
                            points
                        FROM
                            played
                        WHERE
                            player_id = :id
                        ORDER BY
                            start_time DESC
                        LIMIT
                            ' . self::PLAYED_NUMBER . '
                    ) a';
        $statement = $db->prepare($sql);
        $statement->execute(array('id' => $this->id));
        $this->rating = $statement->fetchColumn();
        
        $sql = 'UPDATE
                    players
                SET
                    rating = :rating
                WHERE
                    id = :id';
        $statement = $db->prepare($sql);
        $statement->execute(array('id' => $this->id, 'rating' => $this->rating));
    }
    
    public function getLevels() {
        $db = Db::getInstance();
        
        if ($this->loggedIn()) {
            $filter = ' WHERE id NOT IN (SELECT game_id FROM played WHERE player_id = :player_id) ';
            $params = array('player_id' => $this->id);
        } else {
            $filter = '';
            $params = array();
        }
        $sql = "SELECT DISTINCT level FROM games $filter ORDER BY level";
        $sth = $db->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Test sending of email
     * @param string $toEmail
     * @param string $messageHTML
     * @param string $messageTEXT
     * @return boolean
     */
    public function sendMail($email, $hash) {
        require_once 'phpmailer/class.phpmailer.php';
        require_once 'phpmailer/class.pop3.php';
        require_once 'phpmailer/class.smtp.php';
        
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->Username = 'triturn.org@gmail.com';
        $mail->Password = 'veljko';
        $mail->Priority = 1;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = '8bit';
        $mail->Subject = 'Activate new password';
        $mail->ContentType = 'text/html; charset=utf-8\r\n';
        $mail->From = 'triturn.org@gmail.com';
        $mail->FromName = 'Triturn';
        $mail->WordWrap = 900;

        $mail->AddAddress($email);
        $mail->isHTML(TRUE);
        
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '?action=activate&hash=' . $hash;
        $body = 'To activate new password click on this link: <a href="' . $url . '" target="_blank">ACTIVATE</a>';
        $altBody = 'To activate new password open this url in a browser: ' . $url;
        $mail->Body = $body;
        $mail->AltBody = $altBody;
        
        if(!$mail->Send()) {
            return 'An error occurred while sending email to you! Please try again later.';
        } else {
            return 'An email is sent to you with a link for activation of new password. You need just to click on the link and new password will be active.';
        }
    }
    
    public function sendMailNew($to, $hash) {
        $subject = 'Activate new password';
        $message = "To activate new password open this url in a browser:/r/n" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "?action=activate&hash=" . $hash;
        if(mail($to, $subject, $message)) {
            return 'An email is sent to you with a link for activation of new password. You need just to click on the link and new password will be active.';
        } else {
            return 'An error occurred while sending email to you! Please try again later.';
        }
    }
    
    /**
     * Update profile
     * @param string $username Username
     * @param string $password Password
     * @param string $repeatPassword Password repeated
     * @param string $email Email
     * @return string Message
     */
    public function profile($username, $password, $repeatPassword, $email) {
        $db = Db::getInstance();
        
        if ($this->loggedIn()) {
            
            $edit = FALSE;
            
            if ($username != $this->username) {
                if (empty($username)) {
                    return 'Username is required!';
                } else if (strlen($username) > 255) {
                    return 'Username can have max 255 characters!';
                }
                // Check if there is a player with the same username
                $sql = 'SELECT
                            COUNT(id)
                        FROM
                            players
                        WHERE
                            username = :username';
                $statement = $db->prepare($sql);
                $statement->execute(array('username' => $username));
                if ($statement->fetchColumn() > 0) {
                    return 'Username you entered already exists. Please change username and try again.';
                }
                $edit = TRUE;
            }
            
            // If email is provided, check its validity
            if ($email != $this->email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
                    return 'Email you entered is not a valid email address. Please change email and try again.';
                }
                $edit = TRUE;
            }
            
            $extra = '';
            $params = array(
                'username' => $username,
                'email' => $email,
                'id' => $this->id
            );
            
            if (!empty($password)) {
                if ($password != $repeatPassword) {
                    return 'Passwords you entered do not match. Please change passwords and try again.';
                }
                $extra = ', password = :password ';
                $params['password'] = md5(self::SALT . $password);
                $edit = TRUE;
            }
            
            if ($edit) {
                $sql = "UPDATE
                            players
                        SET
                            username = :username, email = :email $extra
                        WHERE
                            id = :id";
                $statement = $db->prepare($sql);
                $saved = $statement->execute($params);
                
                if ($saved) {
                    $fbPlayer = $this->fbPlayer();
                    $this->setData(
                        array(
                            'id' => $this->id,
                            'username' => $username,
                            'email' => $email,
                            'rating' => $this->rating
                        )
                    );
                    $this->fb = $fbPlayer;
                    return 'Profile data changed.';
                } else {
                    return 'An error occurred while editing profile! Please try again.';
                }
            }
            
        } else {
            return 'You are not logged in!';
        }
    }
    
    private function usernameFree($username) {
        $db = Db::getInstance();
        $sql = 'SELECT
                    *
                FROM
                    players
                WHERE
                    username = :username';
        $sth = $db->prepare($sql);
        $sth->execute(array('username' => $username));
        return empty($sth->fetch());
    }
    
    private function checkStarted() {
        $db = Db::getInstance();
        $sql = 'SELECT
                    s.*
                FROM
                    started s
                LEFT JOIN
                    played p
                    ON s.player_id = p.player_id AND s.game_id = p.game_id
                WHERE
                    s.player_id = :player_id
                    AND p.id IS NULL';
        $sth = $db->prepare($sql);
        $sth->execute(array('player_id' => $this->id));
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        
        if (Game::allowSave($this->id, 0)) {
            foreach ($rows as $row) {
                $sql = "INSERT INTO
                            played
                            (game_id, player_id, start_time, moves, raw_moves, moves_counter, points)
                        VALUES
                            (:game_id, :player_id, :start_time, '[]', '[]', 0, 0)";
                $sth = $db->prepare($sql);
                $params = array(
                    'game_id' => $row['game_id'],
                    'player_id' => $row['player_id'],
                    'start_time' => $row['start_time']
                );
                $sth->execute($params);
            }
        }
        
        $sql = "DELETE FROM
                    started
                WHERE
                    player_id = :player_id";
        $sth = $db->prepare($sql);
        $sth->execute(array('player_id' => $this->id));
    }
    
    public function fbLogin($id, $firstName, $lastName) {
        
        $response = array('message' => '', 'session_id' => '');
        
        $db = Db::getInstance();
        $sql = 'SELECT
                    *
                FROM
                    players
                WHERE
                    fb_id = :fb_id';
        $sth = $db->prepare($sql);
        $sth->execute(array('fb_id' => $id));
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        
        if (empty($data)) {
            // register
            $username = $firstName;
            if (!$this->usernameFree($username)) {
                $username .= ' ' . $lastName;
                while (!$this->usernameFree($username)) {
                    $username .= '1';
                }
            }
            
            $sql = 'INSERT INTO
                        players
                        (fb_id, username)
                    VALUES
                        (:fb_id, :username)';
            $sth = $db->prepare($sql);
            $saved = $sth->execute(array('fb_id' => $id, 'username' => $username));
            
            if ($saved) {
                $this->setData(
                    array(
                        'id' => $db->lastInsertId(),
                        'username' => $username,
                        'email' => '',
                        'rating' => 0
                    )
                );
                $this->fb = TRUE;
                $this->saveSessionId();
                $response['session_id'] = session_id();
            } else {
                $response['message'] = 'An error occurred while logging! Please try again.';
            }
        } else {
            // login
            $this->setData($data);
            $this->fb = TRUE;
            $this->saveSessionId();
            $response['session_id'] = session_id();
        }
        
        return $response;
    }
    
    public function fbPlayer() {
        return isset($_SESSION['player']['fb']);
    }
    
}

?>