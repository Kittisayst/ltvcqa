<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'ທຳສຳເນົາ',
            ],

            'add' => [
                'label' => 'ເພີ່ມໄປຍັງ :label',
            ],

            'add_between' => [
                'label' => 'ແທລກລະຫວ່າງບລັອກ',
            ],

            'delete' => [
                'label' => 'ລຶບ',
            ],

            'reorder' => [
                'label' => 'ຍ້າຍ',
            ],

            'move_down' => [
                'label' => 'ເລື່ອນລົງ',
            ],

            'move_up' => [
                'label' => 'ເລື່ອນຂຶ້ນ',
            ],

            'collapse' => [
                'label' => 'ຫຍໍ້',
            ],

            'expand' => [
                'label' => 'ຂະຫຍາຍ',
            ],

            'collapse_all' => [
                'label' => 'ຫຍໍ້ທັງໝົດ',
            ],

            'expand_all' => [
                'label' => 'ຂະຫຍາຍທັງໝົດ',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'ເລືອກທັງໝົດ',
            ],

            'select_all' => [
                'label' => 'ຍົກເລີກການເລືອກທັງໝົດ',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'ຍົກເລີກ',
                ],

                'drag_crop' => [
                    'label' => 'ໂໝດລາກ "ຕັດຮູບ"',
                ],

                'drag_move' => [
                    'label' => 'ໂໝດລາກ "ຍ້າຍ"',
                ],

                'flip_horizontal' => [
                    'label' => 'ພິກຮູບແນວນອນ',
                ],

                'flip_vertical' => [
                    'label' => 'ພິກຮູບແນວຕັ້ງ',
                ],

                'move_down' => [
                    'label' => 'ຍ້າຍຮູບລົງ',
                ],

                'move_left' => [
                    'label' => 'ຍ້າຍຮູບໄປທາງຊ້າຍ',
                ],

                'move_right' => [
                    'label' => 'ຍ້າຍຮູບໄປທາງຂວາ',
                ],

                'move_up' => [
                    'label' => 'ຍ້າຍຮູບຂຶ້ນ',
                ],

                'reset' => [
                    'label' => 'ຣີເຊັດ',
                ],

                'rotate_left' => [
                    'label' => 'ໝຸນຮູບໄປທາງຊ້າຍ',
                ],

                'rotate_right' => [
                    'label' => 'ໝຸນຮູບໄປທາງຂວາ',
                ],

                'set_aspect_ratio' => [
                    'label' => 'ຕັ້ງອັດຕາສ່ວນຮູບເປັນ :ratio',
                ],

                'save' => [
                    'label' => 'ບັນທຶກ',
                ],

                'zoom_100' => [
                    'label' => 'ຂະຫຍາຍຮູບເປັນ 100%',
                ],

                'zoom_in' => [
                    'label' => 'ຊູມເຂົ້າ',
                ],

                'zoom_out' => [
                    'label' => 'ຊູມອອກ',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'ຄວາມສູງ',
                    'unit' => 'ພິກ',
                ],

                'rotation' => [
                    'label' => 'ໝຸນ',
                    'unit' => 'ອົງສາ',
                ],

                'width' => [
                    'label' => 'ກວ້າງ',
                    'unit' => 'ພິກ',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'ພິກ',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'ພິກ',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'ອັດຕາສ່ວນຮູບ',

                'no_fixed' => [
                    'label' => 'ອິສະຫຼະ',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'ການແກ້ໄຂໄຟລ໌ SVG ບໍ່ແນະນຳ ເນື່ອງຈາກອາດເກີດການສູນເສຍຄຸນນະພາບເມື່ອມີການປັບຂະໜາດ\nແນ່ໃຈບໍ່ວ່າຕ້ອງການດຳເນີນການຕໍ່?',
                    'disabled' => 'ການແກ້ໄຂໄຟລ໌ SVG ຖືກປິດໃຊ້ງານ ເນື່ອງຈາກອາດເຮັດໃຫ້ເກີດການສູນເສຍຄຸນນະພາບເມື່ອມີການປັບຂະໜາດ',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'ເພີ່ມແຖວ',
            ],

            'delete' => [
                'label' => 'ລຶບແຖວ',
            ],

            'reorder' => [
                'label' => 'ຈັດລຳດັບແຖວ',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'ກຸນແຈ',
            ],

            'value' => [
                'label' => 'ຄ່າ',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'ແນບໄຟລ໌',
            'blockquote' => 'ບລັອກຄຳເວົ້າ',
            'bold' => 'ຕົວໜາ',
            'bullet_list' => 'ລາຍການສັນຍາລັກສະແດງຫົວຂໍ້ຍ່ອຍ',
            'code_block' => 'ບລັອກໂຄດ',
            'heading' => 'ຫົວຂໍ້',
            'italic' => 'ຕົວເອຽງ',
            'link' => 'ລິ້ງ',
            'ordered_list' => 'ລາຍການລຳດັບເລກ',
            'redo' => 'ທຳອີກຄັ້ງ',
            'strike' => 'ຂີດຄ່າ',
            'table' => 'ຕາຕະລາງ',
            'undo' => 'ຍົກເລີກທຳ',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'ແມ່ນ',
            'false' => 'ບໍ່ແມ່ນ',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'ເພີ່ມໄປຍັງ :label',
            ],
            'add_between' => [
                'label' => 'ແທລກລະຫວ່າງ',
            ],
            'delete' => [
                'label' => 'ລຶບ',
            ],
            'clone' => [
                'label' => 'ທຳສຳເນົາ',
            ],
            'reorder' => [
                'label' => 'ຍ້າຍ',
            ],
            'move_down' => [
                'label' => 'ເລື່ອນລົງ',
            ],
            'move_up' => [
                'label' => 'ເລື່ອນຂຶ້ນ',
            ],
            'collapse' => [
                'label' => 'ຫຍໍ້',
            ],
            'expand' => [
                'label' => 'ຂະຫຍາຍ',
            ],
            'collapse_all' => [
                'label' => 'ຫຍໍ້ທັງໝົດ',
            ],
            'expand_all' => [
                'label' => 'ຂະຫຍາຍທັງໝົດ',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'ເຊື່ອມໂຍງ',
                    'unlink' => 'ຍົກເລີກການເຊື່ອມໂຍງ',
                ],

                'label' => 'URL',

                'placeholder' => 'ລະບຸ URL',

            ],

        ],

        'tools' => [
            'attach_files' => 'ແນບໄຟລ໌',
            'blockquote' => 'ບລັອກຄຳເວົ້າ',
            'bold' => 'ຕົວໜາ',
            'bullet_list' => 'ລາຍການສັນຍາລັກສະແດງຫົວຂໍ້ຍ່ອຍ',
            'code_block' => 'ບລັອກໂຄດ',
            'h1' => 'ຊື່',
            'h2' => 'ຫົວຂໍ້',
            'h3' => 'ຫົວຂໍ້ຍ່ອຍ',
            'italic' => 'ຕົວເອຽງ',
            'link' => 'ລິ້ງ',
            'ordered_list' => 'ລາຍການລຳດັບເລກ',
            'redo' => 'ກັບຄືນສູ່ປັດຈຸບັນ',
            'strike' => 'ຂີດຄ່າ',
            'underline' => 'ຂີດເສັ້ນໃຕ້',
            'undo' => 'ຍ້ອນກັບ',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'ສ້າງ',

                    'actions' => [

                        'create' => [
                            'label' => 'ສ້າງ',
                        ],

                        'create_another' => [
                            'label' => 'ບັນທຶກ ແລະ ສ້າງອີກລາຍການ',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'ແກ້ໄຂ',

                    'actions' => [

                        'save' => [
                            'label' => 'ບັນທຶກ',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'ແມ່ນ',
            'false' => 'ບໍ່ແມ່ນ',
        ],

        'loading_message' => 'ກຳລັງໂຫຼດ...',

        'max_items_message' => 'ສາມາດເລືອກໄດ້ພຽງ :count ເທົ່ານັ້ນ',

        'no_search_results_message' => 'ບໍ່ມີຕົວເລືອກທີ່ຕົງກັບການຄົ້ນຫາ',

        'placeholder' => 'ເລືອກ',

        'searching_message' => 'ກຳລັງຄົ້ນຫາ...',

        'search_prompt' => 'ເລີ່ມພິມເພື່ອຄົ້ນຫາ...',

    ],

    'tags_input' => [
        'placeholder' => 'ແທັກໃໝ່',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'ເຊື່ອງລະຫັດຜ່ານ',
            ],

            'show_password' => [
                'label' => 'ສະແດງລະຫັດຜ່ານ',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'ແມ່ນ',
            'false' => 'ບໍ່ແມ່ນ',
        ],

    ],

];