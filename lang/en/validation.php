<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (ຂໍ້ຄວາມແຈ້ງເຕືອນການກວດສອບ)
    |--------------------------------------------------------------------------
    |
    | ຂໍ້ຄວາມແຈ້ງເຕືອນຕໍ່ໄປນີ້ປະກອບມີຂໍ້ຄວາມຜິດພາດເລີ່ມຕົ້ນທີ່ໃຊ້ໂດຍ
    | validator class. ບາງກົດລະບຽບມີຫຼາຍສະບັບ ເຊັ່ນ: size rules.
    | ເຈົ້າສາມາດແກ້ໄຂແຕ່ລະຂໍ້ຄວາມເຫຼົ່ານີ້ໄດ້.
    |
    */

    'accepted' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງໄດ້ຮັບການຍອມຮັບ.',
    'accepted_if' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງໄດ້ຮັບການຍອມຮັບເມື່ອ :other ແມ່ນ :value.',
    'active_url' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນ URL ທີ່ຖືກຕ້ອງ.',
    'after' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນວັນທີຫຼັງຈາກ :date.',
    'after_or_equal' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນວັນທີຫຼັງຈາກ ຫຼື ເທົ່າກັບ :date.',
    'alpha' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີພຽງແຕ່ຕົວອັກສອນເທົ່ານັ້ນ.',
    'alpha_dash' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີພຽງຕົວອັກສອນ, ຕົວເລກ, ເສັ້ນທີ່ຍາວ, ແລະ ເສັ້ນຂີດເທົ່ານັ້ນ.',
    'alpha_num' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີພຽງຕົວອັກສອນແລະຕົວເລກເທົ່ານັ້ນ.',
    'any_of' => 'ຊ່ອງຂໍ້ມູນ :attribute ບໍ່ຖືກຕ້ອງ.',
    'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນຂໍ້ມູນປະເພດ array.',
    'ascii' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີພຽງຕົວອັກສອນ ຕົວເລກ ແລະ ສັນຍາລັກແບບ single-byte ເທົ່ານັ້ນ.',
    'before' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນວັນທີກ່ອນ :date.',
    'before_or_equal' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນວັນທີກ່ອນ ຫຼື ເທົ່າກັບ :date.',
    'between' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີລາຍການລະຫວ່າງ :min ແລະ :max ລາຍການ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຂະໜາດລະຫວ່າງ :min ແລະ :max kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຄ່າລະຫວ່າງ :min ແລະ :max.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຄວາມຍາວລະຫວ່າງ :min ແລະ :max ຕົວອັກສອນ.',
    ],
    'boolean' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນ true ຫຼື false.',
    'can' => 'ຊ່ອງຂໍ້ມູນ :attribute ມີຄ່າທີ່ບໍ່ໄດ້ຮັບອະນຸຍາດ.',
    'confirmed' => 'ການຢືນຢັນຊ່ອງຂໍ້ມູນ :attribute ບໍ່ຕົງກັນ.',
    'contains' => 'ຊ່ອງຂໍ້ມູນ :attribute ຂາດຄ່າທີ່ຈຳເປັນ.',
    'current_password' => 'ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ.',
    'date' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນວັນທີທີ່ຖືກຕ້ອງ.',
    'date_equals' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນວັນທີທີ່ເທົ່າກັບ :date.',
    'date_format' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງຕົງກັບຮູບແບບ :format.',
    'decimal' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີ :decimal ຈຸດທົດສະນິຍົມ.',
    'declined' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງຖືກປະຕິເສດ.',
    'declined_if' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງຖືກປະຕິເສດເມື່ອ :other ແມ່ນ :value.',
    'different' => 'ຊ່ອງຂໍ້ມູນ :attribute ແລະ :other ຕ້ອງແຕກຕ່າງກັນ.',
    'digits' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີ :digits ຫລັກເລກ.',
    'digits_between' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຫລັກເລກລະຫວ່າງ :min ແລະ :max ຫລັກ.',
    'dimensions' => 'ຊ່ອງຂໍ້ມູນ :attribute ມີຂະໜາດຮູບບໍ່ຖືກຕ້ອງ.',
    'distinct' => 'ຊ່ອງຂໍ້ມູນ :attribute ມີຄ່າຊ້ຳກັນ.',
    'doesnt_contain' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຄ່າດັ່ງຕໍ່ໄປນີ້: :values.',
    'doesnt_end_with' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ລົງທ້າຍດ້ວຍໜຶ່ງໃນຄ່າຕໍ່ໄປນີ້: :values.',
    'doesnt_start_with' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ເລີ່ມຕົ້ນດ້ວຍໜຶ່ງໃນຄ່າຕໍ່ໄປນີ້: :values.',
    'email' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນທີ່ຢູ່ອີເມວທີ່ຖືກຕ້ອງ.',
    'ends_with' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງລົງທ້າຍດ້ວຍໜຶ່ງໃນຄ່າຕໍ່ໄປນີ້: :values.',
    'enum' => 'ຄ່າ :attribute ທີ່ເລືອກບໍ່ຖືກຕ້ອງ.',
    'exists' => 'ຄ່າ :attribute ທີ່ເລືອກບໍ່ຖືກຕ້ອງ.',
    'extensions' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີນາມສະກຸນໜຶ່ງໃນຕໍ່ໄປນີ້: :values.',
    'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນໄຟລ໌.',
    'filled' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຄ່າ.',
    'gt' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຫຼາຍກວ່າ :value ລາຍການ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງໃຫຍ່ກວ່າ :value kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງໃຫຍ່ກວ່າ :value.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງຍາວກວ່າ :value ຕົວອັກສອນ.',
    ],
    'gte' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີ :value ລາຍການ ຫຼື ຫຼາຍກວ່ານັ້ນ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງໃຫຍ່ກວ່າ ຫຼື ເທົ່າກັບ :value kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງໃຫຍ່ກວ່າ ຫຼື ເທົ່າກັບ :value.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງຍາວກວ່າ ຫຼື ເທົ່າກັບ :value ຕົວອັກສອນ.',
    ],
    'hex_color' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນສີແບບ hexadecimal ທີ່ຖືກຕ້ອງ.',
    'image' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນຮູບພາບ.',
    'in' => 'ຄ່າ :attribute ທີ່ເລືອກບໍ່ຖືກຕ້ອງ.',
    'in_array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢູ່ໃນ :other.',
    'in_array_keys' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍໜຶ່ງໃນກະແຈຕໍ່ໄປນີ້: :values.',
    'integer' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນຕົວເລກຈຳນວນເຕັມ.',
    'ip' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນທີ່ຢູ່ IP ທີ່ຖືກຕ້ອງ.',
    'ipv4' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນທີ່ຢູ່ IPv4 ທີ່ຖືກຕ້ອງ.',
    'ipv6' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນທີ່ຢູ່ IPv6 ທີ່ຖືກຕ້ອງ.',
    'json' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນສາຍຕົວອັກສອນ JSON ທີ່ຖືກຕ້ອງ.',
    'list' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນລາຍການ.',
    'lowercase' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນຕົວພິມນ້ອຍ.',
    'lt' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີນ້ອຍກວ່າ :value ລາຍການ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງນ້ອຍກວ່າ :value kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງນ້ອຍກວ່າ :value.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງສັ້ນກວ່າ :value ຕົວອັກສອນ.',
    ],
    'lte' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຫຼາຍກວ່າ :value ລາຍການ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງນ້ອຍກວ່າ ຫຼື ເທົ່າກັບ :value kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງນ້ອຍກວ່າ ຫຼື ເທົ່າກັບ :value.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງສັ້ນກວ່າ ຫຼື ເທົ່າກັບ :value ຕົວອັກສອນ.',
    ],
    'mac_address' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນທີ່ຢູ່ MAC ທີ່ຖືກຕ້ອງ.',
    'max' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຫຼາຍກວ່າ :max ລາຍການ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ໃຫຍ່ກວ່າ :max kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ໃຫຍ່ກວ່າ :max.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ຍາວກວ່າ :max ຕົວອັກສອນ.',
    ],
    'max_digits' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຫຼາຍກວ່າ :max ຫລັກເລກ.',
    'mimes' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນໄຟລ໌ປະເພດ: :values.',
    'mimetypes' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນໄຟລ໌ປະເພດ: :values.',
    'min' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍ :min ລາຍການ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍ :min kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍ :min.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍ :min ຕົວອັກສອນ.',
    ],
    'min_digits' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍ :min ຫລັກເລກ.',
    'missing' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຢູ່.',
    'missing_if' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຢູ່ເມື່ອ :other ແມ່ນ :value.',
    'missing_unless' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຢູ່ເວັ້ນແຕ່ :other ແມ່ນ :value.',
    'missing_with' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຢູ່ເມື່ອ :values ມີຢູ່.',
    'missing_with_all' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງບໍ່ມີຢູ່ເມື່ອ :values ທັງໝົດມີຢູ່.',
    'multiple_of' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນຜົລຄູນຂອງ :value.',
    'not_in' => 'ຄ່າ :attribute ທີ່ເລືອກບໍ່ຖືກຕ້ອງ.',
    'not_regex' => 'ຮູບແບບຊ່ອງຂໍ້ມູນ :attribute ບໍ່ຖືກຕ້ອງ.',
    'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນຕົວເລກ.',
    'password' => [
        'letters' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍໜຶ່ງຕົວອັກສອນ.',
        'mixed' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍໜຶ່ງຕົວພິມໃຫຍ່ແລະຕົວພິມນ້ອຍ.',
        'numbers' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍໜຶ່ງຕົວເລກ.',
        'symbols' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢ່າງນ້ອຍໜຶ່ງສັນຍາລັກ.',
        'uncompromised' => ':attribute ນີ້ໄດ້ປາກົດຢູ່ໃນການລົ່ວໄຫຼຂໍ້ມູນ. ກະລຸນາເລືອກ :attribute ອື່ນ.',
    ],
    'present' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢູ່.',
    'present_if' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢູ່ເມື່ອ :other ແມ່ນ :value.',
    'present_unless' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢູ່ເວັ້ນແຕ່ :other ແມ່ນ :value.',
    'present_with' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢູ່ເມື່ອ :values ມີຢູ່.',
    'present_with_all' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຢູ່ເມື່ອ :values ທັງໝົດມີຢູ່.',
    'prohibited' => 'ຊ່ອງຂໍ້ມູນ :attribute ຖືກຫ້າມ.',
    'prohibited_if' => 'ຊ່ອງຂໍ້ມູນ :attribute ຖືກຫ້າມເມື່ອ :other ແມ່ນ :value.',
    'prohibited_if_accepted' => 'ຊ່ອງຂໍ້ມູນ :attribute ຖືກຫ້າມເມື່ອ :other ຖືກຍອມຮັບ.',
    'prohibited_if_declined' => 'ຊ່ອງຂໍ້ມູນ :attribute ຖືກຫ້າມເມື່ອ :other ຖືກປະຕິເສດ.',
    'prohibited_unless' => 'ຊ່ອງຂໍ້ມູນ :attribute ຖືກຫ້າມເວັ້ນແຕ່ :other ຢູ່ໃນ :values.',
    'prohibits' => 'ຊ່ອງຂໍ້ມູນ :attribute ຫ້າມ :other ບໍ່ໃຫ້ມີຢູ່.',
    'regex' => 'ຮູບແບບຊ່ອງຂໍ້ມູນ :attribute ບໍ່ຖືກຕ້ອງ.',
    'required' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນຕ້ອງມີ.',
    'required_array_keys' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີລາຍການສຳລັບ: :values.',
    'required_if' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເມື່ອ :other ແມ່ນ :value.',
    'required_if_accepted' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເມື່ອ :other ຖືກຍອມຮັບ.',
    'required_if_declined' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເມື່ອ :other ຖືກປະຕິເສດ.',
    'required_unless' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເວັ້ນແຕ່ :other ຢູ່ໃນ :values.',
    'required_with' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເມື່ອ :values ມີຢູ່.',
    'required_with_all' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເມື່ອ :values ທັງໝົດມີຢູ່.',
    'required_without' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເມື່ອ :values ບໍ່ມີຢູ່.',
    'required_without_all' => 'ຊ່ອງຂໍ້ມູນ :attribute ຈຳເປັນເມື່ອບໍ່ມີໜຶ່ງໃນ :values.',
    'same' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງຕົງກັບ :other.',
    'size' => [
        'array' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີ :size ລາຍການ.',
        'file' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຂະໜາດ :size kilobytes.',
        'numeric' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຄ່າ :size.',
        'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງມີຄວາມຍາວ :size ຕົວອັກສອນ.',
    ],
    'starts_with' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເລີ່ມຕົ້ນດ້ວຍໜຶ່ງໃນຄ່າຕໍ່ໄປນີ້: :values.',
    'string' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນສາຍຕົວອັກສອນ.',
    'timezone' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນເຂດເວລາທີ່ຖືກຕ້ອງ.',
    'unique' => ':attribute ໄດ້ຖືກນຳໃຊ້ແລ້ວ.',
    'uploaded' => ':attribute ອັບໂຫຼດບໍ່ສຳເລັດ.',
    'uppercase' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນຕົວພິມໃຫຍ່.',
    'url' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນ URL ທີ່ຖືກຕ້ອງ.',
    'ulid' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນ ULID ທີ່ຖືກຕ້ອງ.',
    'uuid' => 'ຊ່ອງຂໍ້ມູນ :attribute ຕ້ອງເປັນ UUID ທີ່ຖືກຕ້ອງ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines (ຂໍ້ຄວາມກວດສອບແບບກຳນົດເອງ)
    |--------------------------------------------------------------------------
    |
    | ທີ່ນີ້ເຈົ້າສາມາດກຳນົດຂໍ້ຄວາມກວດສອບແບບກຳນົດເອງສຳລັບ attributes
    | ໂດຍໃຊ້ໂຄງສ້າງ "attribute.rule" ເພື່ອຕັ້ງຊື່ບັນທັດ.
    | ນີ້ຈະເຮັດໃຫ້ສາມາດກຳນົດຂໍ້ຄວາມສະເພາະສຳລັບ attribute rule ໄດ້ງ່າຍ.
    |
    */

    // 'custom' => [
    //     'attribute-name' => [
    //         'rule-name' => 'ຂໍ້ຄວາມແບບກຳນົດເອງ',
    //     ],

    //     // ຕົວຢ່າງສຳລັບລະບົບໂຮງຮຽນ
    //     'student_id' => [
    //         'required' => 'ກະລຸນາເລືອກນັກຮຽນ',
    //         'exists' => 'ນັກຮຽນທີ່ເລືອກບໍ່ມີໃນລະບົບ',
    //     ],
    //     'phone' => [
    //         'regex' => 'ຮູບແບບເບີໂທບໍ່ຖືກຕ້ອງ (ຕົວຢ່າງ: 020xxxxxxxx)',
    //     ],
    //     'national_id' => [
    //         'unique' => 'ເລກບັດປະຊາຊົນນີ້ມີໃນລະບົບແລ້ວ',
    //         'regex' => 'ຮູບແບບເລກບັດປະຊາຊົນບໍ່ຖືກຕ້ອງ',
    //     ],
    //     'email' => [
    //         'unique' => 'ອີເມວນີ້ໄດ້ຖືກນຳໃຊ້ແລ້ວ',
    //         'email' => 'ກະລຸນາໃສ່ອີເມວທີ່ຖືກຕ້ອງ',
    //     ],
    //     'password' => [
    //         'min' => 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງນ້ອຍ :min ຕົວອັກສອນ',
    //         'confirmed' => 'ການຢືນຢັນລະຫັດຜ່ານບໍ່ຕົງກັນ',
    //     ],
    //     'date_of_birth' => [
    //         'required' => 'ກະລຸນາໃສ່ວັນເກີດ',
    //         'date' => 'ຮູບແບບວັນເກີດບໍ່ຖືກຕ້ອງ',
    //         'before' => 'ວັນເກີດຕ້ອງເປັນໃນອະດີດ',
    //     ],
    //     'grade_id' => [
    //         'required' => 'ກະລຸນາເລືອກຊັ້ນຮຽນ',
    //         'exists' => 'ຊັ້ນຮຽນທີ່ເລືອກບໍ່ມີໃນລະບົບ',
    //     ],
    //     'academic_year_id' => [
    //         'required' => 'ກະລຸນາເລືອກປີການຮຽນ',
    //         'exists' => 'ປີການຮຽນທີ່ເລືອກບໍ່ມີໃນລະບົບ',
    //     ],
    //     'amount' => [
    //         'required' => 'ກະລຸນາໃສ່ຈຳນວນເງິນ',
    //         'numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
    //         'min' => 'ຈຳນວນເງິນຕ້ອງມີຄ່າຢ່າງນ້ອຍ :min',
    //     ],
    //     'tuition_months' => [
    //         'required' => 'ກະລຸນາເລືອກເດືອນທີ່ຈ່າຍຄ່າຮຽນ',
    //         'array' => 'ເດືອນທີ່ຈ່າຍຄ່າຮຽນຕ້ອງເປັນລາຍການ',
    //     ],
    //     'food_months' => [
    //         'required' => 'ກະລຸນາເລືອກເດືອນທີ່ຈ່າຍຄ່າອາຫານ',
    //         'array' => 'ເດືອນທີ່ຈ່າຍຄ່າອາຫານຕ້ອງເປັນລາຍການ',
    //     ],
    // ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes (ຊື່ Attributes ແບບກຳນົດເອງ)
    |--------------------------------------------------------------------------
    |
    | ບັນທັດຂໍ້ຄວາມຕໍ່ໄປນີ້ຖືກໃຊ້ເພື່ອປ່ຽນ attribute placeholder
    | ດ້ວຍສິ່ງທີ່ອ່ານງ່າຍກວ່າ ເຊັ່ນ "ທີ່ຢູ່ອີເມວ" ແທນທີ່ຈະເປັນ "email".
    | ນີ້ເປັນພຽງການຊ່ວຍໃຫ້ຂໍ້ຄວາມຂອງພວກເຮົາສະແດງອອກໄດ້ດີຂຶ້ນ.
    |
    */

    'attributes' => [
        // ຂໍ້ມູນພື້ນຖານ
        'name' => 'ຊື່',
        'first_name' => 'ຊື່ຕົວຈິງ',
        'last_name' => 'ນາມສະກຸນ',
        'first_name_lao' => 'ຊື່ຕົວຈິງ (ລາວ)',
        'last_name_lao' => 'ນາມສະກຸນ (ລາວ)',
        'first_name_en' => 'ຊື່ຕົວຈິງ (ອັງກິດ)',
        'last_name_en' => 'ນາມສະກຸນ (ອັງກິດ)',
        'email' => 'ອີເມວ',
        'password' => 'ລະຫັດຜ່ານ',
        'password_confirmation' => 'ຢືນຢັນລະຫັດຜ່ານ',
        'phone' => 'ເບີໂທ',
        'alternative_phone' => 'ເບີໂທສຳຮອງ',
        'address' => 'ທີ່ຢູ່',
        'date_of_birth' => 'ວັນເກີດ',
        'gender' => 'ເພດ',
        'national_id' => 'ເລກບັດປະຊາຊົນ',
        'profile' => 'ຮູບໂປຣໄຟລ໌',

        // ຂໍ້ມູນການຮຽນ
        'student_id' => 'ນັກຮຽນ',
        'grade_id' => 'ຊັ້ນຮຽນ',
        'academic_year_id' => 'ປີການຮຽນ',
        'subject_id' => 'ວິຊາ',
        'class_id' => 'ຫ້ອງຮຽນ',
        'enrollment_date' => 'ວັນທີລົງທະບຽນ',
        'graduation_date' => 'ວັນທີຈົບການຮຽນ',
        'status' => 'ສະຖານະ',

        // ຂໍ້ມູນການເງິນ
        'amount' => 'ຈຳນວນເງິນ',
        'cash' => 'ເງິນສົດ',
        'transfer' => 'ເງິນໂອນ',
        'tuition_amount' => 'ຄ່າຮຽນ',
        'food_money' => 'ຄ່າອາຫານ',
        'payment_date' => 'ວັນທີຈ່າຍ',
        'payment_method' => 'ວິທີການຈ່າຍ',
        'tuition_months' => 'ເດືອນຈ່າຍຄ່າຮຽນ',
        'food_months' => 'ເດືອນຈ່າຍຄ່າອາຫານ',
        'receipt_number' => 'ເລກທີໃບຮັບ',
        'image_path' => 'ຮູບຫຼັກຖານການຈ່າຍ',

        // ຂໍ້ມູນທີ່ຢູ່
        'province_id' => 'ແຂວງ',
        'district_id' => 'ເມືອງ',
        'village_id' => 'ບ້ານ',
        'village_name' => 'ຊື່ບ້ານ',
        'district_name' => 'ຊື່ເມືອງ',
        'province_name' => 'ຊື່ແຂວງ',

        // ຂໍ້ມູນຜູ້ປົກຄອງ
        'guardian_id' => 'ຜູ້ປົກຄອງ',
        'job_category_id' => 'ປະເພດອາຊີບ',
        'relationship' => 'ຄວາມສຳພັນ',
        'occupation' => 'ອາຊີບ',
        'monthly_income' => 'ລາຍຮັບລາຍເດືອນ',

        // ຂໍ້ມູນອື່ນໆ
        'nationality_id' => 'ສັນຊາດ',
        'religion_id' => 'ສາສະໜາ',
        'created_at' => 'ວັນທີສ້າງ',
        'updated_at' => 'ວັນທີອັບເດດ',
        'is_active' => 'ສະຖານະການນຳໃຊ້',
        'description' => 'ຄຳອະທິບາຍ',
        'notes' => 'ໝາຍເຫດ',
        'title' => 'ຫົວຂໍ້',
        'content' => 'ເນື້ອຫາ',
        'type' => 'ປະເພດ',
        'category' => 'ໝວດໝູ່',
        'year' => 'ປີ',
        'month' => 'ເດືອນ',
        'day' => 'ມື້',
        'time' => 'ເວລາ',
        'start_time' => 'ເວລາເລີ່ມ',
        'end_time' => 'ເວລາສິ້ນສຸດ',
        'duration' => 'ໄລຍະເວລາ',
    ],

];
