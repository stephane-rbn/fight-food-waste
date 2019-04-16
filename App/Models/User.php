<?php

namespace App\Models;

use App\Helper\Helper;
use App\Mail;
use App\Token;
use Core\Model;
use Core\View;
use Exception;
use PDO;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * User model
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
     * User id
     *
     * @var int
     */
    public $id;

    /**
     * User unique ID
     *
     * @var string
     */
    public $uniqueId;

    /**
     * User first name
     *
     * @var string
     */
    public $firstName;

    /**
     * User middle name
     *
     * @var string
     */
    public $middleName;

    /**
     * User last name
     *
     * @var string
     */
    public $lastName;

    /**
     * User email
     *
     * @var string
     */
    public $email;

    /**
     * User company
     *
     * @var string
     */
    public $companyName;

    /**
     * User phone number
     *
     * @var string
     */
    public $phoneNumber;

    /**
     * User password
     *
     * @var string
     */
    private $password;

    /**
     * User password confirmation
     *
     * @var string
     */
    private $passwordConfirmation;

    /**
     * User authentication token value
     *
     * @var string
     */
    public $rememberToken;

    /**
     * User authentication token expiry
     *
     * @var string
     */
    public $expiryTimestamp;

    /**
     * User password reset token
     *
     * @var string
     */
    public $passwordResetToken;

    /**
     * User account activation token
     *
     * @var string
     */
    private $activationToken;

    /**
     * User constructor
     *
     * @param array $data Initial property values
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Save the user model with the current property values
     *
     * @return bool
     * @throws Exception
     */
    public function save()
    {
        // Form validation before database insertion
        $this->validate();

        if (empty($this->errors)) {
            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

            $activationToken = new Token();
            $hashedToken = $activationToken->getHash();
            $this->activationToken = $activationToken->getValue();

            $fields = [
                'uniqueId'       => Helper::generateUniqueId(),
                'firstName'      => $this->firstName,
                'middleName'     => $this->middleName,
                'lastName'       => $this->lastName,
                'email'          => $this->email,
                'companyName'    => $this->companyName,
                'phoneNumber'    => $this->phoneNumber,
                'passwordHash'   => $passwordHash,
                'activationHash' => $hashedToken,
                'createdAt'      => date('Y-m-d H:i:s'),
            ];

            return parent::insert('users', $fields);
        }

        return false;
    }

    /**
     * Validate current property values, adding validation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        $this->firstName = trim($this->firstName);
        $this->middleName = trim($this->middleName);
        $this->lastName = trim($this->lastName);
        $this->email = strtolower(trim($this->email));

        // First name validation
        if (strlen($this->firstName) < 2 || strlen($this->firstName) > 60) {
            $this->errors[] = 'First name should be between 1 and 60 characters in length.';
        }

        // Middle name validation (optional)
        if ($this->middleName !== '') {
            if (strlen($this->middleName) < 2 || strlen($this->middleName) > 60) {
                $this->errors[] = 'Middle name should be between 1 and 60 characters in length.';
            }
        }

        // Last name validation
        if (strlen($this->lastName) < 2 || strlen($this->lastName) > 60) {
            $this->errors[] = 'Last name should be between 1 and 60 characters in length.';
        }

        // Email validation: format and uniqueness
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Email format should be valid.';
        } else if (self::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'Email already used.';
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
        if (isset($this->password)) {
            if (strlen($this->password) < 8 || strlen($this->password) > 50) {
                $this->errors[] = 'Password should be between 8 and 50 characters in length.';
            } else if (preg_match('/[a-z]/i', $this->password === 0)) {
                $this->errors[] = 'Password needs at least one letter.';
            } else if (preg_match('/\d/', $this->password === 0)) {
                $this->errors[] = 'Password needs at least one number.';
            }
        }

        // Password validations: identical
        if (isset($this->password)) {
            if ($this->password !== $this->passwordConfirmation) {
                $this->errors[] = 'The password and the confirmation one need to be the same.';
            }
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

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     * @param string $ignoreID Return false anyway if the record found has this ID
     *
     * @return bool True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignoreID = null)
    {
        $user = self::findByID($email);

        if ($user) {
            if ($user->id != $ignoreID) {
                return true;
            }
        }

        return false;
    }

    /**
     * See if a user already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM `users` WHERE email = :email';

        // Database connection
        $connection = parent::getDB();

        $statement = $connection->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);

        // Return a class instead of an array for this statement
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM `users` WHERE id = :id';

        // Database connection
        $connection = parent::getDB();

        $statement = $connection->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        // Return a class instead of an array for this statement
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Authenticate a user by email and password
     *
     * @param string $email
     * @param string $password
     *
     * @return mixed The user object or false if authentication failes
     */
    public static function authenticate($email, $password)
    {
        $user = self::findByEmail(trim(strtolower($email)));

        if ($user && $user->isActive) {
            if (password_verify($password, $user->passwordHash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Remember the login by inserting a new unique token into
     * the remembered logins table for the user record
     *
     * @return bool True if the login was remembered successfully, false otherwise
     * @throws Exception
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashToken = $token->getHash();
        $this->rememberToken = $token->getValue();

        // 30 days from now
        $this->expiryTimestamp = time() + 60 * 60 * 24 * 30;

        $fields = [
            'tokenHash' => $hashToken,
            'userId'    => $this->id,
            'expiresAt' => date('Y-m-d H:i:s', $this->expiryTimestamp),
        ];

        return parent::insert('remembered_logins', $fields);
    }

    /**
     * Send password reset instructions to the user specified
     *
     * @param string $email The email address
     *
     * @return void
     */
    public static function sendPasswordReset($email)
    {
        $user = self::findByEmail($email);

        if ($user) {
            if ($user->startPasswordReset()) {
                $user->sendPasswordResetEmail();
            }
        }
    }

    /**
     * Start the password reset process by generating a new token and expiry
     *
     * @return void
     * @throws Exception
     */
    protected function startPasswordReset()
    {
        $token = new Token();
        $hashedToken = $token->getHash();
        $this->passwordResetToken = $token->getValue();

        // 2 hours from now
        $expiryTimestamp = time() + 60 * 60 * 2;

        $sql = 'UPDATE `users`
                SET `passwordResetHash` = :token_hash,
                    `passwordResetExpiry` = :expires_at
                WHERE `id` = :id';

        $connection = self::getDB();
        $statement = $connection->prepare($sql);

        $statement->bindValue(':token_hash', $hashedToken, PDO::PARAM_STR);
        $statement->bindValue(':expires_at', date('Y-m-d H:i:s', $expiryTimestamp), PDO::PARAM_STR);
        $statement->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * Send password reset instructions in an email to the user
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->passwordResetToken;

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html.twig', ['url' => $url]);

        Mail::send($this->email, 'Password reset', $text, $html);
    }

    /**
     * Find a user model by password reset token and expiry
     *
     * @param string $token Password reset token sent to user
     *
     * @return mixed User object if found and the token hasn't expired, null otherwise
     * @throws Exception
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashedToken = $token->getHash();

        $sql = 'SELECT * FROM `users` WHERE `passwordResetHash` = :token_hash';

        $connection = self::getDB();
        $statement = $connection->prepare($sql);

        $statement->bindValue(':token_hash', $hashedToken, PDO::PARAM_STR);

        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $statement->execute();

        $user = $statement->fetch();

        if ($user) {
            if (strtotime($user->passwordResetExpiry) > time()) {
                return $user;
            }
        }
    }

    /**
     * Reset the password
     *
     * @param string $password The new password
     *
     * @return bool True if the password was updated successfully, false otherwise
     */
    public function resetUserPassword($password)
    {
        $this->password = $password;
//        $this->passwordConfirmation = $password;

        $this->validate();

        if (empty($this->errors)) {
            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE `users`
                    SET `passwordHash` = :password_hash,
                        `passwordResetHash` = NULL,
                        `passwordResetExpiry` = NULL
                    WHERE `id` = :id';

            $connection = self::getDB();
            $statement = $connection->prepare($sql);

            $statement->bindValue(':id', $this->id, PDO::PARAM_INT);
            $statement->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);

            return $statement->execute();
        }

        return false;
    }

    /**
     * Send an email to the user containing the activation link
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendActivationEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/register/activate/' . $this->activationToken;

        $text = View::getTemplate('Register/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Register/activation_email.html.twig', ['url' => $url]);

        Mail::send($this->email, 'Account activation', $text, $html);
    }

    /**
     * Activate the user account with the specified activation token
     *
     * @param string $value Activation token from the URL
     *
     * @return void
     * @throws Exception
     */
    public static function activateUser($value)
    {
        $activationToken = new Token($value);
        $hashedToken = $activationToken->getHash();

        $sql = 'UPDATE `users`
                SET `isActive` = 1, `activationHash` = NULL
                WHERE `activationHash` = :hashed_token';

        $connection = self::getDB();
        $statement = $connection->prepare($sql);

        $statement->bindValue(':hashed_token', $hashedToken, PDO::PARAM_STR);

        $statement->execute();
    }

    /**
     * Update the user's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return bool True if the data was updated, false otherwise
     */
    public function updateProfile($data)
    {
        $this->firstName   = $data['firstName'];
        $this->middleName  = $data['middleName'];
        $this->lastName    = $data['lastName'];
        $this->email       = $data['email'];
        $this->phoneNumber = $data['phoneNumber'];
        $this->companyName = $data['companyName'];

        if (!empty($data['password'])) {
            $this->password = $data['password'];
        }

        $this->validate();

        if (empty($this->errors)) {
            $sql = 'UPDATE `users`
                    SET firstName   = :first_name,
                        middleName  = :middle_name,
                        lastName    = :last_name,
                        email       = :email,
                        phoneNumber = :phone_number,
                        companyName = :company_name,
                        updatedAt   = :updated_at';

            if (isset($this->password)) {
                $sql .= ', passwordHash = :password_hash';
            }

            $sql .= "\nWHERE id = :id";

            $connection = self::getDB();
            $statement = $connection->prepare($sql);

            $statement->bindValue(':first_name', $this->firstName, PDO::PARAM_STR);
            $statement->bindValue(':middle_name', $this->middleName, PDO::PARAM_STR);
            $statement->bindValue(':last_name', $this->lastName, PDO::PARAM_STR);
            $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
            $statement->bindValue(':phone_number', $this->phoneNumber, PDO::PARAM_STR);
            $statement->bindValue(':company_name', $this->companyName, PDO::PARAM_STR);
            $statement->bindValue(':updated_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $statement->bindValue(':id', $this->id, PDO::PARAM_INT);

            if (isset($this->password)) {
                $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
                $statement->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
            }

            return $statement->execute();
        }

        return false;
    }
}
