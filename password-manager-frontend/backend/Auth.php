<?php
class Auth 
{
    public static function generateToken($length = 64) // The former size of this is 32. If there's problems, consult your doctor.
    {
        return bin2hex(random_bytes($length));
    }

    public static function checkAuth() 
    {
        session_start();
        if (!isset($_SESSION['user_id'])) 
        {
            return false;
        }
        return true;
    }

    public static function login($user_id, $is_admin) 
    {
        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['is_admin'] = $is_admin;
    }

    public static function logout() 
    {
        session_start();
        session_destroy();
    }
}
?>
