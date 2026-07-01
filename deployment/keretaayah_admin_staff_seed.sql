-- KeretaAyah admin/staff seed SQL
-- Import after keretaayah_schema.sql if you cannot run php artisan db:seed.
-- Existing users with the same email keep their current password.

INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`)
VALUES
('KeretaAyah Admin', 'admin@keretaayah.myuni.agency', '2026-06-03 04:16:42', '$2y$12$GTZSOsFjtpRtkSNJg2Bl7uaLUuvlAsHKeCyzLslCVIRvbzlaNI.IW', 'admin', NULL, '2026-06-03 04:16:42', '2026-06-03 04:16:42')
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `role` = VALUES(`role`),
  `email_verified_at` = COALESCE(`email_verified_at`, VALUES(`email_verified_at`)),
  `updated_at` = VALUES(`updated_at`);

INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`)
VALUES
('KeretaAyah Staff', 'staff@keretaayah.myuni.agency', '2026-06-03 04:16:42', '$2y$12$bmirp6T0.yN22ISXdYzWIugbsv/B1x30XIcJDMqZHlT.UBzj2YV3.', 'staff', NULL, '2026-06-03 04:16:42', '2026-06-03 04:16:42')
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `role` = VALUES(`role`),
  `email_verified_at` = COALESCE(`email_verified_at`, VALUES(`email_verified_at`)),
  `updated_at` = VALUES(`updated_at`);