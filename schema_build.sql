DROP TABLE IF EXISTS `remembered_logins`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`                  INTEGER PRIMARY KEY AUTO_INCREMENT,
  `uniqueId`            VARCHAR(32) NOT NULL,
  `firstName`           VARCHAR(60) NOT NULL,
  `middleName`          VARCHAR(60),
  `lastName`            VARCHAR(60) NOT NULL,
  `email`               VARCHAR(255) NOT NULL UNIQUE,
  `companyName`         VARCHAR(100),
  `phoneNumber`         VARCHAR(60) NOT NULL,
  `passwordHash`        VARCHAR(255) NOT NULL,
  `passwordResetHash`   VARCHAR(64) DEFAULT NULL UNIQUE,
  `passwordResetExpiry` DATETIME DEFAULT NULL,
  `activationHash`      VARCHAR(64) DEFAULT NULL UNIQUE,
  `isActive`            BOOLEAN NOT NULL DEFAULT 0,
  `createdAt`           DATETIME,
  `updatedAt`           DATETIME
#   `pickupPlaceId` INTEGER REFERENCES `pickup_places` (`id`),
#   `userTypeId` INTEGER REFERENCES `user_types` (`id`),
);

CREATE TABLE `remembered_logins` (
  `tokenHash` VARCHAR(64) PRIMARY KEY,
  `userId`   INTEGER NOT NULL REFERENCES `users` (`id`) ON DELETE CASCADE,
  `expiresAt` DATETIME
);
