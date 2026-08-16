<?php

return [

    'label' => 'ສ້າງຄຳຄົ້ນ',

    'form' => [

        'operator' => [
            'label' => 'ດຳເນີນການ',
        ],

        'or_groups' => [

            'label' => 'ກຸ່ມ',

            'block' => [
                'label' => 'ແຍກເງື່ອນໄຂ (ຫຼື)',
                'or' => 'ຫຼື',
            ],

        ],

        'rules' => [

            'label' => 'ເງື່ອນໄຂ',

            'item' => [
                'and' => 'ແລະ',
            ],

        ],

    ],

    'no_rules' => '(ບໍ່ມີເງື່ອນໄຂ)',

    'item_separators' => [
        'and' => 'ແລະ',
        'or' => 'ຫຼື',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'ມີຂໍ້ມູນ',
                'inverse' => 'ວ່າງ',
            ],

            'summary' => [
                'direct' => ':attribute ມີຂໍ້ມູນ',
                'inverse' => ':attribute ວ່າງ',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'ເປັນຈິງ',
                    'inverse' => 'ເປັນເທັດ',
                ],

                'summary' => [
                    'direct' => ':attribute ເປັນຈິງ',
                    'inverse' => ':attribute ເປັນເທັດ',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'ຫຼັງຈາກ',
                    'inverse' => 'ບໍ່ເກີນ',
                ],

                'summary' => [
                    'direct' => ':attribute ຫຼັງຈາກ :date',
                    'inverse' => ':attribute ບໍ່ເກີນ :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'ກ່ອນ',
                    'inverse' => 'ຕັ້ງແຕ່',
                ],

                'summary' => [
                    'direct' => ':attribute ກ່ອນ :date',
                    'inverse' => ':attribute ຕັ້ງແຕ່ :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'ວັນທີ',
                    'inverse' => 'ບໍ່ແມ່ນວັນທີ',
                ],

                'summary' => [
                    'direct' => ':attribute ເປັນ :date',
                    'inverse' => ':attribute ບໍ່ແມ່ນ :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'ເດືອນ',
                    'inverse' => 'ບໍ່ແມ່ນເດືອນ',
                ],

                'summary' => [
                    'direct' => ':attribute ເປັນ :month',
                    'inverse' => ':attribute ບໍ່ແມ່ນ :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'ປີ',
                    'inverse' => 'ບໍ່ແມ່ນປີ',
                ],

                'summary' => [
                    'direct' => ':attribute ປີ :year',
                    'inverse' => ':attribute ບໍ່ແມ່ນປີ :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'ວັນ',
                ],

                'month' => [
                    'label' => 'ເດືອນ',
                ],

                'year' => [
                    'label' => 'ປີ',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'ເທົ່າກັບ',
                    'inverse' => 'ບໍ່ເທົ່າກັບ',
                ],

                'summary' => [
                    'direct' => ':attribute ເທົ່າກັບ :number',
                    'inverse' => ':attribute ບໍ່ເທົ່າກັບ :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'ບໍ່ເກີນ',
                    'inverse' => 'ຫຼາຍກວ່າ',
                ],

                'summary' => [
                    'direct' => ':attribute ບໍ່ເກີນ :number',
                    'inverse' => ':attribute ຫຼາຍກວ່າ :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'ຢ່າງໜ້ອຍ',
                    'inverse' => 'ໜ້ອຍກວ່າ',
                ],

                'summary' => [
                    'direct' => ':attribute ຢ່າງໜ້ອຍ :number',
                    'inverse' => ':attribute ໜ້ອຍກວ່າ :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'ຄ່າສະເລ່ຍ',
                    'summary' => ':attribute ສະເລ່ຍ',
                ],

                'max' => [
                    'label' => 'ສູງສຸດ',
                    'summary' => ':attribute ສູງສຸດ',
                ],

                'min' => [
                    'label' => 'ຕໍ່າສຸດ',
                    'summary' => ':attribute ຕໍ່າສຸດ',
                ],

                'sum' => [
                    'label' => 'ລວມ',
                    'summary' => ':attribute ລວມ',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'ຜົນລວມ',
                ],

                'number' => [
                    'label' => 'ຕົວເລກ',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'ມີ',
                    'inverse' => 'ບໍ່ມີ',
                ],

                'summary' => [
                    'direct' => 'ມີ :count :relationship',
                    'inverse' => 'ບໍ່ມີ :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'ມີສູງສຸດ',
                    'inverse' => 'ມີຫຼາຍກວ່າ',
                ],

                'summary' => [
                    'direct' => 'ມີສູງສຸດ :count :relationship',
                    'inverse' => 'ມີຫຼາຍກວ່າ :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'ມີຂັ້ນຕໍ່າ',
                    'inverse' => 'ມີໜ້ອຍກວ່າ',
                ],

                'summary' => [
                    'direct' => 'ມີຂັ້ນຕໍ່າ :count :relationship',
                    'inverse' => 'ມີໜ້ອຍກວ່າ :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'ວ່າງ',
                    'inverse' => 'ບໍ່ວ່າງ',
                ],

                'summary' => [
                    'direct' => ':relationship ວ່າງ',
                    'inverse' => ':relationship ບໍ່ວ່າງ',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'ກ່ຽວກັບ',
                        'inverse' => 'ບໍ່ກ່ຽວກັບ',
                    ],

                    'multiple' => [
                        'direct' => 'ມີ',
                        'inverse' => 'ບໍ່ມີ',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship ແມ່ນ :values',
                        'inverse' => ':relationship ບໍ່ແມ່ນ :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship ມີ :values',
                        'inverse' => ':relationship ບໍ່ມີ :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ຫຼື ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'ຄ່າ',
                    ],

                    'values' => [
                        'label' => 'ຄ່າ',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'ຈຳນວນ',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'ແມ່ນ',
                    'inverse' => 'ບໍ່ແມ່ນ',
                ],

                'summary' => [
                    'direct' => ':attribute :values',
                    'inverse' => ':attribute ບໍ່ແມ່ນ :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ຫຼື ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'ຄ່າ',
                    ],

                    'values' => [
                        'label' => 'ຄ່າ',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'ມີຄຳວ່າ',
                    'inverse' => 'ບໍ່ມີຄຳວ່າ',
                ],

                'summary' => [
                    'direct' => ':attribute ມີຄຳວ່າ :text',
                    'inverse' => ':attribute ບໍ່ມີຄຳວ່າ :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'ລົງທ້າຍດ້ວຍ',
                    'inverse' => 'ບໍ່ລົງທ້າຍດ້ວຍ',
                ],

                'summary' => [
                    'direct' => ':attribute ລົງທ້າຍດ້ວຍ :text',
                    'inverse' => ':attribute ບໍ່ລົງທ້າຍດ້ວຍ :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'ເທົ່າກັບ',
                    'inverse' => 'ບໍ່ເທົ່າກັບ',
                ],

                'summary' => [
                    'direct' => ':attribute ເທົ່າກັບ :text',
                    'inverse' => ':attribute ບໍ່ເທົ່າກັບ :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'ຂຶ້ນຕົ້ນດ້ວຍ',
                    'inverse' => 'ບໍ່ຂຶ້ນຕົ້ນດ້ວຍ',
                ],

                'summary' => [
                    'direct' => ':attribute ຂຶ້ນຕົ້ນດ້ວຍ :text',
                    'inverse' => ':attribute ບໍ່ຂຶ້ນຕົ້ນດ້ວຍ :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'ຂໍ້ຄວາມ',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'ເພີ່ມເງື່ອນໄຂ',
        ],

        'add_rule_group' => [
            'label' => 'ເພີ່ມກຸ່ມເງື່ອນໄຂ',
        ],

    ],

];