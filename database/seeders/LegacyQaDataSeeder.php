<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use App\Models\User;
use Database\Seeders\Concerns\ImportsLegacyDump;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PDO;

class LegacyQaDataSeeder extends Seeder
{
    use ImportsLegacyDump;

    /**
     * userID=14 in the legacy dump ('a' / 'a') stores a plaintext password
     * and has no documents attached to it — it is a leftover test account.
     */
    private const SKIPPED_LEGACY_USER_IDS = [14];

    public function run(): void
    {
        $legacy = $this->legacyPdo();

        DB::transaction(function () use ($legacy): void {
            $framework = QaFramework::create([
                'name' => 'ຊຸດມາດຕະຖານ 2023',
                'status' => 'published',
            ]);

            $standardMap = $this->seedStandards($legacy, $framework);
            $indicatorMap = $this->seedIndicators($legacy, $standardMap);
            $basisMainMap = $this->seedBasisMains($legacy, $indicatorMap);
            $departmentMap = $this->seedDepartments($legacy);
            $userMap = $this->seedUsers($legacy, $departmentMap);
            $yearMap = $this->seedAcademicYears($legacy, $framework);
            $documentMap = $this->seedDocuments($legacy, $userMap, $basisMainMap, $yearMap);
            $this->seedDocumentFiles($legacy, $documentMap);
        });
    }

    /**
     * @return array<int, int> legacy standardID => new standards.id
     */
    private function seedStandards(PDO $legacy, QaFramework $framework): array
    {
        $map = [];

        $rows = $legacy->query('SELECT * FROM tb_standard ORDER BY standardID')->fetchAll();

        foreach ($rows as $order => $row) {
            $standard = Standard::create([
                'framework_id' => $framework->id,
                'name' => trim($row['standardName']),
                'order' => $order + 1,
            ]);

            $map[(int) $row['standardID']] = $standard->id;
        }

        return $map;
    }

    /**
     * @param  array<int, int>  $standardMap
     * @return array<int, int> legacy IndicatorID => new indicators.id
     */
    private function seedIndicators(PDO $legacy, array $standardMap): array
    {
        $map = [];
        $orderCounters = [];

        $rows = $legacy->query('SELECT * FROM tb_indicator ORDER BY standardID, IndicatorID')->fetchAll();

        foreach ($rows as $row) {
            $standardId = $standardMap[(int) $row['standardID']] ?? null;

            if ($standardId === null) {
                continue;
            }

            $orderCounters[$standardId] = ($orderCounters[$standardId] ?? 0) + 1;

            $indicator = Indicator::create([
                'standard_id' => $standardId,
                'name' => trim($row['IndicatorName']),
                'order' => $orderCounters[$standardId],
            ]);

            $map[(int) $row['IndicatorID']] = $indicator->id;
        }

        return $map;
    }

    /**
     * Each `\n`-separated bullet line under a legacy basisMainID becomes its
     * own BasisMain, so upload progress can be tracked and reported per line
     * within an indicator rather than for the group as a whole. Legacy
     * documents (submitted once per basisMainID) attach to the first line's
     * BasisMain only — the raw data does not say which bullet the original
     * evidence bundle covered.
     *
     * @param  array<int, int>  $indicatorMap
     * @return array<int, int> legacy basisMainID => first line's basis_mains.id
     */
    private function seedBasisMains(PDO $legacy, array $indicatorMap): array
    {
        $map = [];
        $orderCounters = [];

        $rows = $legacy->query('SELECT * FROM tb_basismain ORDER BY IndicatorID, basisMainID')->fetchAll();

        foreach ($rows as $row) {
            $indicatorId = $indicatorMap[(int) $row['IndicatorID']] ?? null;

            if ($indicatorId === null) {
                continue;
            }

            foreach ($this->splitBasisName($row['basisName']) as $lineIndex => $title) {
                $orderCounters[$indicatorId] = ($orderCounters[$indicatorId] ?? 0) + 1;

                $basisMain = BasisMain::create([
                    'indicator_id' => $indicatorId,
                    'title' => $title,
                    'order' => $orderCounters[$indicatorId],
                ]);

                if ($lineIndex === 0) {
                    $map[(int) $row['basisMainID']] = $basisMain->id;
                }
            }
        }

        return $map;
    }

