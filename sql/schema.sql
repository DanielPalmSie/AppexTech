SET NAMES utf8mb4;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS application_status_audit;
DROP TABLE IF EXISTS application_applicants;
DROP TABLE IF EXISTS customer_addresses;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS brokers;
DROP TABLE IF EXISTS customers;

CREATE TABLE customers (
                           id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                           full_name VARCHAR(200) NOT NULL,
                           date_of_birth DATE NOT NULL,
                           email VARCHAR(255) NOT NULL,
                           created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                           PRIMARY KEY (id),
                           UNIQUE KEY uq_customers_email (email),
                           KEY idx_customers_dob (date_of_birth),
                           KEY idx_customers_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_addresses (
                                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                    customer_id BIGINT UNSIGNED NOT NULL,
                                    address_line1 VARCHAR(255) NOT NULL,
                                    city VARCHAR(120) NOT NULL,
                                    postcode VARCHAR(30) NOT NULL,
                                    country CHAR(2) NOT NULL,
                                    valid_from DATE NOT NULL,
                                    valid_to DATE NULL,
                                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                    PRIMARY KEY (id),
                                    KEY idx_addr_customer_from (customer_id, valid_from),
                                    KEY idx_addr_customer_to (customer_id, valid_to),
                                    CONSTRAINT fk_addr_customer
                                        FOREIGN KEY (customer_id) REFERENCES customers(id)
                                            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE brokers (
                         id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                         name VARCHAR(200) NOT NULL,
                         email VARCHAR(255) NOT NULL,
                         company_address VARCHAR(500) NOT NULL,
                         types SET('A','B','C','D') NOT NULL,
                         created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         PRIMARY KEY (id),
                         UNIQUE KEY uq_brokers_email (email),
                         KEY idx_brokers_types (types),
                         KEY idx_brokers_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
                          id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          name VARCHAR(200) NOT NULL,
                          interest_rate DECIMAL(7,6) NOT NULL,
                          term_months SMALLINT UNSIGNED NOT NULL,
                          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (id),
                          UNIQUE KEY uq_products_name (name),
                          KEY idx_products_rate (interest_rate),
                          KEY idx_products_term (term_months)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE applications (
                              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                              broker_id INT UNSIGNED NULL,
                              product_id SMALLINT UNSIGNED NOT NULL,
                              interest_rate DECIMAL(7,6) NOT NULL,
                              loan_amount DECIMAL(12,2) NOT NULL,
                              monthly_payment DECIMAL(12,2)
                                  GENERATED ALWAYS AS (ROUND(loan_amount * (interest_rate / 12), 2)) STORED,
                              status ENUM('NEW','PROCESSING','APPROVED','DECLINED','COMPLETED','CANCELLED') NOT NULL DEFAULT 'NEW',
                              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                              PRIMARY KEY (id),
                              KEY idx_app_broker (broker_id),
                              KEY idx_app_status (status),
                              KEY idx_app_product (product_id),
                              KEY idx_app_created_at (created_at),
                              CONSTRAINT fk_app_broker
                                  FOREIGN KEY (broker_id) REFERENCES brokers(id)
                                      ON DELETE SET NULL,
                              CONSTRAINT fk_app_product
                                  FOREIGN KEY (product_id) REFERENCES products(id)
                                      ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE application_applicants (
                                        application_id BIGINT UNSIGNED NOT NULL,
                                        customer_id BIGINT UNSIGNED NOT NULL,
                                        role ENUM('PRIMARY','COAPPLICANT') NOT NULL DEFAULT 'PRIMARY',
                                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                        PRIMARY KEY (application_id, customer_id),
                                        KEY idx_applicants_customer (customer_id),
                                        CONSTRAINT fk_applicants_app
                                            FOREIGN KEY (application_id) REFERENCES applications(id)
                                                ON DELETE CASCADE,
                                        CONSTRAINT fk_applicants_customer
                                            FOREIGN KEY (customer_id) REFERENCES customers(id)
                                                ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE application_status_audit (
                                          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                          application_id BIGINT UNSIGNED NOT NULL,
                                          old_status ENUM('NEW','PROCESSING','APPROVED','DECLINED','COMPLETED','CANCELLED') NULL,
                                          new_status ENUM('NEW','PROCESSING','APPROVED','DECLINED','COMPLETED','CANCELLED') NOT NULL,
                                          changed_at DATETIME NOT NULL,
                                          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                          PRIMARY KEY (id),
                                          KEY idx_audit_app_changed (application_id, changed_at),
                                          KEY idx_audit_new_status (new_status),
                                          CONSTRAINT fk_audit_app
                                              FOREIGN KEY (application_id) REFERENCES applications(id)
                                                  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
