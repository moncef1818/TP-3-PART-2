CREATE DATABASE IF NOT EXISTS netbank_db;
USE netbank_db;
CREATE TABLE IF NOT EXISTS corporate_accounts (
  id INT PRIMARY KEY,
  username VARCHAR(50),
  email VARCHAR(100),
  balance_dzd INT,
  api_status VARCHAR(100)
);
INSERT INTO corporate_accounts VALUES
(1, 'treasury_admin', 'cfo@atlas-netbank.dz', 2500000, 'Standard Production Key');
CREATE TABLE IF NOT EXISTS wire_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient VARCHAR(100),
  amount_dzd INT,
  status_note VARCHAR(255)
);
INSERT INTO wire_transfers (recipient, amount_dzd, status_note) VALUES
('Algiers_Supplier_01', 150000, 'Routine monthly invoice');
