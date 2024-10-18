<?php 

namespace App\Controllers;

use App\Models\User;

class RegistrationController extends BaseController
{
    public function registerForm() {
        // Show the empty registration form with no errors
        return $this->render('registration-form');
    }

    public function register() {

        // Initialize errors array to store validation errors
        $errors = [];
    
        try {
            // Retrieve form data
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirmation = $_POST['confirm_password'] ?? '';
    
            // Required field check
            if (empty($username) || empty($email) || empty($password) || empty($password_confirmation)) {
                $errors[] = "All required fields must be filled out.";
            }
    
            // Password length check
            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }
    
            // Numeric character check
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = "Password must contain at least one numeric character.";
            }
    
            // Non-numeric character check
            if (!preg_match('/[a-zA-Z]/', $password)) {
                $errors[] = "Password must contain at least one non-numeric character.";
            }
    
            // Special character check
            if (!preg_match('/[\W]/', $password)) {
                $errors[] = "Password must contain at least one special character (!@#$%^&*-+).";
            }
    
            // Password confirmation check
            if ($password !== $password_confirmation) {
                $errors[] = "Passwords do not match.";
            }
    
            // Check if there are any validation errors
            if (!empty($errors)) {
                // Render the registration form with errors
                $data = [
                    'errors' => $errors, // Errors passed to Registration-Form.Mustache template
                    'username' => $username,
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name
                ];
    
                return $this->render('registration-form', $data);
            }
    
            // If all checks pass, save the data using the User model
            $user = new User();
            $save_result = $user->save($username, $email, $first_name, $last_name, $password);
    
            if ($save_result > 0) {
                return $this->render('success'); // Registration success page
            } else {
                echo "There was an error during registration. Please try again.";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
