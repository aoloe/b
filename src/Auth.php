<?php

/**
 * Requires bcrypt (-B) to be used when creating the htusers file
 * htpasswd -B -c db/htusers username
 */

namespace B;

class Auth {
    private $user = null;

    public function __construct($config) {
        session_start();
        if (isset($_SESSION) && array_key_exists('auth_user', $_SESSION)) {
            if (array_key_exists('logout', $_REQUEST)) {
                unset($_SESSION['auth_user']);
                return;
            }
            $this->user = $_SESSION['auth_user'];
            return;
        }
        if (!array_key_exists('username', $_REQUEST)) {
            return;
        }
        $username = $_REQUEST['username'];

        // read a file created by htpasswd ("user:hash") as a list of [user, hash]
        $auth_file = array_map(
            function($v) { return explode(":", $v, 2); },
            explode("\n", file_get_contents($config['AUTHFILE'])));

        // find the user matching username
        $user = current(array_filter($auth_file, function($v) use($username) {
            return $v[0] === $username;
        }));
        if ($user === false) {
            return;
        }

        // verify the password
        if (!password_verify($_REQUEST['password'], $user[1])) {
            return;
        }

        $this->user = $user[0];
        $_SESSION['auth_user'] = $this->user;
    }
    public function is_logged_in($username) {
        return $username === $this->user;
    }

    public function render_form() {
        return <<<EOT
        <form method="POST">
            <input name="username">
            <input type="password" name="password">
            <button type="submit">Ok</button>
        </form>
        EOT;
    }

    public function render_logout() {
        return <<<EOT
        <form method="POST">
            <button type="submit" name="logout">Logout</button>
        </form>
        EOT;
    }
}
