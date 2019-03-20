<?php

namespace App\Models;

use App\Helper\Helper;
use Core\Model;
use PDO;

/**
 * User model
 *
 * PHP version 7.2
 */
class User extends Model
{
    /**
     * Error messages from the validations
     *
     * @var array
     */
    private $errors = [];

    /**
     * User constructor
     *
     * @param array $data Initial property values
     *
     * @return void
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Save the user model with the current property values
     *
     * @return bool
     */
    public function save()
    {
        // Form validation before database insertion
        $this->validate();

        if (empty($this->errors)) {

            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

            $query = 'INSERT INTO `donors`
                  (`unique_id`, `first_name`, `middle_name`, `last_name`, `email`, `company_name`, `phone_number`, `password`, `created_at`)
                  VALUES (:unique_id, :first_name, :middle_name, :last_name, :email, :company_name, :phone_number, :password, :created_at)';

            // $connection = static::getDB();
            $connection = parent::getDB();
            $stmt = $connection->prepare($query);

            $stmt->bindValue(':unique_id', Helper::generateUniqueId(), PDO::PARAM_STR);
            $stmt->bindValue(':first_name', $this->firstName, PDO::PARAM_STR);
            $stmt->bindValue(':middle_name', $this->middleName, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->lastName, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':company_name', $this->companyName, PDO::PARAM_STR);
            $stmt->bindValue(':phone_number', $this->phoneNumber, PDO::PARAM_STR);
            $stmt->bindValue(':password', $passwordHash, PDO::PARAM_STR);
            $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);

            return $stmt->execute();

        }

        return false;
    }

    /**
     * Validate current property values, adding validation error messages to the errors array property
     */
    public function validate()
    {
        $this->firstName = trim($this->firstName);
        $this->middleName = trim($this->middleName);
        $this->lastName = trim($this->lastName);
        $this->email = strtolower(trim($this->email));

        // First name validation
        if (strlen($this->firstName) < 2 || strlen($this->firstName) > 60) {
            $this->errors[] = 'First name should be between 2 and 60 characters in length.';
        }

        // Middle name validation (optional)
        if ($this->middleName !== '') {
            if (strlen($this->middleName) < 2 || strlen($this->middleName) > 60) {
                $this->errors[] = 'Middle name should be between 2 and 60 characters in length.';
            }
        }

        // Last name validation
        if (strlen($this->lastName) < 2 || strlen($this->lastName) > 60) {
            $this->errors[] = 'Last name should be between 2 and 60 characters in length.';
        }

        // Email validation: format
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Email format should be valid.';
        } else {
            // Email validation: uniqueness

            // Database connection
            $connection = parent::getDB();

            // Query that returns 1 every time it founds this email
            $query = $connection->prepare("SELECT 1 FROM donors WHERE email= :email");

            // Execute
            $query->execute(['email' => $this->email]);

            // Fetch data with the query
            $result = $query->fetch();

            if (!empty($result)) {
                $this->errors[] = 'Email already used.';
            }
        }

        // Phone number validation
        if ($this->phoneNumber === '') {
            $this->errors[] = 'Phone number can not be empty.';
        }

        // Company name validation
        if ($this->companyName !== '') {
            if (strlen($this->companyName) < 2) {
                $this->errors[] = 'Company name should be valid.';
            }
        }

        // Password validations: between 8 and 50 characters in length, one letter and one number
        if (strlen($this->password) < 8 || strlen($this->password) > 50) {
            $this->errors[] = 'Password should be between 8 and 50 characters in length.';
        } else if (preg_match('/[a-z]/i', $this->password === 0)) {
            $this->errors[] = 'Password needs at least one letter.';
        } else if (preg_match('/\d/', $this->password === 0)) {
            $this->errors[] = 'Password needs at least one number.';
        }

        // Password validations: identical
        if ($this->password !== $this->passwordConfirmation) {
            $this->errors[] = 'The password and the confirmation one need to be the same.';
        }
    }

    /**
     * Get errors from the validations
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
