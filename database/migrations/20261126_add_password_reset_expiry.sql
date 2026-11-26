-- Store the creation time for password reset codes so they expire after 10 minutes.
SET @otp_created_at_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'otp_created_at'
);
SET @otp_created_at_sql = IF(
    @otp_created_at_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `otp_created_at` int unsigned NULL DEFAULT NULL',
    'SELECT 1'
);
PREPARE otp_created_at_statement FROM @otp_created_at_sql;
EXECUTE otp_created_at_statement;
DEALLOCATE PREPARE otp_created_at_statement;
