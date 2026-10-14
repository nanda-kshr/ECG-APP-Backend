-- Migration: 0001_add_read_at
-- Description: Add read_at column to tasks table to track when a doctor has read a task.

ALTER TABLE `tasks` ADD COLUMN `read_at` DATETIME DEFAULT NULL;
