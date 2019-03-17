<?php

namespace App\Models;

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
     * @return void
     */
    public function save()
    {
        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        $query = 'INSERT INTO `donors` (`unique_id`, `first_name`, `middle_name`, `last_name`, `email`, `company_name`, `phone_number`, `password`, `created_at`) VALUES (:unique_id, :first_name, :middle_name, :last_name, :email, :company_name, :phone_number, :password, :created_at)';

//        $connection = static::getDB();
        $connection = parent::getDB();
        $stmt = $connection->prepare($query);

        $stmt->bindValue(':unique_id', uniqid(), PDO::PARAM_STR);
        $stmt->bindValue(':first_name', $this->firstName, PDO::PARAM_STR);
        $stmt->bindValue(':middle_name', $this->middleName, PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $this->lastName, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':company_name', $this->companyName, PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $this->phoneNumber, PDO::PARAM_STR);
        $stmt->bindValue(':password', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);

        $stmt->execute();
    }
}
