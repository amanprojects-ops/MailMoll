-- Migration 002: Add auth_type column to smtp_accounts
ALTER TABLE smtp_accounts ADD COLUMN auth_type TEXT NOT NULL DEFAULT 'LOGIN';
