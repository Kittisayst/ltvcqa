<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LTVCQA') }} — ລະບົບປະກັນຄຸນນະພາບພາຍໃນ</title>

    <link rel="icon" type="image/png" href="{{ asset('images/ltvc_logo.png') }}">

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    @php
        $loginUrl = \Illuminate\Support\Facades\Route::has('filament.admin.auth.login')
            ? route('filament.admin.auth.login')
            : url('/admin/login');
    @endphp

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-6 py-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/ltvc_logo.png') }}" alt="ວິທະຍາໄລ ເຕັກນິກ-ວິຊາຊີບ ຫຼວງພະບາງ" class="h-11 w-auto">
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-slate-900">ວິທະຍາໄລ ເຕັກນິກ-ວິຊາຊີບ ຫຼວງພະບາງ</p>
                    <p class="text-xs text-slate-500">ລະບົບປະກັນຄຸນນະພາບພາຍໃນ</p>
                </div>
            </div>
            <a href="{{ $loginUrl }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                ເຂົ້າສູ່ລະບົບ
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-12 sm:py-16">

        @if ($year && $framework)

            {{-- Hero --}}
            <section class="mb-10">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-800 ring-1 ring-inset ring-blue-200">
                        ສົກຮຽນ {{ $year->name }}
                    </span>

                    @if ($framework->status === 'published')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            ກຳລັງໃຊ້ງານ
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700 ring-1 ring-inset ring-amber-200">
                            ຮ່າງ
                        </span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    {{ $framework->name }}
                </h1>
                <p class="mt-3 max-w-2xl text-base text-slate-600">
                    ຊຸດມາດຕະຖານປະກັນຄຸນນະພາບທີ່ນຳໃຊ້ໃນສົກຮຽນນີ້ ປະກອບດ້ວຍ ມາດຕະຖານ, ຕົວຊີ້ວັດ ແລະ ຫຼັກຖານ ຕາມລາຍລະອຽດຂ້າງລຸ່ມ.
                </p>
            </section>

            {{-- Stat tiles --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($standardCount) }}</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">ມາດຕະຖານ</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($indicatorCount) }}</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">ຕົວຊີ້ວັດ</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($basisMainCount) }}</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">ຫຼັກຖານ</p>
                </div>
            </section>

            {{-- Standards list --}}
            <section class="mt-10">
                <h2 class="text-lg font-semibold text-slate-900">ລາຍການມາດຕະຖານ</h2>

                <ul role="list" class="mt-4 divide-y divide-slate-200 overflow-hidden rounded-xl border border-slate-200 bg-white">
                    @forelse ($standards as $standard)
                        <li class="flex items-center justify-between gap-4 px-5 py-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 inline-flex h-7 w-7 flex-none items-center justify-center rounded-lg bg-slate-100 text-sm font-semibold text-slate-600">
                                    {{ $standard->order }}
                                </span>
                                <p class="min-w-0 text-sm font-medium text-slate-900">{{ $standard->name }}</p>
                            </div>
                            <div class="flex flex-none gap-4 text-xs text-slate-500">
                                <span>{{ number_format($standard->indicators_count) }} ຕົວຊີ້ວັດ</span>
                                <span class="text-slate-300">·</span>
                                <span>{{ number_format($standard->basis_mains_count) }} ຫຼັກຖານ</span>
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-sm text-slate-500">
                            ຊຸດມາດຕະຖານນີ້ຍັງບໍ່ທັນມີລາຍການມາດຕະຖານ.
                        </li>
                    @endforelse
                </ul>
            </section>

            {{-- CTA --}}
            <section class="mt-12 rounded-xl border border-slate-200 bg-white p-6 text-center sm:p-8">
                <p class="text-base font-medium text-slate-900">ເປັນພະນັກງານຂອງໜ່ວຍງານ?</p>
                <p class="mt-1 text-sm text-slate-600">ເຂົ້າສູ່ລະບົບເພື່ອອັບໂຫຼດຫຼັກຖານ ແລະ ຕິດຕາມຄວາມຄືບໜ້າ.</p>
                <a href="{{ $loginUrl }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                    ເຂົ້າສູ່ລະບົບ
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </section>

        @else

            {{-- No active academic year --}}
            <section class="mx-auto max-w-md py-16 text-center">
                <img src="{{ asset('images/ltvc_logo.png') }}" alt="ວິທະຍາໄລ ເຕັກນິກ-ວິຊາຊີບ ຫຼວງພະບາງ" class="mx-auto h-20 w-auto">
                <h1 class="mt-6 text-2xl font-bold text-slate-900">ວິທະຍາໄລ ເຕັກນິກ-ວິຊາຊີບ ຫຼວງພະບາງ</h1>
                <p class="mt-1 text-sm font-medium text-slate-500">ລະບົບປະກັນຄຸນນະພາບພາຍໃນ</p>
                <p class="mt-3 text-sm text-slate-600">
                    ຍັງບໍ່ໄດ້ກຳນົດສົກຮຽນທີ່ໃຊ້ງານ. ກະລຸນາເຂົ້າສູ່ລະບົບເພື່ອຕັ້ງຄ່າສົກຮຽນ ແລະ ຊຸດມາດຕະຖານ.
                </p>
                <a href="{{ $loginUrl }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                    ເຂົ້າສູ່ລະບົບ
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </section>

        @endif

    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-6 py-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'LTVCQA') }}
        </div>
    </footer>

</body>
</html>
