SET NAMES utf8mb4;
SET time_zone = '+00:00';

SET SESSION cte_max_recursion_depth = 30000;

INSERT INTO products (name, interest_rate, term_months, created_at, updated_at) VALUES
                                                                                    ('Starter Loan 12m',  0.045000,  12, NOW(), NOW()),
                                                                                    ('Starter Loan 24m',  0.049000,  24, NOW(), NOW()),
                                                                                    ('Standard 12m',      0.055000,  12, NOW(), NOW()),
                                                                                    ('Standard 24m',      0.059500,  24, NOW(), NOW()),
                                                                                    ('Standard 36m',      0.062000,  36, NOW(), NOW()),
                                                                                    ('Standard 48m',      0.064000,  48, NOW(), NOW()),
                                                                                    ('Standard 60m',      0.066000,  60, NOW(), NOW()),
                                                                                    ('Premium 12m',       0.041000,  12, NOW(), NOW()),
                                                                                    ('Premium 24m',       0.043500,  24, NOW(), NOW()),
                                                                                    ('Premium 36m',       0.046000,  36, NOW(), NOW()),
                                                                                    ('Premium 48m',       0.048000,  48, NOW(), NOW()),
                                                                                    ('Premium 60m',       0.050000,  60, NOW(), NOW()),
                                                                                    ('Risk Plus 12m',     0.085000,  12, NOW(), NOW()),
                                                                                    ('Risk Plus 24m',     0.092000,  24, NOW(), NOW()),
                                                                                    ('Risk Plus 36m',     0.098000,  36, NOW(), NOW()),
                                                                                    ('Green Loan 24m',    0.039000,  24, NOW(), NOW()),
                                                                                    ('Green Loan 36m',    0.041000,  36, NOW(), NOW()),
                                                                                    ('Auto Loan 36m',     0.052000,  36, NOW(), NOW()),
                                                                                    ('Auto Loan 48m',     0.054500,  48, NOW(), NOW()),
                                                                                    ('Consolidation 60m', 0.069000,  60, NOW(), NOW());

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 200
)
INSERT INTO brokers (name, email, company_address, types, created_at, updated_at)
SELECT
    CONCAT('Broker ', n),
    CONCAT('broker', n, '@example.test'),
    CONCAT('Company Address ', n, ', Example Street ', (n % 90) + 1),

    CASE
        WHEN n % 7 = 0 THEN 'A,B,C,D'
        WHEN n % 4 = 0 THEN 'A,C'
        WHEN n % 3 = 0 THEN 'B,D'
        WHEN n % 2 = 0 THEN 'A,B'
        ELSE 'C'
        END,
    NOW(), NOW()
FROM seq;

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 5000
)
INSERT INTO customers (full_name, date_of_birth, email, created_at, updated_at)
SELECT
    CONCAT('Customer ', n, ' Example'),
    DATE_ADD('1965-01-01', INTERVAL (n % 18000) DAY),
    CONCAT('customer', n, '@example.test'),
    NOW(), NOW()
FROM seq;


INSERT INTO customer_addresses (customer_id, address_line1, city, postcode, country, valid_from, valid_to, created_at, updated_at)
SELECT
    c.id,
    CONCAT('Street ', (c.id % 200) + 1, ' House ', (c.id % 50) + 1),
    CONCAT('City ', (c.id % 80) + 1),
    LPAD(CAST((c.id % 99999) + 1 AS CHAR), 5, '0'),
    'DE',
    DATE_SUB(CURDATE(), INTERVAL 5 YEAR),
    DATE_SUB(CURDATE(), INTERVAL 2 YEAR),
    NOW(), NOW()
FROM customers c
WHERE c.id % 15 <> 0
  AND c.id % 10 <> 0
  AND c.id % 21 <> 0;

INSERT INTO customer_addresses (customer_id, address_line1, city, postcode, country, valid_from, valid_to, created_at, updated_at)
SELECT
    c.id,
    CONCAT('Avenue ', (c.id % 200) + 1, ' Apt ', (c.id % 120) + 1),
    CONCAT('City ', (c.id % 80) + 1),
    LPAD(CAST((c.id % 99999) + 1 AS CHAR), 5, '0'),
    'DE',
    DATE_SUB(CURDATE(), INTERVAL 2 YEAR),
    NULL,
    NOW(), NOW()
FROM customers c
WHERE c.id % 15 <> 0
  AND c.id % 10 <> 0
  AND c.id % 21 <> 0;

INSERT INTO customer_addresses (customer_id, address_line1, city, postcode, country, valid_from, valid_to, created_at, updated_at)
SELECT
    c.id,
    CONCAT('ShortHistory St ', (c.id % 200) + 1),
    CONCAT('City ', (c.id % 80) + 1),
    LPAD(CAST((c.id % 99999) + 1 AS CHAR), 5, '0'),
    'DE',
    DATE_SUB(CURDATE(), INTERVAL 2 YEAR),
    NULL,
    NOW(), NOW()
