<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15">
    <title>KIST Medical College - Patient Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <!-- Left Logo -->
            <div class="flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="KIST Medical College Logo" class="w-20 h-14 object-contain">
            </div>
            <!-- Center Text -->
            <div class="text-center flex-1 px-4">
                <h1 class="text-2xl font-bold text-gray-800">KIST Medical College and Teaching Hospital</h1>
                <p class="text-sm font-semibold text-gray-700">Est. 2006</p>
            </div>
            <!-- Right Logo -->
            <div class="flex-shrink-0">
                <img src="{{ asset('images/nabh_accredited.png') }}" alt="NABH Accredited" class="h-14 object-contain">
            </div>
        </header>

        <!-- Patient Table Card -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 px-6 py-5 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">Current Patient List</h2>
                <span class="bg-white text-indigo-700 text-sm font-medium px-4 py-1.5 rounded-full">
                    {{ $patients->count() }} patients
                </span>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">S.N</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Bed No.</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Hospital No.</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Patient Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Age</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Sex</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Department</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $index => $patient)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-cyan-500">{{ $patient->bed_no }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->hos_no }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 uppercase">{{ $patient->patient_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->age }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->sex }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $dept = strtoupper($patient->department ?? '');
                                        $deptShort = match(true) {
                                            str_contains($dept, 'GYNAE') || str_contains($dept, 'GYNECOLOGY') || str_contains($dept, 'OBSTETRICS') => 'GYNAE',
                                            str_contains($dept, 'MEDI') || $dept === 'MED' => 'MED',
                                            str_contains($dept, 'PEDIA') || str_contains($dept, 'PEDIATRIC') => 'PEDIA',
                                            str_contains($dept, 'CARDIO') || str_contains($dept, 'CARDIOLOGY') => 'CARDIO',
                                            str_contains($dept, 'ORTHO') => 'ORTHO',
                                            str_contains($dept, 'NEURO') => 'NEURO',
                                            str_contains($dept, 'SURG') => 'SURG',
                                            str_contains($dept, 'ENT') => 'ENT',
                                            str_contains($dept, 'OPHTHAL') || str_contains($dept, 'EYE') => 'OPHTH',
                                            str_contains($dept, 'DERMA') => 'DERMA',
                                            str_contains($dept, 'PSYCH') => 'PSYCH',
                                            str_contains($dept, 'PULMO') => 'PULMO',
                                            str_contains($dept, 'GASTRO') => 'GASTRO',
                                            str_contains($dept, 'NEPHRO') || str_contains($dept, 'KIDNEY') => 'NEPHRO',
                                            str_contains($dept, 'URO') => 'URO',
                                            default => $patient->department ?? '-'
                                        };
                                        $colors = [
                                            'GYNAE' => 'bg-emerald-100 text-emerald-700',
                                            'MED' => 'bg-emerald-100 text-emerald-700',
                                            'PEDIA' => 'bg-pink-100 text-pink-700',
                                            'CARDIO' => 'bg-gray-700 text-white',
                                            'ORTHO' => 'bg-blue-100 text-blue-700',
                                            'NEURO' => 'bg-purple-100 text-purple-700',
                                            'SURG' => 'bg-red-100 text-red-700',
                                            'ENT' => 'bg-amber-100 text-amber-700',
                                            'OPHTH' => 'bg-cyan-100 text-cyan-700',
                                            'DERMA' => 'bg-orange-100 text-orange-700',
                                            'PSYCH' => 'bg-indigo-100 text-indigo-700',
                                            'PULMO' => 'bg-teal-100 text-teal-700',
                                            'GASTRO' => 'bg-yellow-100 text-yellow-700',
                                            'NEPHRO' => 'bg-rose-100 text-rose-700',
                                            'URO' => 'bg-lime-100 text-lime-700',
                                        ];
                                        $badgeColor = $colors[$deptShort] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-md {{ $badgeColor }}">
                                        {{ $deptShort }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $patient->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-lg font-medium">No patients found</p>
                                    <p class="text-sm mt-1">Add patients through the <a href="/admin" class="text-indigo-600 hover:underline">admin panel</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                <p>Showing <span class="text-indigo-600 font-medium">{{ $patients->count() }}</span> patients</p>
                <p>Last updated: {{ now()->format('d/m/Y, H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
