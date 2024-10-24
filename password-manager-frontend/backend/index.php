<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once './config/Database.php';
include_once './classes/User.php';
include_once './classes/PasswordEntry.php';
include_once './classes/Auth.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) 
{
    case 'POST':
        if (isset($_GET['action'])) 
        {
            $action = $_GET['action'];

            switch ($action) 
            {
                case 'login':
                    $data = json_decode(file_get_contents("php://input"));
                    $user = new User($db);
                    $user->email = $data->email;
                    $user->password = $data->password;

                    if ($user->login()) 
                    {
                        Auth::login($user->id, $user->is_admin);
                        echo json_encode(["message" => "Login successful"]);
                    } 
                    else 
                    {
                        echo json_encode(["message" => "Login failed"]);
                    }
                    break;

                case 'register':
                    $data = json_decode(file_get_contents("php://input"));
                    $user = new User($db);
                    $user->username = $data->username;
                    $user->email = $data->email;
                    $user->password = $data->password;
                    $user->is_admin = $data->is_admin ?? 0;

                    if ($user->register()) {
                        echo json_encode(["message" => "Registration successful"]);
                    } else {
                        echo json_encode(["message" => "Registration failed"]);
                    }
                    break;

                case 'add_password':
                    if (Auth::checkAuth()) 
                    {
                        $data = json_decode(file_get_contents("php://input"));
                        $passwordEntry = new PasswordEntry($db);
                        $passwordEntry->user_id = $_SESSION['user_id'];
                        $passwordEntry->account_type = $data->account_type;
                        $passwordEntry->company = $data->company;
                        $passwordEntry->email = $data->email;
                        $passwordEntry->password = $data->password;
                        $passwordEntry->is_2fa_on = $data->is_2fa_on;

                        if ($passwordEntry->create()) 
                        {
                            echo json_encode(["message" => "Password added successfully"]);
                        } 
                        else 
                        {
                            echo json_encode(["message" => "Failed to add password"]);
                        }
                    } 
                    else 
                    {
                        echo json_encode(["message" => "Unauthorized"]);
                    }
                    break;
            }
        }
        break;

    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] == 'get_passwords') 
        {
            if (Auth::checkAuth()) 
            {
                $passwordEntry = new PasswordEntry($db);
                $stmt = $passwordEntry->getUserPasswords($_SESSION['user_id']);
                $passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($passwords);
            } 
            else 
            {
                echo json_encode(["message" => "Unauthorized"]);
            }
        }
        break;
}
?>


/*  This section must be integrated.
<?php
switch ($request_method) 
{
    case 'POST':
        if (isset($_GET['action'])) 
        {
            $action = $_GET['action'];

            switch ($action) 
            {
                case 'register':
                    $data = json_decode(file_get_contents("php://input"));
                    $user = new User($db);
                    $user->username = $data->username;
                    $user->email = $data->email;
                    $user->password = $data->password;
                    $user->is_admin = $data->is_admin ?? 0; // defaults

                    if ($user->register()) 
                    {
                        echo json_encode(["message" => "Registration successful"]);
                    } 
                    else 
                    {
                        echo json_encode(["message" => "Email is already registered"]);
                    }
                    break;

                case 'login':
                    $data = json_decode(file_get_contents("php://input"));
                    $user = new User($db);
                    $user->email = $data->email;
                    $user->password = $data->password;

                    if ($user->login()) 
                    {
                        Auth::login($user->id, $user->is_admin);
                        echo json_encode(["message" => "Login successful"]);
                    } 
                    else 
                    {
                        echo json_encode(["message" => "Login failed"]);
                    }
                break;
            }
        }
    break;
}
?>


*/
