# ຂຽນ Seeder ແປງຂໍ້ມູນຈາກ db_qa2025.sql

- **Date:** 2026-08-15
- **Status:** planned
- **Project:** ltvcqa

## ເປົ້າໝາຍ (Goal)

ຂຽນ Laravel seeder ທີ່ອ່ານຂໍ້ມູນຈິງຈາກ dump ເກົ່າ `ex_db/db_qa2025.sql` (9 ຕາຕະລາງ `tb_*`, ບໍ່ມີ FK, ອອກແບບບໍ່ normalize) ແລ້ວແປງ/ໂຫຼດເຂົ້າ schema ໃໝ່ທີ່ສ້າງໄວ້ແລ້ວ (`departments`, `academic_years`, `standards`, `indicators`, `basis_mains`, `basis_items`, `users`, `documents`, `document_files`, `reports`) ໃຫ້ຄົບ ແລະ ຖືກຕ້ອງ, ໂດຍບໍ່ນຳເອົາຂໍ້ມູນເປື້ອນ (plaintext password, ຮູບແບບວັນທີປົນກັນ) ຕິດຕາມໄປນຳ.

## Context ປັດຈຸບັນ

Schema ໃໝ່ຖືກສ້າງແລ້ວ (migrations ຮັນສຳເລັດ, ຖານຂໍ້ມູນວ່າງເປົ່າ) ພ້ອມ models ຄົບ:

- [app/Models/Department.php](../../app/Models/Department.php)
- [app/Models/AcademicYear.php](../../app/Models/AcademicYear.php)
- [app/Models/Standard.php](../../app/Models/Standard.php)
- [app/Models/Indicator.php](../../app/Models/Indicator.php)
- [app/Models/BasisMain.php](../../app/Models/BasisMain.php)
- [app/Models/BasisItem.php](../../app/Models/BasisItem.php)
- [app/Models/User.php](../../app/Models/User.php) — ມີ `username`, `department_id`, `last_login_at`, `HasRoles` (Shield)
- [app/Models/Document.php](../../app/Models/Document.php)
- [app/Models/DocumentFile.php](../../app/Models/DocumentFile.php)
- [app/Models/Report.php](../../app/Models/Report.php)

ຂໍ້ມູນເກົ່າຢູ່ [ex_db/db_qa2025.sql](../../ex_db/db_qa2025.sql) (865 ແຖວ), ບໍ່ມີ FK constraint, ບໍ່ມີ `.env`/DB ເກົ່າໃຫ້ query ໂດຍກົງ — ຕ້ອງອ່ານຈາກ `.sql` file.

### ຕາຕະລາງ/ຂໍ້ມູນເກົ່າ ແລະ ຈຳນວນແຖວ

| ຕາຕະລາງເກົ່າ | ຈຳນວນແຖວ | ໝາຍເຫດ |
|---|---|---|
| `tb_standard` | 10 | id ຕໍ່ເນື່ອງ 1-10 |
| `tb_indicator` | 54 | `standardID` FK-like → `tb_standard` |
| `tb_basismain` | 34 | `IndicatorID` FK-like → `tb_indicator`, `basisName` ເປັນ text ຄັ່ນຫຼາຍຂໍ້ດ້ວຍ `\n` |
| `tb_department` | 9 | |
| `tb_user` | 10 | `departmentID` FK-like → `tb_department`; ລະຫັດຜ່ານ bcrypt (`$2b$10$...`) ຍົກເວັ້ນ `userID=14` (`'a'/'a'` plaintext) |
| `tb_year` | 1 | ມີແຕ່ `(1, '2023-2024')` |
| `tb_document` | ~140 (ID ບໍ່ຕໍ່ເນື່ອງ, ມີຊ່ອງຫວ່າງ) | `userID`, `basisMainID`, `yearID` |
| `tb_file` | ~354 | `documentID` FK-like → `tb_document`; `fileNo`+`fileDate` ຈິງແລ້ວແມ່ນຄຸນສົມບັດຂອງໜັງສືອອກ (document-level) ບໍ່ແມ່ນຂອງໄຟລ໌ |
| `tb_reportqa` | 0 (ວ່າງເປົ່າ) | ບໍ່ຕ້ອງ seed |