    /**
     * Splits the legacy `\n`-separated bullet text into individual titles,
     * one per BasisMain.
     *
     * @return list<string>
     */
    private function splitBasisName(string $basisName): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $basisName);

        $items = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/^-\s*/u', '', trim($line)));

            if ($line !== '') {
                $items[] = $line;
            }
        }

        return $items === [] ? [trim($basisName)] : $items;
    }

    /**
     * @return array<int, int> legacy departmentID => new departments.id
     */
    private function seedDepartments(PDO $legacy): array
    {
        $map = [];

        $rows = $legacy->query('SELECT * FROM tb_department ORDER BY departmentID')->fetchAll();

        foreach ($rows as $row) {
            $department = Department::create([
                'name' => trim($row['departmentName']),
            ]);

            $map[(int) $row['departmentID']] = $department->id;
        }

        return $map;
    }

    /**
     * @param  array<int, int>  $departmentMap
     * @return array<int, int> legacy userID => new users.id
     */
    private function seedUsers(PDO $legacy, array $departmentMap): array
    {
        $map = [];

        $rows = $legacy->query('SELECT * FROM tb_user ORDER BY userID')->fetchAll();

        foreach ($rows as $row) {
            $legacyId = (int) $row['userID'];

            if (in_array($legacyId, self::SKIPPED_LEGACY_USER_IDS, true)) {
                continue;
            }

            $user = User::create([
                'name' => $row['userName'],
                'username' => $row['userName'],
                'password' => $row['userName'] === 'admin' ? 'admin123' : $row['password'],
                'department_id' => $departmentMap[(int) $row['departmentID']] ?? null,
            ]);

            $map[$legacyId] = $user->id;
        }

        return $map;
    }

    /**
     * @return array<int, int> legacy yearID => new academic_years.id
     */
    private function seedAcademicYears(PDO $legacy, QaFramework $framework): array
    {
        $map = [];

        $rows = $legacy->query('SELECT * FROM tb_year ORDER BY yearID')->fetchAll();

        foreach ($rows as $row) {
            $year = AcademicYear::create([
                'framework_id' => $framework->id,
                'name' => trim($row['YearName']),
                'is_active' => true,
            ]);

            $map[(int) $row['yearID']] = $year->id;
        }

        return $map;
    }

    /**
     * @param  array<int, int>  $userMap
     * @param  array<int, int>  $basisMainMap
     * @param  array<int, int>  $yearMap
     * @return array<int, int> legacy documentID => new documents.id
     */
    private function seedDocuments(PDO $legacy, array $userMap, array $basisMainMap, array $yearMap): array
    {
        $map = [];

        $rows = $legacy->query('SELECT * FROM tb_document ORDER BY documentID')->fetchAll();

        foreach ($rows as $row) {
            $legacyId = (int) $row['documentID'];
            $userId = $userMap[(int) $row['userID']] ?? null;
            $basisMainId = $basisMainMap[(int) $row['basisMainID']] ?? null;
            $yearId = $yearMap[(int) $row['yearID']] ?? null;

            if ($userId === null || $basisMainId === null || $yearId === null) {
                continue;
            }

            $document = Document::create([
                'user_id' => $userId,
                'basis_main_id' => $basisMainId,
                'academic_year_id' => $yearId,
            ]);

            $map[$legacyId] = $document->id;
        }

        return $map;
    }

    /**
     * Every `tb_file` row is a separately-numbered physical document — its
     * `fileNo`/`fileDate` belong to that file, not to the parent `tb_document`
     * grouping (see docs/adr/0002-reference-number-belongs-to-document-file.md).
     *
     * @param  array<int, int>  $documentMap
     */
    private function seedDocumentFiles(PDO $legacy, array $documentMap): void
    {
        $rows = $legacy->query('SELECT * FROM tb_file ORDER BY fileID')->fetchAll();

        foreach ($rows as $row) {
            $documentId = $documentMap[(int) $row['documentID']] ?? null;

            if ($documentId === null) {
                continue;
            }

            DocumentFile::create([
                'document_id' => $documentId,
                'reference_no' => $row['fileNo'],
                'issued_date' => $row['fileDate'],
                'disk' => 'local',
                'path' => $row['fileLocation'],
                'original_name' => $row['fileName'],
                'mime_type' => $row['fileType'],
                'size' => (int) $row['fileSize'],
            ]);
        }
    }
}