FROM customers c
WHERE c.id % 15 <> 0
  AND c.id % 10 = 0;

INSERT INTO customer_addresses (customer_id, address_line1, city, postcode, country, valid_from, valid_to, created_at, updated_at)
SELECT
    c.id,
    CONCAT('GapStart Rd ', (c.id % 200) + 1),
    CONCAT('City ', (c.id % 80) + 1),
    LPAD(CAST((c.id % 99999) + 1 AS CHAR), 5, '0'),
    'DE',
    DATE_SUB(CURDATE(), INTERVAL 5 YEAR),
    DATE_SUB(CURDATE(), INTERVAL 3 YEAR),
    NOW(), NOW()
FROM customers c
WHERE c.id % 15 <> 0
  AND c.id % 21 = 0;

INSERT INTO customer_addresses (customer_id, address_line1, city, postcode, country, valid_from, valid_to, created_at, updated_at)
SELECT
    c.id,
    CONCAT('GapEnd Rd ', (c.id % 200) + 1),
    CONCAT('City ', (c.id % 80) + 1),
    LPAD(CAST((c.id % 99999) + 1 AS CHAR), 5, '0'),
    'DE',
    -- start after a gap of ~6 months
    DATE_SUB(CURDATE(), INTERVAL 30 MONTH),
    NULL,
    NOW(), NOW()
FROM customers c
WHERE c.id % 15 <> 0
  AND c.id % 21 = 0;


WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 20000
)
INSERT INTO applications (broker_id, product_id, interest_rate, loan_amount, status, created_at, updated_at)
SELECT
    CASE WHEN n % 5 = 0 THEN NULL ELSE (n % 200) + 1 END AS broker_id,
    ((n % 20) + 1) AS product_id,
    p.interest_rate AS interest_rate,
    CAST(1000 + (n % 9000) * 10 AS DECIMAL(12,2)) AS loan_amount,
    CASE
        WHEN n % 11 = 0 THEN 'CANCELLED'
        WHEN n % 9  = 0 THEN 'DECLINED'
        WHEN n % 7  = 0 THEN 'COMPLETED'
        WHEN n % 4  = 0 THEN 'APPROVED'
        WHEN n % 3  = 0 THEN 'PROCESSING'
        ELSE 'NEW'
        END AS status,
    DATE_SUB(NOW(), INTERVAL (n % 365) DAY) AS created_at,
    DATE_SUB(NOW(), INTERVAL (n % 365) DAY) AS updated_at
FROM seq
         JOIN products p ON p.id = ((seq.n % 20) + 1);


INSERT INTO application_applicants (application_id, customer_id, role, created_at, updated_at)
SELECT
    a.id,
    ((a.id % 5000) + 1) AS customer_id,
    'PRIMARY',
    a.created_at,
    a.created_at
FROM applications a;

INSERT INTO application_applicants (application_id, customer_id, role, created_at, updated_at)
SELECT
    a.id,
    (((a.id + 123) % 5000) + 1) AS customer_id,
    'COAPPLICANT',
    a.created_at,
    a.created_at
FROM applications a
WHERE a.id % 7 = 0
  AND (((a.id + 123) % 5000) + 1) <> ((a.id % 5000) + 1);


INSERT INTO application_status_audit (application_id, old_status, new_status, changed_at, created_at, updated_at)
SELECT
    a.id,
    NULL,
    'NEW',
    a.created_at,
    NOW(), NOW()
FROM applications a;

INSERT INTO application_status_audit (application_id, old_status, new_status, changed_at, created_at, updated_at)
SELECT
    a.id,
    'NEW',
    'PROCESSING',
    DATE_ADD(a.created_at, INTERVAL 1 HOUR),
    NOW(), NOW()
FROM applications a;

INSERT INTO application_status_audit (application_id, old_status, new_status, changed_at, created_at, updated_at)
SELECT a.id, 'PROCESSING', 'APPROVED', DATE_ADD(a.created_at, INTERVAL 6 HOUR), NOW(), NOW()
FROM applications a
WHERE a.status IN ('APPROVED','COMPLETED');

INSERT INTO application_status_audit (application_id, old_status, new_status, changed_at, created_at, updated_at)
SELECT a.id, 'APPROVED', 'COMPLETED', DATE_ADD(a.created_at, INTERVAL 30 DAY), NOW(), NOW()
FROM applications a
WHERE a.status = 'COMPLETED';

INSERT INTO application_status_audit (application_id, old_status, new_status, changed_at, created_at, updated_at)
SELECT a.id, 'PROCESSING', 'DECLINED', DATE_ADD(a.created_at, INTERVAL 8 HOUR), NOW(), NOW()
FROM applications a
WHERE a.status = 'DECLINED';

INSERT INTO application_status_audit (application_id, old_status, new_status, changed_at, created_at, updated_at)
SELECT a.id, 'PROCESSING', 'CANCELLED', DATE_ADD(a.created_at, INTERVAL 2 DAY), NOW(), NOW()
FROM applications a
WHERE a.status = 'CANCELLED';
