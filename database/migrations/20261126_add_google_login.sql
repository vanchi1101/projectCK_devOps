-- Add a stable Google account identifier without linking existing local accounts.
SET @google_sub_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'google_sub'
);
SET @google_sub_sql = IF(
    @google_sub_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `google_sub` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NULL DEFAULT NULL',
    'SELECT 1'
);
PREPARE google_sub_statement FROM @google_sub_sql;
EXECUTE google_sub_statement;
DEALLOCATE PREPARE google_sub_statement;

SET @google_sub_index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND INDEX_NAME = 'uq_users_google_sub'
);
SET @google_sub_index_sql = IF(
    @google_sub_index_exists = 0,
    'ALTER TABLE `users` ADD UNIQUE KEY `uq_users_google_sub` (`google_sub`)',
    'SELECT 1'
);
PREPARE google_sub_index_statement FROM @google_sub_index_sql;
EXECUTE google_sub_index_statement;
DEALLOCATE PREPARE google_sub_index_statement;