**ໄຟລ໌ຕົວຈິງ (PDF ອ້າງອີງໃນ `fileLocation`) ບໍ່ມີຢູ່ໃນ repo** — ມີແຕ່ metadata ໃນ SQL, ບໍ່ມີ binary file ໃຫ້ copy.

## ແນວທາງ (Approach)

1. **ອ່ານ SQL ດ້ວຍ PHP ໂດຍກົງ** (ບໍ່ import ໃສ່ MySQL ຄືນ) — parse ສະເພາະ `INSERT INTO` statement ຂອງແຕ່ລະຕາຕະລາງດ້ວຍ regex/PHP tokenizer ງ່າຍໆ (ຫຼືໃຊ້ library ຂະໜາດນ້ອຍ) ເພາະ `.sql` ນີ້ເປັນ static file ໃນ repo, ບໍ່ແມ່ນ live DB connection.
2. **1 seeder ຫຼັກ** `LegacyQaDataSeeder` ຂຽນເປັນລຳດັບຂັ້ນຕອນຄືກັນກັບລຳດັບ FK dependency (standards → indicators → basis_mains → basis_items → departments → users → academic_years → documents → document_files), ໃຊ້ `id map array` (`old_id => new_id`) ໃນແຕ່ລະຂັ້ນຕອນ ເພື່ອຜູກ FK ຂອງຂັ້ນຕອນຕໍ່ໄປ.
3. **basis_items**: split `tb_basismain.basisName` ດ້ວຍ `\n`, ຕັດ bullet character (`-`, `	-`) ແລະ whitespace ຫົວແຖວ, ຖິ້ມແຖວຫວ່າງ, ໃສ່ `order` ຕາມລຳດັບໃນ array.
4. **users**: ໃຊ້ `tb_user.userName` ເປັນ `username`, migrate `password` hash ກົງໆ (bcrypt เข้ากันได้), **ຍົກເວັ້ນ `userID=14`** (plaintext) — ບໍ່ seed ບັນຊີນີ້ (ບັນທຶກເປັນ log/comment ໃນ seeder ວ່າເປັນຫຍັງ). ບໍ່ migrate `email` (ບໍ່ມີໃນລະບົບເກົ່າ). `log` (string ປົນຮູບແບບ) → ບໍ່ migrate ເຂົ້າ `last_login_at` (ຂໍ້ມູນບໍ່ໜ້າເຊື່ອຖືພຽງພໍທີ່ຈະ parse), ປ່ອຍ `null`.
5. **documents**: `fileNo` (ຈາກ `tb_file` ແຖວທຳອິດຂອງແຕ່ລະ `documentID`, ຖ້າມີຫຼາຍໄຟລ໌ຄ່າ `fileNo` ຄືກັນທຸກແຖວ — ຢືນຢັນຕອນຂຽນ script) → `reference_no`; `fileDate` → `issued_date`.
6. **document_files**: 1:1 map ຈາກ `tb_file`, `fileLocation`/`fileName` → `path`/`original_name`, `fileSize` → `size`, `fileType` → `mime_type`, `disk` = `'public'` (ຄ່າ default, ໄຟລ໌ຕົວຈິງບໍ່ໄດ້ copy ມານຳ — ເປັນພຽງ metadata ອ້າງອີງ).
7. ໃສ່ໃນ `DatabaseSeeder::run()` ຫຼັງ default User factory call, ຫຼືແທນທີ່ (ຕັດສິນໃຈຕອນຂຽນ code — ຄວນຖາມຜູ້ໃຊ້ວ່າຢາກໃຫ້ replace default admin user ບໍ່).
8. Wrap ທັງໝົດໃນ DB transaction ດຽວ ເພື່ອໃຫ້ seed ລົ້ມເຫຼວແລ້ວ rollback ໝົດ ບໍ່ຄ້າງຂໍ້ມູນເຄິ່ງໆກາງໆ.

## ຂັ້ນຕອນ (Steps)

