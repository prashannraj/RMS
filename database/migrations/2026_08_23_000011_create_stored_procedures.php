<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only run for MySQL
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // ─── Stored Function: Get record count by status ───
        DB::unprepared("
            CREATE FUNCTION IF NOT EXISTS fn_record_count_by_status(p_status VARCHAR(50))
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count FROM records WHERE status = p_status AND deleted_at IS NULL;
                RETURN v_count;
            END
        ");

        // ─── Stored Function: Get total resource cost ───
        DB::unprepared("
            CREATE FUNCTION IF NOT EXISTS fn_total_resource_cost()
            RETURNS DECIMAL(15,2)
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(15,2);
                SELECT COALESCE(SUM(cost), 0) INTO v_total FROM resources WHERE deleted_at IS NULL;
                RETURN v_total;
            END
        ");

        // ─── Stored Procedure: Get dashboard stats ───
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS sp_get_dashboard_stats()
            BEGIN
                SELECT
                    (SELECT COUNT(*) FROM resources WHERE deleted_at IS NULL) AS total_resources,
                    (SELECT COUNT(*) FROM resources WHERE status = 'active' AND deleted_at IS NULL) AS active_resources,
                    (SELECT COUNT(*) FROM records WHERE deleted_at IS NULL) AS total_records,
                    (SELECT COUNT(*) FROM records WHERE status = 'pending' AND deleted_at IS NULL) AS pending_approvals,
                    (SELECT COUNT(*) FROM records WHERE status = 'approved' AND approved_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND deleted_at IS NULL) AS approved_this_month,
                    (SELECT COUNT(*) FROM users WHERE is_active = 1 AND deleted_at IS NULL) AS total_users,
                    (SELECT COALESCE(SUM(cost), 0) FROM resources WHERE deleted_at IS NULL) AS total_cost;
            END
        ");

        // ─── Stored Procedure: Get records by date range ───
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS sp_get_records_by_date_range(
                IN p_date_from DATE,
                IN p_date_to DATE
            )
            BEGIN
                SELECT *
                FROM records
                WHERE created_at BETWEEN p_date_from AND p_date_to
                  AND deleted_at IS NULL
                ORDER BY created_at DESC;
            END
        ");

        // ─── Stored Procedure: Get resource stats by category ───
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS sp_get_resource_stats_by_category()
            BEGIN
                SELECT
                    c.id AS category_id,
                    c.name AS category_name,
                    COUNT(r.id) AS resource_count,
                    COALESCE(SUM(r.cost), 0) AS total_cost
                FROM categories c
                LEFT JOIN resources r ON r.category_id = c.id AND r.deleted_at IS NULL
                GROUP BY c.id, c.name
                ORDER BY resource_count DESC;
            END
        ");

        // ─── Stored Procedure: Get pending approvals ───
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS sp_get_pending_approvals()
            BEGIN
                SELECT
                    r.id,
                    r.title,
                    r.type,
                    r.status,
                    r.created_at,
                    u.name AS created_by_name,
                    u.email AS created_by_email
                FROM records r
                LEFT JOIN users u ON u.id = r.created_by
                WHERE r.status = 'pending'
                  AND r.deleted_at IS NULL
                ORDER BY r.created_at ASC;
            END
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_pending_approvals');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_resource_stats_by_category');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_records_by_date_range');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_dashboard_stats');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_total_resource_cost');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_record_count_by_status');
    }
};