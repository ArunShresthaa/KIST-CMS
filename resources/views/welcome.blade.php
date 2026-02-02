<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15">
    <title>KIST Medical College - Patient Information Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a5f',
                        secondary: '#c9302c',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-6 text-center">
            <div class="flex items-center justify-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="KIST Medical College Logo" class="w-20 h-20 object-contain">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-primary">KIST Medical College and Teaching Hospital</h1>
                    <p class="text-sm text-gray-500 mt-1">Estd. 2006</p>
                </div>
            </div>
        </header>

        <!-- Patient Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Current Patient List</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">S.N</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Bed No.</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Hos. No.</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Patient Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Age</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Sex</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Department</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($patients as $index => $patient)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                <td class="px-6 py-4 text-sm text-primary font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-blue-600">{{ $patient->bed_no }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->hos_no }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->patient_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->age }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->sex }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = [
                                            'Cardiology' => 'bg-red-100 text-red-700',
                                            'Orthopedics' => 'bg-blue-100 text-blue-700',
                                            'Neurology' => 'bg-purple-100 text-purple-700',
                                            'Pediatrics' => 'bg-pink-100 text-pink-700',
                                            'Pulmonology' => 'bg-amber-100 text-amber-700',
                                            'Gastroenterology' => 'bg-orange-100 text-orange-700',
                                        ];
                                        $defaultColor = 'bg-gray-100 text-gray-700';
                                        $badgeColor = $colors[$patient->department] ?? $defaultColor;
                                    @endphp
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $badgeColor }}">
                                        {{ $patient->department ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $patient->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-lg font-medium">No patients found</p>
                                    <p class="text-sm mt-1">Add patients through the <a href="/admin" class="text-blue-600 hover:underline">admin panel</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($patients->count() > 0)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-500">Total Patients: {{ $patients->count() }}</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
