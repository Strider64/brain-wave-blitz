<?php

namespace brainwave;

trait CheckStatus
{
    public function check_login_token(): bool
    {
        // Check for the presence of the cookie and the session key
        if (isset($_COOKIE['login_token']) && isset($_SESSION['login_token'])) {
            // Verify the token against the stored value
            if ($_COOKIE['login_token'] === $_SESSION['login_token']) {
                return true;
            }
        }

        return false;
    }
}