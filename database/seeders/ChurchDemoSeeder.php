<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Department;
use App\Models\Event;
use Spatie\Permission\Models\Role;

class ChurchDemoSeeder extends Seeder
{
    /**
     * Data nyingi za mfano kwa ajili ya Kanisa la Manzese SDA
     * Majina ya Kiswahili/Tanzania - realistic demo data
     */
    public function run(): void
    {
        $this->command->info('🌍 Inaanza kupakia data za mfano...');

        // ──────────────────────────────────────
        // 1. ROLES
        // ──────────────────────────────────────
        $this->command->info('📋 Inaunda roles...');
        $roles = ['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader', 'member'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ──────────────────────────────────────
        // 2. WATUMIAJI WA MFUMO (Staff)
        // ──────────────────────────────────────
        $this->command->info('👤 Inaunda watumiaji...');

        $superAdmin = User::updateOrCreate(['email' => 'admin@manzesesda.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('Admin@2025!'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->syncRoles(['super_admin']);

        $pastor = User::updateOrCreate(['email' => 'mchungaji@manzesesda.com'], [
            'name' => 'Mch. Emmanuel Kileo',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $pastor->syncRoles(['pastor']);

        $treasurer = User::updateOrCreate(['email' => 'hazina@manzesesda.com'], [
            'name' => 'Dorcas Mwanga',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $treasurer->syncRoles(['treasurer']);

        $admin = User::updateOrCreate(['email' => 'msimamizi@manzesesda.com'], [
            'name' => 'Samuel Kagera',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->syncRoles(['admin']);

        // ──────────────────────────────────────
        // 3. IDARA (Departments)
        // ──────────────────────────────────────
        $this->command->info('🏛️  Inaunda idara...');

        $deptData = [
            ['Ibada na Muziki',                  'Huduma za muziki, kwaya, na ibada ya Sabato'],
            ['Vijana (AY)',                       'Jumuiya ya Vijana wa Adventista - shughuli na michezo ya kiroho'],
            ['Watoto (Pathfinder)',               'Mpango wa Pathfinder na Adventurer kwa watoto'],
            ['Elimu ya Biblia (Sabbath School)',  'Masomo ya kila Sabato asubuhi kwa makundi yote'],
            ['Utumishi wa Jamii (ADRA)',          'Shughuli za kusaidia jamii - chakula, elimu, afya'],
            ['Mawasiliano na Sanaa',              'Picha, video, tovuti, na matangazo ya kanisa'],
            ['Uinjilisti',                        'Kampeni za injili, kugawana neno, na kufikia wengine'],
            ['Afya na Lishe',                     'Maonyesho ya afya, masomo ya lishe ya Kikristo'],
            ['Familia na Ndoa',                   'Ushauri wa ndoa, semina za familia, na mama na mtoto'],
            ['Wazee (Senior Ministry)',           'Shughuli maalum kwa wazee wa kanisa'],
        ];

        $departments = [];
        foreach ($deptData as [$name, $desc]) {
            $departments[] = Department::firstOrCreate(['name' => $name], ['description' => $desc]);
        }

        // ──────────────────────────────────────
        // 4. WANACHAMA WENGI (60 members)
        // ──────────────────────────────────────
        $this->command->info('👥 Inaunda wanachama 60...');

        $membersRawData = [
            ['Emmanuel Kileo',       'male',   '1975-03-12', 'mchungaji@manzesesda.com',       '0754001001', 'married',  'Manzese Shule',        '2000-04-15'],
            ['Dorcas Mwanga',        'female', '1982-09-18', 'hazina@manzesesda.com',           '0754001002', 'married',  'Manzese Kwa Mtogole',  '2001-08-20'],
            ['Samuel Kagera',        'male',   '1988-07-04', 'msimamizi@manzesesda.com',        '0754001003', 'married',  'Sinza',                '2003-01-10'],
            ['Grace Mhina',          'female', '1990-11-25', 'grace.mhina@gmail.com',          '0754001004', 'single',   'Manzese Makorora',     '2005-03-22'],
            ['Peter Lusambo',        'male',   '1985-06-30', 'peter.lusambo@gmail.com',        '0754001005', 'married',  'Manzese Ward 3',       '1999-12-01'],
            ['Ruth Shillingi',       'female', '1993-04-17', 'ruth.shillingi@gmail.com',       '0754001006', 'single',   'Tandale',              '2010-07-14'],
            ['Amos Ndunguru',        'male',   '1978-02-08', 'amos.ndunguru@gmail.com',        '0754001007', 'married',  'Manzese Kivule',       '1996-09-30'],
            ['Esther Mwita',         'female', '1987-08-21', 'esther.mwita@gmail.com',         '0754001008', 'married',  'Manzese Shule',        '2002-06-18'],
            ['Joshua Chacha',        'male',   '1995-01-14', 'joshua.chacha@gmail.com',        '0754001009', 'single',   'Manzese Ward 4',       '2012-04-05'],
            ['Miriam Mganga',        'female', '1991-05-09', 'miriam.mganga@gmail.com',        '0754001010', 'single',   'Magomeni',             '2008-11-25'],
            ['Daniel Msigwa',        'male',   '1970-12-03', 'daniel.msigwa@gmail.com',        '0754001011', 'married',  'Manzese Kwa Mtogole',  '1990-03-17'],
            ['Elizabeth Makonda',    'female', '1983-07-28', 'elizabeth.makonda@gmail.com',    '0754001012', 'married',  'Manzese Makorora',     '2000-10-08'],
            ['Simon Komba',          'male',   '1997-03-05', 'simon.komba@gmail.com',          '0754001013', 'single',   'Sinza',                '2014-05-20'],
            ['Theresia Malaba',      'female', '1989-10-16', 'theresia.malaba@gmail.com',      '0754001014', 'married',  'Tandale',              '2006-02-14'],
            ['Elijah Maro',          'male',   '1974-08-22', 'elijah.maro@gmail.com',          '0754001015', 'married',  'Manzese Ward 3',       '1994-08-05'],
            ['Naomi Kimaro',         'female', '1996-02-11', 'naomi.kimaro@gmail.com',         '0754001016', 'single',   'Manzese Shule',        '2013-01-19'],
            ['Isaac Mmbaga',         'male',   '1980-06-18', 'isaac.mmbaga@gmail.com',         '0754001017', 'married',  'Magomeni',             '1998-07-22'],
            ['Lydia Samweli',        'female', '1994-12-30', 'lydia.samweli@gmail.com',        '0754001018', 'single',   'Manzese Kivule',       '2011-09-11'],
            ['Benjamin Massawe',     'male',   '1969-04-07', 'benjamin.massawe@gmail.com',     '0754001019', 'married',  'Manzese Kwa Mtogole',  '1987-12-25'],
            ['Sarah Moshi',          'female', '1992-09-14', 'sarah.moshi@gmail.com',          '0754001020', 'married',  'Tandale',              '2009-03-07'],
            ['Joseph Mlangi',        'male',   '1986-11-29', 'joseph.mlangi@gmail.com',        '0754001021', 'married',  'Manzese Ward 4',       '2003-05-18'],
            ['Deborah Kiimba',       'female', '1999-07-23', 'deborah.kiimba@gmail.com',       '0754001022', 'single',   'Sinza',                '2016-08-14'],
            ['Andrew Mwalimu',       'male',   '1983-01-09', 'andrew.mwalimu@gmail.com',       '0754001023', 'married',  'Manzese Shule',        '2001-11-30'],
            ['Martha Chiwanga',      'female', '1977-05-14', 'martha.chiwanga@gmail.com',      '0754001024', 'widowed',  'Manzese Makorora',     '1995-04-12'],
            ['Stephen Ngailo',       'male',   '1991-08-27', 'stephen.ngailo@gmail.com',       '0754001025', 'single',   'Magomeni',             '2008-06-28'],
            ['Hannah Mushi',         'female', '1998-03-16', 'hannah.mushi@gmail.com',         '0754001026', 'single',   'Manzese Kivule',       '2015-09-03'],
            ['Caleb Massawe',        'male',   '1973-10-05', 'caleb.massawe@gmail.com',        '0754001027', 'married',  'Manzese Ward 3',       '1993-02-28'],
            ['Rebecca Matama',       'female', '1988-04-20', 'rebecca.matama@gmail.com',       '0754001028', 'divorced', 'Tandale',              '2005-07-16'],
            ['Thomas Msami',         'male',   '1994-06-13', 'thomas.msami@gmail.com',         '0754001029', 'single',   'Manzese Kwa Mtogole',  '2011-04-22'],
            ['Lynet Kilonzo',        'female', '1985-02-25', 'lynet.kilonzo@gmail.com',        '0754001030', 'married',  'Manzese Shule',        '2002-08-09'],
            ['Philip Mruma',         'male',   '1979-09-08', 'philip.mruma@gmail.com',         '0754001031', 'married',  'Magomeni',             '1997-10-14'],
            ['Charity Mwakasege',    'female', '1993-12-19', 'charity.mwakasege@gmail.com',    '0754001032', 'single',   'Sinza',                '2010-03-01'],
            ['Nehemiah Kianga',      'male',   '1968-07-15', 'nehemiah.kianga@gmail.com',      '0754001033', 'married',  'Manzese Makorora',     '1988-06-20'],
            ['Tabitha Mrema',        'female', '1990-01-22', 'tabitha.mrema@gmail.com',        '0754001034', 'married',  'Manzese Kivule',       '2007-12-15'],
            ['Abel Mwaseba',         'male',   '1984-05-31', 'abel.mwaseba@gmail.com',         '0754001035', 'married',  'Tandale',              '2001-09-08'],
            ['Priscilla Kapinga',    'female', '1997-08-06', 'priscilla.kapinga@gmail.com',    '0754001036', 'single',   'Manzese Ward 4',       '2014-07-27'],
            ['Moses Mwaipyana',      'male',   '1976-03-28', 'moses.mwaipyana@gmail.com',      '0754001037', 'married',  'Manzese Shule',        '1996-01-12'],
            ['Agnes Lema',           'female', '1982-11-11', 'agnes.lema@gmail.com',           '0754001038', 'married',  'Magomeni',             '2000-04-28'],
            ['Ezekiel Chacha',       'male',   '1989-06-04', 'ezekiel.chacha@gmail.com',       '0754001039', 'single',   'Manzese Kwa Mtogole',  '2006-11-19'],
            ['Judith Kimaro',        'female', '1995-09-27', 'judith.kimaro@gmail.com',        '0754001040', 'single',   'Sinza',                '2012-05-14'],
            ['Raphael Mbilinyi',     'male',   '1972-04-15', 'raphael.mbilinyi@gmail.com',     '0754001041', 'married',  'Manzese Makorora',     '1992-08-06'],
            ['Veronica Mgaya',       'female', '1986-07-18', 'veronica.mgaya@gmail.com',       '0754001042', 'married',  'Manzese Ward 3',       '2003-03-25'],
            ['Solomon Malindisa',    'male',   '1998-02-01', 'solomon.malindisa@gmail.com',    '0754001043', 'single',   'Tandale',              '2015-10-08'],
            ['Florence Kavishe',     'female', '1981-10-09', 'florence.kavishe@gmail.com',     '0754001044', 'widowed',  'Manzese Kivule',       '1999-06-17'],
            ['Cornelius Byaruhanga', 'male',   '1987-08-14', 'cornelius.b@gmail.com',          '0754001045', 'married',  'Magomeni',             '2004-02-22'],
            ['Leonida Rwegasha',     'female', '1992-05-03', 'leonida.rwegasha@gmail.com',     '0754001046', 'single',   'Manzese Shule',        '2009-08-31'],
            ['Barnabas Mgimba',      'male',   '1965-12-20', 'barnabas.mgimba@gmail.com',      '0754001047', 'married',  'Manzese Kwa Mtogole',  '1984-11-05'],
            ['Magdalena Mtui',       'female', '1999-04-12', 'magdalena.mtui@gmail.com',       '0754001048', 'single',   'Sinza',                '2016-04-23'],
            ['Timothy Mhagama',      'male',   '1993-01-07', 'timothy.mhagama@gmail.com',      '0754001049', 'married',  'Manzese Makorora',     '2010-01-15'],
            ['Perpetua Nkwabi',      'female', '1984-06-29', 'perpetua.nkwabi@gmail.com',      '0754001050', 'married',  'Tandale',              '2001-07-04'],
            ['Gideon Msemwa',        'male',   '1977-09-23', 'gideon.msemwa@gmail.com',        '0754001051', 'married',  'Manzese Ward 4',       '1997-03-18'],
            ['Doris Kitula',         'female', '1990-03-08', 'doris.kitula@gmail.com',         '0754001052', 'single',   'Magomeni',             '2007-09-12'],
            ['Zacharia Mwakimako',   'male',   '1971-11-17', 'zacharia.mwakimako@gmail.com',   '0754001053', 'married',  'Manzese Kivule',       '1991-12-30'],
            ['Beatrice Makwetta',    'female', '1995-07-31', 'beatrice.makwetta@gmail.com',    '0754001054', 'single',   'Manzese Shule',        '2012-08-17'],
            ['Hezekiah Malasa',      'male',   '1982-02-14', 'hezekiah.malasa@gmail.com',      '0754001055', 'married',  'Manzese Kwa Mtogole',  '2000-05-09'],
            ['Angeline Mwanga',      'female', '1988-10-26', 'angeline.mwanga@gmail.com',      '0754001056', 'married',  'Sinza',                '2005-10-21'],
            ['Zechariah Ndile',      'male',   '1996-05-19', 'zechariah.ndile@gmail.com',      '0754001057', 'single',   'Manzese Makorora',     '2013-06-07'],
            ['Felistas Kiwelu',      'female', '1980-08-04', 'felistas.kiwelu@gmail.com',      '0754001058', 'married',  'Tandale',              '1998-09-26'],
            ['Godlisten Mwakasendo', 'male',   '1991-04-28', 'godlisten.mwakasendo@gmail.com', '0754001059', 'single',   'Manzese Ward 3',       '2008-07-19'],
            ['Anitha Massawe',       'female', '1986-01-15', 'anitha.massawe@gmail.com',       '0754001060', 'divorced', 'Magomeni',             '2003-02-11'],
        ];

        $createdMembers = [];
        foreach ($membersRawData as $i => $row) {
            [$fullName, $gender, $dob, $email, $phone, $marital, $address, $baptismDate] = $row;
            $memberNumber = 'MNZ-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $user = User::where('email', $email)->first();
            $status = $i < 55 ? 'active' : ($i < 58 ? 'inactive' : 'pending');

            $member = Member::updateOrCreate(
                ['email' => $email],
                [
                    'user_id'                 => $user?->id,
                    'member_number'           => $memberNumber,
                    'full_name'               => $fullName,
                    'gender'                  => $gender,
                    'date_of_birth'           => $dob,
                    'phone'                   => $phone,
                    'marital_status'          => $marital,
                    'address'                 => $address . ', Dar es Salaam',
                    'baptism_date'            => $baptismDate,
                    'salvation_date'          => Carbon::parse($baptismDate)->subMonths(rand(2, 8)),
                    'status'                  => $status,
                    'emergency_contact_name'  => 'Ndugu ' . explode(' ', $fullName)[1],
                    'emergency_contact_phone' => '075' . rand(1000000, 9999999),
                    'approved_by'             => $superAdmin->id,
                    'approved_at'             => now()->subDays(rand(10, 500)),
                ]
            );
            $createdMembers[] = $member;
        }
        $this->command->info('   ✅ Wanachama ' . count($createdMembers) . ' wameundwa.');

        // ──────────────────────────────────────
        // 5. WEKA WANACHAMA KWENYE IDARA
        // ──────────────────────────────────────
        $this->command->info('🏛️  Inaweka wanachama kwenye idara...');

        $deptAssignments = [
            0 => ['leader' => 3,  'members' => [5, 10, 15, 22, 30, 38, 44]],
            1 => ['leader' => 4,  'members' => [8, 12, 19, 25, 31, 35, 41, 46, 50, 55]],
            2 => ['leader' => 6,  'members' => [13, 20, 27, 33, 39, 45, 51]],
            3 => ['leader' => 2,  'members' => [7, 14, 21, 28, 34, 40, 47, 52, 56]],
            4 => ['leader' => 9,  'members' => [16, 23, 29, 36, 42, 48, 53]],
            5 => ['leader' => 18, 'members' => [11, 24, 37, 43, 49, 54, 57]],
            6 => ['leader' => 0,  'members' => [1, 17, 26, 32, 58, 59]],
            7 => ['leader' => 7,  'members' => [15, 23, 31, 39, 47, 55]],
            8 => ['leader' => 13, 'members' => [19, 27, 35, 43, 51]],
            9 => ['leader' => 46, 'members' => [32, 40, 48, 56]],
        ];

        foreach ($deptAssignments as $deptIdx => $assignment) {
            if (!isset($departments[$deptIdx])) continue;
            $dept = $departments[$deptIdx];
            $leaderIdx = $assignment['leader'];
            if (isset($createdMembers[$leaderIdx])) {
                $createdMembers[$leaderIdx]->departments()->syncWithoutDetaching([
                    $dept->id => ['role' => 'leader', 'status' => 'active'],
                ]);
            }
            foreach ($assignment['members'] as $mi) {
                if (isset($createdMembers[$mi])) {
                    $createdMembers[$mi]->departments()->syncWithoutDetaching([
                        $dept->id => ['role' => 'member', 'status' => 'active'],
                    ]);
                }
            }
        }

        // ──────────────────────────────────────
        // 6. GIVING CATEGORIES
        // ──────────────────────────────────────
        $this->command->info('💰 Inaunda kategoria za kutoa...');
        $givingCats = [
            ['Zaka (Tithe)',            'Zaka ya kumi ya mapato',                       1],
            ['Sadaka ya Jumla',         'Sadaka za kawaida za ibada',                   2],
            ['Mfuko wa Ujenzi',         'Kukusanya fedha za jengo jipya la kanisa',     3],
            ['Mfuko wa Elimu',          'Msaada wa masomo kwa watoto wa kanisa',        4],
            ['Mfuko wa Afya',           'Msaada wa matibabu kwa wanachama wahitaji',    5],
            ['World Budget',            'Mchango wa kimataifa wa SDA',                  6],
            ['Sabbath School Offering', 'Mchango wa masomo ya Sabato',                  7],
            ['Investment',              'Mradi wa Investment wa kila mwaka',            8],
            ['Sadaka Maalum',           'Sadaka za matukio maalum',                     9],
        ];
        foreach ($givingCats as [$name, $desc, $order]) {
            DB::table('giving_categories')->updateOrInsert(
                ['name' => $name],
                ['description' => $desc, 'is_active' => true, 'order' => $order, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // ──────────────────────────────────────
        // 7. MATUKIO/IBADA (Events)
        // ──────────────────────────────────────
        $this->command->info('📅 Inaunda matukio na ibada...');

        $events = [];
        $startDate = Carbon::now()->subMonths(6)->startOfWeek(Carbon::SATURDAY);
        $currentDate = clone $startDate;
        while ($currentDate->lte(Carbon::now()->addWeeks(4))) {
            $event = Event::firstOrCreate(
                ['name' => 'Ibada ya Sabato', 'date' => $currentDate->format('Y-m-d')],
                ['type' => 'service', 'start_time' => '09:00:00', 'end_time' => '12:00:00']
            );
            $events[] = $event;

            $event2 = Event::firstOrCreate(
                ['name' => 'Shule ya Sabato', 'date' => $currentDate->format('Y-m-d')],
                ['type' => 'service', 'start_time' => '08:00:00', 'end_time' => '09:00:00']
            );
            $events[] = $event2;

            $wednesday = $currentDate->copy()->next(Carbon::WEDNESDAY);
            $event3 = Event::firstOrCreate(
                ['name' => 'Maombi ya Usiku (Jumatano)', 'date' => $wednesday->format('Y-m-d')],
                ['type' => 'meeting', 'start_time' => '18:00:00', 'end_time' => '20:00:00']
            );
            $events[] = $event3;

            $currentDate->addWeek();
        }

        $specialEvents = [
            ['Kampeni ya Injili - Wiki ya Uinjilisti',  'event',   Carbon::now()->subMonths(5)->format('Y-m-d'),          '18:00', '21:00'],
            ['Siku ya Familia (Family Day)',             'event',   Carbon::now()->subMonths(4)->format('Y-m-d'),          '10:00', '17:00'],
            ['Semina ya Ndoa na Familia',               'meeting', Carbon::now()->subMonths(3)->format('Y-m-d'),          '09:00', '16:00'],
            ['Kampeni ya Afya - ADRA',                  'event',   Carbon::now()->subMonths(3)->addDays(7)->format('Y-m-d'), '08:00', '14:00'],
            ['Siku ya Pathfinder',                      'event',   Carbon::now()->subMonths(2)->format('Y-m-d'),          '08:00', '17:00'],
            ['Kongamano la Vijana (AY Rally)',          'event',   Carbon::now()->subMonths(2)->addDays(14)->format('Y-m-d'), '09:00', '18:00'],
            ['Mkutano Mkuu wa Kanisa',                  'meeting', Carbon::now()->subMonths(1)->format('Y-m-d'),          '14:00', '17:00'],
            ['Sherehe ya Ubatizo',                      'event',   Carbon::now()->subWeeks(3)->format('Y-m-d'),           '15:00', '17:00'],
            ['Semina ya Elimu ya Biblia',               'meeting', Carbon::now()->addWeeks(2)->format('Y-m-d'),           '09:00', '16:00'],
            ['Kampeni ya Injili - Desemba',             'event',   Carbon::now()->addMonths(1)->format('Y-m-d'),          '18:00', '21:00'],
        ];
        foreach ($specialEvents as [$name, $type, $date, $start, $end]) {
            $event = Event::firstOrCreate(
                ['name' => $name, 'date' => $date],
                ['type' => $type, 'start_time' => $start . ':00', 'end_time' => $end . ':00']
            );
            $events[] = $event;
        }

        // ──────────────────────────────────────
        // 8. MAHUDHURIO (Attendance)
        // ──────────────────────────────────────
        $this->command->info('✅ Inaunda rekodi za mahudhurio...');

        $pastEvents = Event::where('date', '<', Carbon::now()->format('Y-m-d'))
            ->where('type', 'service')
            ->orderBy('date', 'desc')
            ->take(20)
            ->get();

        $activeMembers = collect($createdMembers)->filter(fn($m) => $m->status === 'active')->values();

        foreach ($pastEvents as $event) {
            $attendingCount = (int)($activeMembers->count() * (rand(60, 85) / 100));
            $attendingMembers = $activeMembers->random(min($attendingCount, $activeMembers->count()));
            foreach ($attendingMembers as $member) {
                $status = rand(1, 10) <= 8 ? 'present' : (rand(1, 2) === 1 ? 'late' : 'excused');
                DB::table('attendances')->updateOrInsert(
                    ['event_id' => $event->id, 'member_id' => $member->id],
                    [
                        'scanned_by' => $admin->id,
                        'scanned_at' => Carbon::parse($event->date)->format('Y-m-d') . ' ' . Carbon::parse($event->start_time)->format('H:i:s'),
                        'status'     => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // ──────────────────────────────────────
        // 9. FEDHA - Transactions (miezi 6)
        // ──────────────────────────────────────
        $this->command->info('💳 Inaunda rekodi za fedha...');

        $paymentMethods = ['Cash', 'M-Pesa', 'CRDB Bank', 'NMB Bank', 'Tigo Pesa', 'Airtel Money'];
        $txCount = 0;
        $txDate = Carbon::now()->subMonths(6)->startOfMonth();
        while ($txDate->lte(Carbon::now())) {
            if ($txDate->dayOfWeek === Carbon::SATURDAY) {
                DB::table('transactions')->insert([
                    'type' => 'income', 'category' => 'Zaka (Tithe)',
                    'amount' => rand(350000, 850000),
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'member_id' => null,
                    'description' => 'Zaka ya Sabato - ' . $txDate->format('d M Y'),
                    'transaction_date' => $txDate->format('Y-m-d'),
                    'reference_number' => 'INC-TITHE-' . str_pad(++$txCount, 5, '0', STR_PAD_LEFT),
                    'recorded_by' => $treasurer->id, 'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('transactions')->insert([
                    'type' => 'income', 'category' => 'Sadaka ya Jumla',
                    'amount' => rand(120000, 380000),
                    'payment_method' => 'Cash', 'member_id' => null,
                    'description' => 'Sadaka ya Ibada - ' . $txDate->format('d M Y'),
                    'transaction_date' => $txDate->format('Y-m-d'),
                    'reference_number' => 'INC-SAD-' . str_pad(++$txCount, 5, '0', STR_PAD_LEFT),
                    'recorded_by' => $treasurer->id, 'created_at' => now(), 'updated_at' => now(),
                ]);
                if (rand(1, 2) === 1) {
                    DB::table('transactions')->insert([
                        'type' => 'income', 'category' => 'Mfuko wa Ujenzi',
                        'amount' => rand(50000, 200000),
                        'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                        'member_id' => null,
                        'description' => 'Mchango wa Ujenzi - ' . $txDate->format('d M Y'),
                        'transaction_date' => $txDate->format('Y-m-d'),
                        'reference_number' => 'INC-UJE-' . str_pad(++$txCount, 5, '0', STR_PAD_LEFT),
                        'recorded_by' => $treasurer->id, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
            if ($txDate->day === 1) {
                $monthlyExpenses = [
                    ['Umeme',             rand(180000, 350000)],
                    ['Maji',              rand(30000,   80000)],
                    ['Usalama (Ulinzi)',   rand(150000, 200000)],
                    ['Usafiri wa Mchungaji', rand(80000, 150000)],
                ];
                foreach ($monthlyExpenses as [$cat, $amount]) {
                    DB::table('transactions')->insert([
                        'type' => 'expense', 'category' => $cat,
                        'amount' => $amount,
                        'payment_method' => rand(1, 2) === 1 ? 'M-Pesa' : 'CRDB Bank',
                        'member_id' => null,
                        'description' => $cat . ' - ' . $txDate->format('M Y'),
                        'transaction_date' => $txDate->format('Y-m-d'),
                        'reference_number' => 'EXP-' . strtoupper(substr($cat, 0, 3)) . '-' . str_pad(++$txCount, 5, '0', STR_PAD_LEFT),
                        'recorded_by' => $treasurer->id, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
            $txDate->addDay();
        }

        // ──────────────────────────────────────
        // 10. PLEDGES (Ahadi za Wanachama)
        // ──────────────────────────────────────
        $this->command->info('🤝 Inaunda ahadi za fedha...');

        $pledgeData = [
            [4,  1500000, 850000,  'Mfuko wa Ujenzi wa Kanisa',      Carbon::now()->subMonths(4), Carbon::now()->addMonths(8),  'active'],
            [6,  500000,  500000,  'Ujenzi wa Darasa la Sabbath School', Carbon::now()->subMonths(5), Carbon::now()->subMonths(1),  'completed'],
            [10, 2000000, 600000,  'Mfuko wa Ujenzi wa Kanisa',      Carbon::now()->subMonths(3), Carbon::now()->addMonths(9),  'active'],
            [14, 300000,  300000,  'Kompyuta za Ofisi ya Kanisa',    Carbon::now()->subMonths(6), Carbon::now()->subMonths(3),  'completed'],
            [18, 800000,  200000,  'Mfuko wa Ujenzi wa Kanisa',      Carbon::now()->subMonths(2), Carbon::now()->addMonths(10), 'active'],
            [22, 250000,  100000,  'Vifaa vya Muziki',               Carbon::now()->subMonths(1), Carbon::now()->addMonths(5),  'active'],
            [26, 1000000, 450000,  'Mfuko wa Ujenzi wa Kanisa',      Carbon::now()->subMonths(4), Carbon::now()->addMonths(8),  'active'],
            [30, 150000,  0,       'Vitabu vya Biblia',              Carbon::now()->subMonths(1), Carbon::now()->addMonths(2),  'active'],
            [34, 600000,  600000,  'Generator ya Kanisa',            Carbon::now()->subMonths(7), Carbon::now()->subMonths(2),  'completed'],
            [38, 400000,  150000,  'Mfuko wa Elimu',                 Carbon::now()->subMonths(2), Carbon::now()->addMonths(6),  'active'],
            [42, 3000000, 800000,  'Mfuko wa Ujenzi wa Kanisa',      Carbon::now()->subMonths(5), Carbon::now()->addMonths(7),  'active'],
            [46, 200000,  50000,   'Sadaka Maalum ya Krismasi',      Carbon::now()->subMonths(1), Carbon::now()->addMonths(1),  'active'],
        ];

        foreach ($pledgeData as [$mi, $amount, $paid, $purpose, $start, $end, $status]) {
            if (!isset($createdMembers[$mi])) continue;
            DB::table('pledges')->updateOrInsert(
                ['member_id' => $createdMembers[$mi]->id, 'purpose' => $purpose],
                [
                    'amount' => $amount, 'amount_paid' => $paid,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date'   => $end->format('Y-m-d'),
                    'status'     => $status,
                    'created_by' => $treasurer->id,
                    'created_at' => now(), 'updated_at' => now(),
                ]
            );
        }

        // ──────────────────────────────────────
        // 11. VIKUNDI VIDOGO (Small Groups)
        // ──────────────────────────────────────
        $this->command->info('👫 Inaunda vikundi vidogo...');

        $smallGroupsData = [
            ['Kijiji cha Manzese Ward 3',    $createdMembers[0]->id,  'Jumanne',  '17:30', 'Manzese Ward 3 - Nyumba ya Ndugu Kileo', [1, 4, 10, 16, 22, 28, 34, 40, 46, 52]],
            ['Kikundi cha Tandale',          $createdMembers[12]->id, 'Jumatano', '18:00', 'Tandale - Kanisa dogo',                  [2, 7, 13, 19, 25, 31, 37, 43, 49, 55]],
            ['Kikundi cha Magomeni',         $createdMembers[24]->id, 'Alhamisi', '18:30', 'Magomeni - Chumba cha Mkutano',          [3, 8, 14, 20, 26, 32, 38, 44, 50, 56]],
            ['Kikundi cha Sinza',            $createdMembers[36]->id, 'Ijumaa',   '17:00', 'Sinza - Nyumba ya Ndugu Mwanga',         [5, 9, 15, 21, 27, 33, 39, 45, 51, 57]],
            ['Kikundi cha Vijana - Central', $createdMembers[8]->id,  'Jumapili', '15:00', 'Kanisa Kuu - Ukumbi wa Vijana',          [11, 17, 23, 29, 35, 41, 47, 53, 58, 59]],
            ['Kikundi cha Wazee',            $createdMembers[46]->id, 'Jumatatu', '10:00', 'Kanisa Kuu - Chumba cha Wazee',          [6, 18, 30, 42, 48, 54]],
        ];

        foreach ($smallGroupsData as [$name, $leaderId, $day, $time, $location, $memberIdxList]) {
            $existing = DB::table('small_groups')->where('name', $name)->first();
            if (!$existing) {
                DB::table('small_groups')->insert([
                    'name' => $name, 'description' => 'Kikundi kidogo cha ibada - ' . $name,
                    'leader_id' => $leaderId, 'meeting_day' => $day,
                    'meeting_time' => $time . ':00', 'location' => $location,
                    'max_members' => 15, 'status' => 'active',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            $sg = DB::table('small_groups')->where('name', $name)->first();
            foreach ($memberIdxList as $mi) {
                if (!isset($createdMembers[$mi])) continue;
                DB::table('small_group_member')->updateOrInsert(
                    ['small_group_id' => $sg->id, 'member_id' => $createdMembers[$mi]->id],
                    ['role' => 'member', 'joined_at' => Carbon::now()->subDays(rand(30, 300))->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // ──────────────────────────────────────
        // 12. WAGENI (Visitors)
        // ──────────────────────────────────────
        $this->command->info('🙋 Inaunda rekodi za wageni...');

        $visitorsData = [
            ['Amina',   'Hassan',      '0755123456', null,                    Carbon::now()->subDays(45), 'Jirani',               'contacted'],
            ['John',    'Mwenda',      '0766234567', 'john.mwenda@gmail.com', Carbon::now()->subDays(38), 'Ndugu wa Mwanachama',  'contacted'],
            ['Fatuma',  'Salim',       '0777345678', null,                    Carbon::now()->subDays(30), 'Social Media',         'pending'],
            ['George',  'Nyabenda',    '0788456789', 'george.n@gmail.com',    Carbon::now()->subDays(25), 'Ndugu wa Mwanachama',  'member'],
            ['Maria',   'Josephine',   '0744567890', null,                    Carbon::now()->subDays(21), 'Matangazo ya Kampeni', 'pending'],
            ['Ali',     'Mwangi',      '0755678901', 'ali.mwangi@gmail.com',  Carbon::now()->subDays(18), 'Jirani',               'contacted'],
            ['Stella',  'Mwakasege',   null,         null,                    Carbon::now()->subDays(14), 'Matangazo ya Kampeni', 'pending'],
            ['David',   'Oloitiptip',  '0766789012', 'david.o@gmail.com',     Carbon::now()->subDays(10), 'Ndugu wa Mwanachama',  'pending'],
            ['Neema',   'Kiungo',      '0777890123', null,                    Carbon::now()->subDays(7),  'Online',               'pending'],
            ['Robert',  'Byamungu',    '0788901234', 'robert.b@gmail.com',    Carbon::now()->subDays(3),  'Jirani',               'pending'],
            ['Lucia',   'Mwangangi',   '0744012345', null,                    Carbon::now()->subDays(2),  'Social Media',         'pending'],
            ['Charles', 'Mwamko',      '0755012346', 'charles.m@gmail.com',   Carbon::now()->subDays(1),  'Matangazo ya Kampeni', 'pending'],
        ];

        foreach ($visitorsData as [$first, $last, $phone, $email, $visitDate, $howFound, $followUpStatus]) {
            DB::table('visitors')->updateOrInsert(
                ['first_name' => $first, 'last_name' => $last, 'visit_date' => $visitDate->format('Y-m-d')],
                [
                    'phone' => $phone, 'email' => $email,
                    'how_found_us' => $howFound,
                    'assigned_to_member_id' => $createdMembers[array_rand($createdMembers)]->id,
                    'follow_up_status' => $followUpStatus,
                    'notes' => $followUpStatus === 'contacted' ? 'Amewasiliana naye, anaonyesha nia.' : null,
                    'created_at' => now(), 'updated_at' => now(),
                ]
            );
        }

        // ──────────────────────────────────────
        // 13. MAOMBI (Prayer Requests)
        // ──────────────────────────────────────
        $this->command->info('🙏 Inaunda maombi...');

        $prayerRequests = [
            [5,  'Niombee kwa ugonjwa wa mama yangu - malaria kali',  Carbon::now()->subDays(30), 'answered', 'Mungu amemponya - asante kwa maombi'],
            [11, 'Nahitaji maombi kwa ajili ya kazi - natafuta ajira', Carbon::now()->subDays(25), 'answered', 'Nimepata kazi - Mungu ni mwaminifu!'],
            [17, 'Maombi kwa ajili ya mtoto wangu - masomo',           Carbon::now()->subDays(20), 'ongoing',  null],
            [23, 'Niombee kwa safari yangu ya biashara',               Carbon::now()->subDays(15), 'active',   null],
            [29, 'Maombi ya umoja wa familia yangu',                   Carbon::now()->subDays(12), 'ongoing',  null],
            [35, 'Niombee kwa ugonjwa wa moyo - hospitali',            Carbon::now()->subDays(10), 'active',   null],
            [41, 'Maombi kwa ajili ya kanisa - Roho Mtakatifu',        Carbon::now()->subDays(7),  'active',   null],
            [47, 'Niombee kwa uamuzi mkubwa wa maisha',                Carbon::now()->subDays(5),  'active',   null],
            [53, 'Maombi kwa ajili ya mtoto - ubatizo',                Carbon::now()->subDays(3),  'active',   null],
            [2,  'Maombi ya nguvu kwa huduma ya kanisa',               Carbon::now()->subDays(1),  'active',   null],
        ];

        foreach ($prayerRequests as [$mi, $request, $date, $status, $answer]) {
            if (!isset($createdMembers[$mi])) continue;
            DB::table('prayer_requests')->updateOrInsert(
                ['member_id' => $createdMembers[$mi]->id, 'request_date' => $date->format('Y-m-d')],
                [
                    'request' => $request, 'status' => $status,
                    'answer' => $answer, 'answered_at' => $answer ? now() : null,
                    'is_private' => rand(0, 1), 'created_by' => $pastor->id,
                    'created_at' => now(), 'updated_at' => now(),
                ]
            );
        }

        // ──────────────────────────────────────
        // 14. ZIARA ZA KICHUNGAJI (Pastoral Visits)
        // ──────────────────────────────────────
        $this->command->info('🚗 Inaunda ziara za kichungaji...');

        $visitTypes  = ['home', 'hospital', 'phone_call', 'office'];
        $visitPurposes = [
            'Kutembelea mwanachama mgonjwa',
            'Mazungumzo ya kichungaji - msaada wa kiroho',
            'Kutembelea familia mpya ya kanisa',
            'Kufuatilia mwanachama aliyekosekana ibadani',
            'Msaada wa hali ngumu ya kifedha',
            'Ushauri wa ndoa', 'Maandalizi ya ubatizo',
        ];
        for ($i = 0; $i < 25; $i++) {
            $mi = array_rand($createdMembers);
            DB::table('visits')->insert([
                'member_id'          => $createdMembers[$mi]->id,
                'visitor_id'         => $pastor->id,
                'visit_type'         => $visitTypes[array_rand($visitTypes)],
                'visit_date'         => Carbon::now()->subDays(rand(1, 150))->format('Y-m-d'),
                'purpose'            => $visitPurposes[array_rand($visitPurposes)],
                'notes'              => 'Ziara iliendelea vizuri. Familia inafurahi na imani imaimarika.',
                'outcome'            => rand(0, 1) ? 'Mwanachama ameimarika - huhitaji follow-up.' : null,
                'follow_up_required' => rand(0, 1),
                'created_by'         => $pastor->id,
                'created_at'         => now(), 'updated_at' => now(),
            ]);
        }

        // ──────────────────────────────────────
        // 15. MATANGAZO (Announcements)
        // ──────────────────────────────────────
        $this->command->info('📢 Inaunda matangazo...');

        $announcements = [
            ['Kampeni ya Injili - Imeanza!',    'Kampeni ya injili itaanza tarehe ' . Carbon::now()->addWeeks(2)->format('d M Y') . '. Wote mnaalikwa kushiriki.', Carbon::now()->subDays(3)],
            ['Mkutano wa Wazee',                'Wazee wa kanisa wanakutana Jumamosi ijayo baada ya ibada. Agenda: Mpango wa mwaka mpya.',                           Carbon::now()->subDays(5)],
            ['Semina ya Familia',               'Semina ya ndoa na familia itafanyika tarehe ' . Carbon::now()->addWeeks(3)->format('d M Y') . '. Wataalam watakuwepo.', Carbon::now()->subDays(7)],
            ['Mchango wa Ujenzi',               'Mfuko wa Ujenzi unakusanya. Target: TZS 50,000,000. Tumefikia 65% ya lengo!',                                        Carbon::now()->subDays(10)],
            ['Ubatizo Ujao',                    'Itakuwa na ubatizo tarehe ' . Carbon::now()->addWeeks(4)->format('d M Y') . '. Wanaotarajiwa kubatizwa ni 8.',       Carbon::now()->subDays(14)],
            ['Saa za Ofisi ya Kanisa',          'Ofisi inafungua Jumatatu-Ijumaa 8:00am-5:00pm. Msimamizi: Ndg. Samuel Kagera - 0754001003.',                         Carbon::now()->subDays(20)],
            ['Pathfinder Rally',                'Mkutano wa Pathfinder wa Mkoa utafanyika hapa tarehe ' . Carbon::now()->addMonths(1)->format('d M Y') . '.',          Carbon::now()->subDays(21)],
            ['Ahsante kwa Wasaidizi',           'Tunamshukuru sana Dada Grace Mhina kwa kuandaa chakula cha mkutano wa wiki iliyopita. Mungu akubariki!',              Carbon::now()->subDays(25)],
        ];

        foreach ($announcements as [$title, $body, $date]) {
            DB::table('announcements')->updateOrInsert(
                ['title' => $title],
                ['body' => $body, 'is_active' => true, 'created_by' => $admin->id, 'created_at' => $date, 'updated_at' => $date]
            );
        }

        // ──────────────────────────────────────
        // 16. ROSTER - Ratiba za Huduma
        // ──────────────────────────────────────
        $this->command->info('📋 Inaunda ratiba za huduma...');

        $rosterRoles    = ['Mhudumu (Usher)', 'Kwaya', 'Msomaji wa Biblia', 'Msaidizi wa Sala', 'Mwenyekiti wa Ibada', 'Piano/Keyboard', 'Ulinzi wa Watoto', 'Maombi ya Ufunguzi'];
        $upcomingEvents = Event::where('date', '>=', Carbon::now()->format('Y-m-d'))->where('type', 'service')->take(8)->get();

        foreach ($upcomingEvents as $event) {
            $cnt      = min(8, $activeMembers->count());
            $selected = $activeMembers->random($cnt);
            foreach ($selected as $k => $member) {
                DB::table('rosters')->updateOrInsert(
                    ['event_id' => $event->id, 'member_id' => $member->id],
                    ['role' => $rosterRoles[$k % count($rosterRoles)], 'status' => rand(0, 1) ? 'confirmed' : 'pending', 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // ──────────────────────────────────────
        // MUHTASARI
        // ──────────────────────────────────────
        $this->command->newLine();
        $this->command->info('════════════════════════════════════════════════');
        $this->command->info('  ✅ DATA ZA MFANO ZIMEPAKIWA KIKAMILIFU!');
        $this->command->info('════════════════════════════════════════════════');
        $this->command->info('  👥 Wanachama      : ' . count($createdMembers));
        $this->command->info('  🏛️  Idara           : ' . count($deptData));
        $this->command->info('  📅 Matukio/Ibada   : ' . Event::count());
        $this->command->info('  💰 Fedha (Tx)      : ' . DB::table('transactions')->count());
        $this->command->info('  🤝 Ahadi (Pledges) : ' . DB::table('pledges')->count());
        $this->command->info('  👫 Vikundi Vidogo  : ' . DB::table('small_groups')->count());
        $this->command->info('  🙋 Wageni          : ' . DB::table('visitors')->count());
        $this->command->info('  🙏 Maombi          : ' . DB::table('prayer_requests')->count());
        $this->command->info('  🚗 Ziara            : ' . DB::table('visits')->count());
        $this->command->info('════════════════════════════════════════════════');
        $this->command->info('  🔑 LOGIN DETAILS:');
        $this->command->info('  Super Admin : admin@manzesesda.com    | Admin@2025!');
        $this->command->info('  Mchungaji   : mchungaji@manzesesda.com | password');
        $this->command->info('  Hazina      : hazina@manzesesda.com   | password');
        $this->command->info('  Msimamizi   : msimamizi@manzesesda.com | password');
        $this->command->info('════════════════════════════════════════════════');
    }
}
