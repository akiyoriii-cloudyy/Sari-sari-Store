-- Add phone column to users table (unique)
ALTER TABLE `users`
  ADD COLUMN `phone` VARCHAR(32) NULL UNIQUE AFTER `email`;

-- Create table to store SMS OTPs (if not exists)
CREATE TABLE IF NOT EXISTS `sms_password_resets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `phone` VARCHAR(32) NOT NULL,
  `otp_code` VARCHAR(10) NOT NULL,
  `expires` INT NOT NULL,
  `attempts` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`phone`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
