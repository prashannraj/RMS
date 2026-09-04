<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\AdvertisementCode;
use App\Models\Application;
use App\Models\Board;
use App\Models\Candidate;
use App\Models\Caste;
use App\Models\Challan;
use App\Models\District;
use App\Models\ExamCenter;
use App\Models\ExamScheduling;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\LocalBody;
use App\Models\MotherTongue;
use App\Models\Organization;
use App\Models\Post;
use App\Models\PostCombination;
use App\Models\Qualification;
use App\Models\Quota;
use App\Models\Religion;
use App\Models\Requisition;
use App\Models\Service;
use App\Models\State;
use App\Models\SubGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Roles & Permissions ─────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $candidateRole = Role::firstOrCreate(['name' => 'candidate', 'guard_name' => 'web']);

        $perms = [
            'manage-users', 'manage-roles', 'manage-advertisements',
            'manage-applications', 'manage-payments', 'manage-exams',
            'manage-documents', 'manage-reports', 'manage-master-data',
            'apply-application', 'view-dashboard',
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }
        $adminRole->syncPermissions($perms);
        $managerRole->syncPermissions(array_diff($perms, ['manage-users', 'manage-roles']));
        $staffRole->syncPermissions(['manage-applications', 'manage-payments', 'manage-exams', 'manage-documents']);
        $candidateRole->syncPermissions(['apply-application', 'view-dashboard']);

        // ── 2. Admin user ──────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@ppsc.gov.np'],
            [
                'name' => 'PPSC Administrator',
                'password' => Hash::make('Admin@12345'),
                'phone' => '01-4567890',
                'is_active' => true,
            ]
        );
        $admin->assignRole($adminRole);

        // ── 3. Sample candidate user ───────────────────────────────────────
        $candidateUser = User::firstOrCreate(
            ['email' => 'candidate@example.com'],
            [
                'name' => 'Ram Bahadur Thapa',
                'password' => Hash::make('Candidate@123'),
                'phone' => '9841234567',
                'is_active' => true,
            ]
        );
        $candidateUser->assignRole($candidateRole);

        // ── 4. Master data ─────────────────────────────────────────────────
        $state = State::firstOrCreate(
            ['state_name_en' => 'Bagmati'],
            ['state_name_np' => 'बागमती', 'is_active' => true]
        );

        $district = District::firstOrCreate(
            ['district_name_en' => 'Kathmandu'],
            ['district_name_np' => 'काठमाडौं', 'state_id' => $state->id, 'is_active' => true]
        );

        LocalBody::firstOrCreate(
            ['local_body_name_en' => 'Kathmandu Metropolitan City'],
            ['local_body_name_np' => 'काठमाडौं महानगरपालिका', 'district_id' => $district->id, 'is_active' => true]
        );

        Caste::firstOrCreate(
            ['caste_name_en' => 'Brahmin'],
            ['caste_name_np' => 'ब्राह्मण', 'is_active' => true]
        );

        Religion::firstOrCreate(
            ['religion_name_en' => 'Hindu'],
            ['religion_name_np' => 'हिन्दू', 'is_active' => true]
        );

        MotherTongue::firstOrCreate(
            ['mother_tongue_name' => 'Nepali'],
            ['is_active' => true]
        );

        Board::firstOrCreate(
            ['board_name_en' => 'Tribhuvan University'],
            ['board_name_np' => 'त्रिभुवन विश्वविद्यालय', 'is_active' => true]
        );

        Faculty::firstOrCreate(
            ['faculty_name_en' => 'Management'],
            ['faculty_name_np' => 'व्यवस्थापन', 'is_active' => true]
        );

        Qualification::firstOrCreate(
            ['qualification_name_en' => 'Bachelor'],
            ['qualification_name_np' => 'स्नातक', 'is_active' => true]
        );

        $service = Service::firstOrCreate(
            ['service_name_en' => 'Administration'],
            ['service_name_np' => 'प्रशासन', 'is_active' => true]
        );

        $group = Group::firstOrCreate(
            ['group_name_en' => 'Non-Gazetted'],
            ['group_name_np' => 'गैर-राजपत्र', 'is_active' => true]
        );

        SubGroup::firstOrCreate(
            ['sub_group_name_en' => 'Level 6'],
            ['sub_group_name_np' => 'लेभल ६', 'is_active' => true]
        );

        $post = Post::firstOrCreate(
            ['post_name' => 'Officer'],
            ['remarks' => 'Administrative officer post', 'is_active' => true]
        );

        PostCombination::firstOrCreate(
            ['post_id' => $post->id, 'service_id' => $service->id, 'group_id' => $group->id, 'sub_group_id' => null],
            []
        );

        $quota = Quota::firstOrCreate(
            ['quota_name' => 'Open'],
            ['remarks' => 'Open competition', 'is_active' => true]
        );

        $org = Organization::firstOrCreate(
            ['organization_name_en' => 'Public Service Commission'],
            [
                'organization_name_np' => 'लोक सेवा आयोग',
                'organization_code' => 'PSC-001',
                'can_schedule_exam' => true,
                'is_active' => true,
            ]
        );

        // ── 5. Requisition (required by advertisements) ────────────────────
        $requisition = Requisition::firstOrCreate(
            ['fiscal_year' => '2081/82'],
            [
                'requesting_office_id' => $org->id,
                'demand_office' => 'Public Service Commission',
                'total_vacancy' => 5,
                'status' => 'approved',
                'distribution_flag' => 'completed',
                'exam_scheduling_flag' => 'pending',
                'is_active' => true,
            ]
        );

        // ── 6. Sample advertisements ───────────────────────────────────────
        Advertisement::firstOrCreate(
            ['advertisementcode' => 'PPSC-2081/82-001'],
            [
                'advertisementnumber' => '001/2081-82',
                'quota_id' => $quota->id,
                'requisition_id' => $requisition->id,
                'status' => 'published',
                'vacancy' => 5,
                'published_date_en' => '2025-09-01',
                'published_date_np' => '2081/05/15',
                'application_start_at' => '2025-09-15',
                'application_end_at' => '2025-12-31',
                'double_fee_deadline_at' => '2025-12-15',
                'description' => 'Recruitment for Administrative Officer positions under Open competition.',
                'description_np' => 'खुल्ला प्रतिस्पर्धामा प्रशासनिक अधिकारी पद भर्ना।',
                'is_active' => true,
            ]
        );

        // Advertisement code (exam lifecycle)
        AdvertisementCode::firstOrCreate(
            ['advertisement_code' => 'PPSC-2081/82-001'],
            [
                'advertisement_published_date_en' => '2025-09-01',
                'advertisement_published_date_np' => '2081/05/15',
                'last_date_for_submission' => '2025-12-31',
                'last_date_for_submission_np' => '2081/09/15',
                'lifecycle_status' => 'published',
                'memorandum_number' => 'PPSC/081-82/001',
                'payment_last_date_en' => '2025-12-31',
                'payment_last_date_np' => '2081/09/15',
                'requesting_office_id' => $org->id,
                'exam_scheduling_status' => 'pending',
                'is_active' => true,
            ]
        );

        // ── 7. Exam centers ────────────────────────────────────────────────
        ExamCenter::firstOrCreate(
            ['exam_center_name_en' => 'Kathmandu Exam Center'],
            [
                'exam_center_name_np' => 'काठमाडौं परीक्षा केन्द्र',
                'state_id' => $state->id,
                'district_id' => $district->id,
                'address' => 'Putalisadak, Kathmandu',
                'contact_person_name_np' => 'परीक्षा संचालक',
                'contact_person_name_en' => 'Exam Controller',
                'contact_number' => '01-4567891',
                'contact_email' => 'exam@ppsc.gov.np',
                'center_capacity' => 500,
                'is_active' => true,
            ]
        );

        // ── 8. Sample candidate profile ─────────────────────────────────────
        $candidate = Candidate::firstOrCreate(
            ['user_id' => $candidateUser->id],
            [
                'first_name' => 'Ram Bahadur',
                'last_name' => 'Thapa',
                'date_of_birth_ad' => '1995-05-15',
                'date_of_birth_bs' => '2052/02/01',
                'citizenship_no' => '1234-5678-9012',
                'district_id' => $district->id,
                'gender' => 'Male',
                'is_active' => true,
            ]
        );

        // ── 9. Sample application ──────────────────────────────────────────
        $application = Application::firstOrCreate(
            ['advertisement_code' => 'PPSC-2081/82-001', 'candidate_id' => $candidate->id],
            [
                'advertisement_number' => '001/2081-82',
                'deposited_fee' => 1000.00,
                'total_fee' => 1000.00,
                'payment_status' => 'pending',
                'submitted_at' => null,
                'is_active' => true,
            ]
        );

        // ── 10. Sample challan ──────────────────────────────────────────────
        Challan::firstOrCreate(
            ['application_id' => $application->id],
            [
                'advt_code' => 'PPSC-2081/82-001',
                'amount' => 1000.00,
                'challan_date' => '2025-10-01',
                'challan_time' => '10:00:00',
                'name' => 'Ram Bahadur Thapa',
                'office' => 'Public Service Commission',
                'status' => 'pending',
                'username' => 'candidate@example.com',
                'voucher_no' => 'VCH-2025-001',
            ]
        );

        // ── 11. Sample exam scheduling ─────────────────────────────────────
        ExamScheduling::firstOrCreate(
            ['category' => 'PPSC-2081/82-001'],
            [
                'starttime' => '10:00:00',
                'endtime' => '13:00:00',
                'exam_date' => '2026-02-15',
                'status' => 'scheduled',
            ]
        );
    }
}