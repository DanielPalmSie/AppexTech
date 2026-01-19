SELECT
    b.id AS broker_id,
    b.name AS broker_name,
    COUNT(a.id) AS applications_count
FROM brokers b
         LEFT JOIN applications a
                   ON a.broker_id = b.id
GROUP BY b.id, b.name
ORDER BY applications_count DESC, b.id ASC;

SELECT
    b.id   AS broker_id,
    b.name AS broker_name,
    a.id   AS application_id,
    sa.old_status,
    sa.new_status,
    sa.changed_at
FROM brokers b
         JOIN applications a
              ON a.broker_id = b.id
         JOIN application_status_audit sa
              ON sa.application_id = a.id
ORDER BY b.id ASC, a.id ASC, sa.changed_at ASC;

WITH
    params AS (
        SELECT
            DATE_SUB(CURDATE(), INTERVAL 5 YEAR) AS window_start,
            CURDATE() AS window_end
    ),
    addr AS (
        SELECT
            ca.customer_id,
            GREATEST(ca.valid_from, p.window_start) AS start_d,
            LEAST(COALESCE(ca.valid_to, p.window_end), p.window_end) AS end_d
        FROM customer_addresses ca
                 JOIN params p
        WHERE COALESCE(ca.valid_to, p.window_end) >= p.window_start
          AND ca.valid_from <= p.window_end
    ),
    ordered AS (
        SELECT
            customer_id,
            start_d,
            end_d,
            LAG(end_d) OVER (PARTITION BY customer_id ORDER BY start_d, end_d) AS prev_end
        FROM addr
    ),
    gaps AS (
        SELECT
            o.customer_id,
            MAX(CASE WHEN o.prev_end IS NOT NULL AND o.start_d > DATE_ADD(o.prev_end, INTERVAL 1 DAY) THEN 1 ELSE 0 END) AS has_gap,
            MIN(o.start_d) AS min_start,
            MAX(o.end_d) AS max_end
        FROM ordered o
        GROUP BY o.customer_id
    )
SELECT
    c.id,
    c.full_name,
    c.email
FROM customers c
         CROSS JOIN params p
         LEFT JOIN gaps g
                   ON g.customer_id = c.id
WHERE
    g.customer_id IS NULL
   OR g.min_start > p.window_start
   OR g.max_end < p.window_end
   OR g.has_gap = 1
ORDER BY c.id ASC;
