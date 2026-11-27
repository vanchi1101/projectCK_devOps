-- Remove the legacy CTV BAN ACC role.
-- Run against the configured shopvip database after deploying the code.
UPDATE `users`
SET `ctvacc` = 0
WHERE `ctvacc` = 1;
