SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `managed_users`;
DROP TABLE IF EXISTS `collaborators`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `states`;
DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(60) NOT NULL,
    `slug` VARCHAR(40) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `states` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `clave` CHAR(3) NOT NULL,
    `clave_curp` CHAR(2) NOT NULL,
    `nombre` VARCHAR(60) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `states_clave_unique` (`clave`),
    UNIQUE KEY `states_clave_curp_unique` (`clave_curp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `name` TEXT NOT NULL,
    `email` TEXT NOT NULL,
    `email_hash` CHAR(64) NOT NULL,
    `rfc` TEXT NOT NULL,
    `rfc_hash` CHAR(64) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `address` TEXT NULL,
    `phone` TEXT NULL,
    `website` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_hash_unique` (`email_hash`),
    UNIQUE KEY `users_rfc_hash_unique` (`rfc_hash`),
    CONSTRAINT `users_role_id_foreign`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
    `user_id` BIGINT UNSIGNED NOT NULL,
    `token_hash` CHAR(64) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `used_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `password_reset_tokens_token_hash_unique` (`token_hash`),
    CONSTRAINT `password_reset_tokens_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `sessions_user_id_unique` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`),
    CONSTRAINT `sessions_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `collaborators` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `nombre` TEXT NOT NULL,
    `correo` TEXT NOT NULL,
    `correo_hash` CHAR(64) NOT NULL,
    `rfc` TEXT NOT NULL,
    `rfc_hash` CHAR(64) NOT NULL,
    `domicilio_fiscal` TEXT NOT NULL,
    `curp` TEXT NOT NULL,
    `curp_hash` CHAR(64) NOT NULL,
    `numero_seguridad_social` TEXT NOT NULL,
    `nss_hash` CHAR(64) NOT NULL,
    `fecha_inicio_laboral` DATE NOT NULL,
    `tipo_contrato` TEXT NOT NULL,
    `departamento` TEXT NOT NULL,
    `puesto` TEXT NOT NULL,
    `salario_diario` TEXT NOT NULL,
    `salario` TEXT NOT NULL,
    `state_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `collaborators_user_id_correo_hash_unique` (`user_id`, `correo_hash`),
    UNIQUE KEY `collaborators_user_id_rfc_hash_unique` (`user_id`, `rfc_hash`),
    UNIQUE KEY `collaborators_curp_hash_unique` (`curp_hash`),
    UNIQUE KEY `collaborators_nss_hash_unique` (`nss_hash`),
    KEY `collaborators_state_id_foreign` (`state_id`),
    CONSTRAINT `collaborators_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `collaborators_state_id_foreign`
        FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `managed_users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `name` TEXT NOT NULL,
    `rfc` TEXT NOT NULL,
    `rfc_hash` CHAR(64) NOT NULL,
    `address` TEXT NULL,
    `phone` TEXT NULL,
    `website` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `managed_users_created_by_rfc_hash_unique` (`created_by`, `rfc_hash`),
    CONSTRAINT `managed_users_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
