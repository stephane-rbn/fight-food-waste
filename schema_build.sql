CREATE TABLE `donors` (
  `id`           INTEGER PRIMARY KEY AUTO_INCREMENT,
  `unique_id`    VARCHAR(32) NOT NULL,
  `first_name`   VARCHAR(60) NOT NULL,
  `middle_name`  VARCHAR(60),
  `last_name`    VARCHAR(60) NOT NULL,
  `email`        VARCHAR(255) NOT NULL UNIQUE,
  `company_name` VARCHAR(100),
  `phone_number` VARCHAR(60) NOT NULL,
  `password`     VARCHAR(255) NOT NULL,
#   `token`        VARCHAR(64),
#   `confirmation` VARCHAR(64),
#   `pickup_place_id` INTEGER REFERENCES `pickup_places` (`id`),
#   `donor_type_id` INTEGER REFERENCES `donor_types` (`id`),
  `created_at`   DATETIME,
  `updated_at`   DATETIME
);

CREATE TABLE `remembered_logins` (
  `token_hash` VARCHAR(64) PRIMARY KEY,
  `donor_id`   INTEGER NOT NULL REFERENCES `donors` (`id`) ON DELETE CASCADE,
  `expires_at` DATETIME
);
