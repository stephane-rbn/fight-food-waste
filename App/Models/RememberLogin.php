<?php

namespace App\Models;

use App\Token;
use Core\Model;
use Exception;
use PDO;

/**
 * Remembered login model
 */
class RememberLogin extends Model
{
    /**
     * User ID
     * @var int
     */
    public $userId;

    /**
     * Token hash
     * @var string
     */
    public $tokenHash;

    /**
     * Token expiry
     * @var string
     */
    public $expiredAt;

    /**
     * Find a remembered login model by the token
     *
     * @param string $token The remembered login token
     *
     * @return mixed Remembered login object if found, false otherwise
     * @throws Exception
     */
    public static function findByToken($token)
    {
        $token = new Token($token);
        $tokenHash = $token->getHash();

        $sql = 'SELECT * FROM `remembered_logins` WHERE `tokenHash` = :token_hash';

        $connection = parent::getDB();
        $statement = $connection->prepare($sql);
        $statement->bindValue(':token_hash', $tokenHash, PDO::PARAM_STR);

        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Get the user model associated with this remembered login
     *
     * @return User The user model
     */
    public function getUser()
    {
        return User::findByID($this->userId);
    }

    /**
     * See if remember token has expired or not, based on the current system time
     *
     * @return bool True if the token has expired, false otherwise
     */
    public function hasExpired()
    {
        return strtotime($this->expiredAt) < time();
    }

    /**
     * Delete this model
     *
     * @return void
     */
    public function delete()
    {
        $sql = 'DELETE FROM `remembered_logins` WHERE `tokenHash` = :token_hash';

        $connection = parent::getDB();

        $statement = $connection->prepare($sql);
        $statement->bindValue(':token_hash', $this->tokenHash, PDO::PARAM_STR);

        $statement->execute();
    }
}
