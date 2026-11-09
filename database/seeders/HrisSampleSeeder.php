<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class HrisSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Departments
        $it = Department::firstOrCreate(['code' => 'IT'], [
            'name' => 'Information Technology',
            'description' => 'Software engineering, infrastructure and IT support',
        ]);

        $hr = Department::firstOrCreate(['code' => 'HR'], [
            'name' => 'Human Resources',
            'description' => 'Recruitment, culture, and employee management',
        ]);

        $fin = Department::firstOrCreate(['code' => 'FIN'], [
            'name' => 'Finance & Accounting',
            'description' => 'Financial operations, payroll, and auditing',
        ]);

        // 2. Positions
        $itLead = Position::firstOrCreate(['code' => 'IT-LEAD'], [
            'department_id' => $it->id,
            'name' => 'Engineering Lead',
            'description' => 'Oversees technical team and architecture',
        ]);

        $softDev = Position::firstOrCreate(['code' => 'DEV-SR'], [
            'department_id' => $it->id,
            'name' => 'Senior Backend Engineer',
            'description' => 'Develops core business services and APIs',
        ]);

        $hrMgr = Position::firstOrCreate(['code' => 'HR-MGR'], [
            'department_id' => $hr->id,
            'name' => 'HR Manager',
            'description' => 'Manages HR division and talent operations',
        ]);

        $financeOfficer = Position::firstOrCreate(['code' => 'FIN-OFF'], [
            'department_id' => $fin->id,
            'name' => 'Finance Officer',
            'description' => 'Handles bookkeeping and expenses',
        ]);

        // 3. Sample Manager Employee
        $manager = Employee::firstOrCreate(['employee_code' => 'EMP-001'], [
            'department_id' => $it->id,
            'position_id' => $itLead->id,
            'first_name' => 'Budi',
            'last_name' => 'Santoso',
            'email' => 'budi.santoso@hris.local',
            'phone' => '081234567890',
            'gender' => 'male',
            'date_of_birth' => '1988-04-12',
            'join_date' => '2021-01-15',
            'employment_status' => 'permanent',
            'status' => 'active',
            'address' => 'Jakarta Selatan, DKI Jakarta',
        ]);

        // 4. Sample Staff Employee reporting to Manager
        Employee::firstOrCreate(['employee_code' => 'EMP-002'], [
            'department_id' => $it->id,
            'position_id' => $softDev->id,
            'manager_id' => $manager->id,
            'first_name' => 'Siti',
            'last_name' => 'Rahmawati',
            'email' => 'siti.rahmawati@hris.local',
            'phone' => '081298765432',
            'gender' => 'female',
            'date_of_birth' => '1995-08-20',
            'join_date' => '2023-03-01',
            'employment_status' => 'permanent',
            'status' => 'active',
            'address' => 'Tangerang, Banten',
        ]);

        // 5. HR Employee
        Employee::firstOrCreate(['employee_code' => 'EMP-003'], [
            'department_id' => $hr->id,
            'position_id' => $hrMgr->id,
            'first_name' => 'Dewi',
            'last_name' => 'Lestari',
            'email' => 'dewi.lestari@hris.local',
            'phone' => '081311223344',
            'gender' => 'female',
            'date_of_birth' => '1992-11-05',
            'join_date' => '2022-06-10',
            'employment_status' => 'permanent',
            'status' => 'active',
            'address' => 'Jakarta Pusat, DKI Jakarta',
        ]);
    }
}
