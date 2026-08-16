<?php

return [

    'label' => 'ນຳເຂົ້າ :label',

    'modal' => [

        'heading' => 'ນຳເຂົ້າ :label',

        'form' => [

            'file' => [
                'label' => 'ໄຟລ໌',
                'placeholder' => 'ອັບໂຫຼດໄຟລ໌ CSV',
            ],

            'columns' => [
                'label' => 'ຄໍລໍາ',
                'placeholder' => 'ເລືອກຄໍລໍາ',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'ດາວໂຫຼດຕົວຢ່າງໄຟລ໌ CSV',
            ],

            'import' => [
                'label' => 'ນຳເຂົ້າ',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ການນຳເຂົ້າສຳເລັດແລ້ວ',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ດາວໂຫຼດຂໍ້ມູນກ່ຽວກັບແຖວທີ່ບໍ່ສຳເລັດ|ດາວໂຫຼດຂໍ້ມູນກ່ຽວກັບແຖວທີ່ບໍ່ສຳເລັດ',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ໄຟລ໌ CSV ທີ່ອັບໂຫຼດໃຫຍ່ເກີນໄປ',
            'body' => 'ບໍ່ສາມາດນຳເຂົ້າຫຼາຍກວ່າ 1 ແຖວໃນຄັ້ງດຽວໄດ້|ບໍ່ສາມາດນຳເຂົ້າຫຼາຍກວ່າ :count ແຖວໃນຄັ້ງດຽວໄດ້',
        ],

        'started' => [
            'title' => 'ເລີ່ມຕົ້ນການນຳເຂົ້າຂໍ້ມູນ',
            'body' => 'ການນຳເຂົ້າໄດ້ເລີ່ມຕົ້ນແລ້ວ ແລະ 1 ລາຍການຈະຖືກປະມວນຜົນໃນພື້ນຫຼັງ|ການນຳເຂົ້າໄດ້ເລີ່ມຕົ້ນແລ້ວ ແລະ :count ລາຍການຈະຖືກປະມວນຜົນໃນພື້ນຫຼັງ',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'ຂໍ້ຜິດພາດໃນລະບົບ ກະລຸນາຕິດຕໍ່ຝ່າຍສະໜັບສະໜູນ',
    ],

];