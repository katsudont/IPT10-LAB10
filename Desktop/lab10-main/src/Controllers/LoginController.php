<?php 

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController {

    public function showLoginForm() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Reset login attempts at the start of each session, if not set
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        $data = [
            'remaining_attempts' => $this->getRemainingAttempts(),
            'form_disabled' => $_SESSION['login_attempts'] >= 3, // Disable form if too many attempts
            'show_remaining_attempts' => $_SESSION['login_attempts'] > 0 // Show attempts if there are any
        ];

        $template = 'login-form';
        return $this->render($template, $data);
    }

    public function login() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user has exceeded the max attempts
        $max_attempts = 3;
        if ($_SESSION['login_attempts'] >= $max_attempts) {
            // Destroy session if max attempts are reached
            session_destroy();

            // Start a new session for the next interaction
            session_start();

            // Redirect back to the form with an error message and form disabled
            $errors[] = "Too many failed login attempts. Please try again later.";
            return $this->render('login-form', [
                'errors' => $errors, 
                'form_disabled' => true, 
                'show_remaining_attempts' => false
            ]);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            if (empty($username) || empty($password)) {
                $errors[] = "Username and password are required.";
                return $this->showLoginFormWithErrors($errors);
            }

            // Initialize User model and fetch password hash
            $user = new User();
            $saved_password_hash = $user->getPassword($username);

            if ($saved_password_hash && password_verify($password, $saved_password_hash)) {
                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;

                // Store session data
                $_SESSION['is_logged_in'] = true;
                $_SESSION['username'] = $username;

                // Redirect to welcome page
                header("Location: /welcome");
                exit;
            } else {
                // Increment failed login attempts
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

                // Get remaining attempts
                $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];

                if ($_SESSION['login_attempts'] >= $max_attempts) {
                    // Destroy session after max attempts and start a new session
                    session_destroy();
                    session_start();
                    
                    $errors[] = "Too many failed login attempts.";
                    return $this->render('login-form', [
                        'errors' => $errors, 
                        'form_disabled' => true, 
                        'show_remaining_attempts' => false
                    ]);
                } else {
                    $errors[] = "Invalid username or password. Attempts remaining: $remaining_attempts.";
                    return $this->showLoginFormWithErrors($errors, $remaining_attempts);
                }
            }
        } else {
            return $this->showLoginForm();
        }
    }

    private function showLoginFormWithErrors($errors, $remaining_attempts = null) {
        // Pass remaining attempts if provided and disable form if attempts exceed max limit
        return $this->render('login-form', [
            'errors' => $errors,
            'remaining_attempts' => $remaining_attempts,
            'form_disabled' => $_SESSION['login_attempts'] >= 3, // Disable form if too many attempts
            'show_remaining_attempts' => $_SESSION['login_attempts'] > 0 // Show attempts only if there are any failed attempts
        ]);
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Reset login attempts on logout
        $_SESSION['login_attempts'] = 0;

        // Destroy session on logout
        session_destroy();

        header("Location: /login-form");
        exit;
    }

    private function getRemainingAttempts() {
        $max_attempts = 3;
        return $max_attempts - ($_SESSION['login_attempts'] ?? 0);
    }
}