1. ຂຽນ helper class/function ອ່ານ `.sql` file → array ຂອງແຕ່ລະ `INSERT INTO tb_xxx VALUES (...)` (ຄົງທົນຕໍ່ comma/quote ພາຍໃນຂໍ້ຄວາມ, escaped quote `\'`).
2. ຂຽນ `database/seeders/LegacyQaDataSeeder.php`:
   - Standards
   - Indicators (map `standardID` → `standard_id`)
   - BasisMains + BasisItems (split `basisName`)
   - Departments
   - Users (skip `userID=14`, map `departmentID` → `department_id`)
   - AcademicYears (1 ແຖວ, ຕັ້ງ `is_active = true`)
   - Documents (map `userID`, `basisMainID`, `yearID`; ດຶງ `reference_no`/`issued_date` ຈາກ `tb_file` ຫົວແຖວທຳອິດຂອງແຕ່ລະ document)
   - DocumentFiles (map `documentID`)
3. ຂຽນ Pest test ຫຼື tinker smoke-check ຢືນຢັນຈຳນວນແຖວຫຼັງ seed ກົງກັບຕົ້ນສະບັບ (ຫັກ 1 user ທີ່ຂ້າມ) ແລະ ບໍ່ມີ orphan FK.
4. ຮັນ `php artisan db:seed --class=LegacyQaDataSeeder` ໃສ່ຖານຂໍ້ມູນ dev, ກວດຜົນດ້ວຍ `mcp__laravel-boost__database-query`.
5. `vendor/bin/pint --dirty` ຫຼັງຂຽນ code ແລ້ວ.

## Files ທີ່ຈະຖືກກະທົບ

- `database/seeders/LegacyQaDataSeeder.php` — ໃໝ່, seeder ຫຼັກ
- `database/seeders/DatabaseSeeder.php` — ເອີ້ນ `LegacyQaDataSeeder` ຕໍ່ຈາກ/ແທນ default user
- (ອາດຕ້ອງການ) `database/seeders/Concerns/ParsesLegacySqlDump.php` ຫຼື helper ຄ້າຍກັນ ສຳລັບ parse `.sql`
- `ex_db/db_qa2025.sql` — ອ່ານຢ່າງດຽວ, ບໍ່ແກ້ໄຂ
- `tests/Feature/LegacyQaDataSeederTest.php` (ຖ້າຕ້ອງການ automated verification)

## ຄວາມສ່ຽງ / ຄຳຖາມທີ່ຍັງເປີດ (Risks / Open Questions)

- **ໄຟລ໌ຕົວຈິງ (PDF) ບໍ່ມີໃນ repo** — `document_files.path` ຈະຊີ້ໄປຫາໄຟລ໌ທີ່ບໍ່ມີຢູ່ຈິງ. ຕ້ອງຕົກລົງກັນວ່າ: (ກ) seed metadata ໄປກ່ອນແລ້ວມາອັບໂຫຼດໄຟລ໌ຈິງພາຍຫຼັງ, ຫຼື (ຂ) ຂ້າມການ seed `document_files` ໄປເລີຍ.
- **`tb_document` ID ຂາດຊ່ວງ** (ຫຼັກຖານວ່າມີ record ຖືກລຶບໃນລະບົບເກົ່າ) — ບໍ່ກະທົບ seeder ເພາະ id ໃໝ່ຈະ auto-increment ເອງ, ແຕ່ຕ້ອງໃຊ້ id map ບໍ່ແມ່ນອີງ id ຕົງໆ.
- **`fileNo`/`fileDate` ອາດຕ່າງກັນລະຫວ່າງ file ໃນ document ດຽວກັນ** — ຕ້ອງກວດຂໍ້ມູນຈິງກ່ອນ (ຕອນນີ້ສົມມຸດວ່າຄືກັນໝົດ, ອາດຜິດ) — ຄວນ verify ຕອນຂຽນ script ຈິງ.
- ຄຳຖາມ: `DatabaseSeeder` ຄວນຄົງ default `test@example.com` user ໄວ້ນຳບໍ່, ຫຼືປ່ຽນເປັນສ້າງບັນຊີ admin ຈິງດ້ວຍ `username` ແທນ?
- ຄຳຖາມ: `tb_user.userID=14` (plaintext password) ຂ້າມໄປເລີຍ ຫຼືຢາກ seed ນຳແຕ່ hash password ໃໝ່ໃຫ້ (ຮັກສາ record ໄວ້ ແຕ່ປອດໄພຂຶ້ນ)?
