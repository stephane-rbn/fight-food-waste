DROP TABLE IF EXISTS `remembered_logins`;
DROP TABLE IF EXISTS `donors`;

CREATE TABLE `donors` (
  `id`                    INTEGER PRIMARY KEY AUTO_INCREMENT,
  `uniqueId`             VARCHAR(32) NOT NULL,
  `firstName`            VARCHAR(60) NOT NULL,
  `middleName`           VARCHAR(60),
  `lastName`             VARCHAR(60) NOT NULL,
  `email`                 VARCHAR(255) NOT NULL UNIQUE,
  `companyName`          VARCHAR(100),
  `phoneNumber`          VARCHAR(60) NOT NULL,
  `password`              VARCHAR(255) NOT NULL,
  `passwordResetHash`   VARCHAR(64) DEFAULT NULL UNIQUE,
  `passwordResetExpiry` DATETIME DEFAULT NULL,
#   `token`        VARCHAR(64),
#   `confirmation` VARCHAR(64),
#   `pickupPlaceId` INTEGER REFERENCES `pickup_places` (`id`),
#   `donorTypeId` INTEGER REFERENCES `donor_types` (`id`),
  `createdAt`   DATETIME,
  `updatedAt`   DATETIME
);

CREATE TABLE `remembered_logins` (
  `tokenHash` VARCHAR(64) PRIMARY KEY,
  `donorId`   INTEGER NOT NULL REFERENCES `donors` (`id`) ON DELETE CASCADE,
  `expiresAt` DATETIME
);
