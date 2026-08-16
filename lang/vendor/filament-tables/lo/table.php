<?php

return [

    'column_manager' => [

        'heading' => 'ຄໍລໍາ',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'ສະແດງໜ້ອຍກວ່ານີ້ :count ລາຍການ',
                'expand_list' => 'ສະແດງອີກ :count ລາຍການ',
            ],

            'more_list_items' => 'ແລະອີກ :count ລາຍການ',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'ເລືອກ/ບໍ່ເລືອກລາຍການທັງໝົດສຳລັບການດຳເນີນການເປັນກຸ່ມ',
        ],

        'bulk_select_record' => [
            'label' => 'ເລືອກ/ບໍ່ເລືອກລາຍການ :key ສຳລັບການດຳເນີນການເປັນກຸ່ມ',
        ],

        'bulk_select_group' => [
            'label' => 'ເລືອກ/ບໍ່ເລືອກກຸ່ມ :title ສຳລັບການດຳເນີນການເປັນກຸ່ມ',
        ],

        'search' => [
            'label' => 'ຄົ້ນຫາ',
            'placeholder' => 'ຄົ້ນຫາ',
            'indicator' => 'ຄົ້ນຫາ',
        ],

    ],

    'summary' => [

        'heading' => 'ສະຫຼຸບ',

        'subheadings' => [
            'all' => ':label ທຸກລາຍການ',
            'group' => 'ສະຫຼຸບ :group',
            'page' => 'ໜ້ານີ້',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'ສະເລ່ຍ',
            ],

            'count' => [
                'label' => 'ຈຳນວນ',
            ],

            'sum' => [
                'label' => 'ລວມ',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'ເລີກການຈັດລຳດັບລາຍການ',
        ],

        'enable_reordering' => [
            'label' => 'ຈັດລຳດັບລາຍການ',
        ],

        'filter' => [
            'label' => 'ຕົວກອງ',
        ],

        'group' => [
            'label' => 'ຈັດກຸ່ມ',
        ],

        'open_bulk_actions' => [
            'label' => 'ການດຳເນີນການເປັນກຸ່ມ',
        ],

        'column_manager' => [
            'label' => 'ສະຫຼັບຄໍລໍາ',
        ],

    ],

    'empty' => [

        'heading' => 'ບໍ່ມີ :model',

        'description' => 'ເພີ່ມ :model ເພື່ອເລີ່ມຕົ້ນ',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'ໃຊ້ຕົວກອງ',
            ],

            'remove' => [
                'label' => 'ລຶບຕົວກອງ',
            ],

            'remove_all' => [
                'label' => 'ລຶບຕົວກອງທັງໝົດ',
                'tooltip' => 'ລຶບຕົວກອງທັງໝົດ',
            ],

            'reset' => [
                'label' => 'ຣີເຊັດ',
            ],

        ],

        'heading' => 'ຕົວກອງ',

        'indicator' => 'ຕົວກອງທີ່ໃຊ້ງານຢູ່',

        'multi_select' => [
            'placeholder' => 'ທັງໝົດ',
        ],

        'select' => [
            'placeholder' => 'ທັງໝົດ',
        ],

        'trashed' => [

            'label' => 'ລາຍການທີ່ຖືກລຶບ',

            'only_trashed' => 'ລາຍການທີ່ຖືກລຶບເທົ່ານັ້ນ',

            'with_trashed' => 'ພ້ອມລາຍການທີ່ຖືກລຶບ',

            'without_trashed' => 'ໂດຍບໍ່ລວມລາຍການທີ່ຖືກລຶບ',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'ຈັດກຸ່ມຕາມ',
                'placeholder' => 'ຈັດກຸ່ມຕາມ',
            ],

            'direction' => [

                'label' => 'ຮຽງລຳດັບກຸ່ມ',

                'options' => [
                    'asc' => 'ຮຽງຈາກໜ້ອຍໄປຫຼາຍ',
                    'desc' => 'ຮຽງຈາກຫຼາຍໄປໜ້ອຍ',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ລາກລາຍການ ແລະ ວາງໃນລຳດັບ',

    'selection_indicator' => [

        'selected_count' => 'ເລືອກ 1 ລາຍການ|ເລືອກ :count ລາຍການ',

        'actions' => [

            'select_all' => [
                'label' => 'ເລືອກທັງ :count ລາຍການ',
            ],

            'deselect_all' => [
                'label' => 'ຍົກເລີກການເລືອກທັງໝົດ',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'ຮຽງລຳດັບໂດຍ',
            ],

            'direction' => [

                'label' => 'ທິດທາງການຮຽງລຳດັບ',

                'options' => [
                    'asc' => 'ຮຽງຈາກໜ້ອຍໄປຫຼາຍ',
                    'desc' => 'ຮຽງຈາກຫຼາຍໄປໜ້ອຍ',
                ],

            ],

        ],

    ],

];