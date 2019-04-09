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
     */
    public function save()
    {
        // Form validation before database insertion
        $this->validate();

        if (empty($this->errors)) {
            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

            $fields = [
                'unique_id'    => Helper::generateUniqueId(),
                'first_name'   => $this->firstName,
                'middle_name'  => $this->middleName,
                'last_name'    => $this->lastName,
                'email'        => $this->email,
                'company_name' => $this->companyName,
                'phone_number' => $this->phoneNumber,
                'password'     => $passwordHash,
                'created_at'   => date('Y-m-d H:i:s'),
            ];

            return parent::insert('donors', $fields);
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
        } else if (self::emailExists($this->email)) {
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

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return bool True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email)
    {
        return self::findByEmail($email) !== false;
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
        $sql = 'SELECT * FROM `donors` WHERE email = :email';

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
        $sql = 'SELECT * FROM `donors` WHERE id = :id';

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

        if ($user) {
            if (password_verify($password, $user->password)) {
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
            'token_hash' => $hashToken,
            'donor_id'   => $this->id,
            'expires_at' => date('Y-m-d H:i:s', $this->expiryTimestamp),
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

        $sql = 'UPDATE `donors`
                SET `password_reset_hash` = :token_hash,
                    `password_reset_expiry` = :expires_at
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
}
