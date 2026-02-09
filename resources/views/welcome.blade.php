<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIST Medical College - Patient Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .page-indicator { transition: all 0.3s ease; }
        .page-indicator.active { transform: scale(1.2); }
        .progress-bar { transition: width 0.1s linear; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="w-[80%] mx-auto pt-12 pb-6">
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
                <div class="flex items-center gap-4">
                    <span class="bg-white text-indigo-700 text-sm font-medium px-4 py-1.5 rounded-full">
                        {{ $patients->count() }} patients
                    </span>
                    <!-- Page indicator -->
                    <div id="pageIndicator" class="flex items-center gap-2 bg-white/20 px-3 py-1.5 rounded-full">
                        <span class="text-white text-sm font-medium">Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
                    </div>
                </div>
            </div>
            
            <!-- Progress bar for auto-scroll timer -->
            <div class="h-1 bg-gray-200">
                <div id="progressBar" class="progress-bar h-full bg-indigo-500" style="width: 0%"></div>
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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Admitted Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-indigo-600">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="patientTableBody">
                        <!-- Patients will be rendered here via JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center gap-4">
                    <p>Showing <span id="showingRange" class="text-indigo-600 font-medium">1-10</span> of <span class="text-indigo-600 font-medium">{{ $patients->count() }}</span> patients</p>
                    <!-- Page dots -->
                    <div id="pageDots" class="flex items-center gap-1.5"></div>
                </div>
                <p>Last updated: {{ now()->format('d/m/Y, H:i:s') }}</p>
            </div>
        </div>
    </div>

    @php
        $patientsData = $patients->map(function($patient) {
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
            return [
                'bed_no' => $patient->bed_no,
                'hos_no' => $patient->hos_no,
                'patient_name' => $patient->patient_name,
                'age' => $patient->age,
                'sex' => $patient->sex,
                'department' => $deptShort,
                'admitted_date' => $patient->admitted_date ? $patient->admitted_date->format('Y/m/d') : '-',
                'remarks' => $patient->remarks ?? '-'
            ];
        })->values();
    @endphp

    <script>
        // Patient data from server
        const patients = @json($patientsData);

        // Pagination configuration
        const PATIENTS_PER_PAGE = 10;
        const AUTO_SCROLL_INTERVAL = 15000; // 15 seconds
        const PROGRESS_UPDATE_INTERVAL = 100; // Update progress bar every 100ms

        // State
        let currentPage = 1;
        let totalPages = Math.max(1, Math.ceil(patients.length / PATIENTS_PER_PAGE));
        let progressTimer = null;
        let autoScrollTimer = null;
        let progressValue = 0;

        // Department color mapping
        const deptColors = {
            'GYNAE': 'bg-emerald-100 text-emerald-700',
            'MED': 'bg-emerald-100 text-emerald-700',
            'PEDIA': 'bg-pink-100 text-pink-700',
            'CARDIO': 'bg-gray-700 text-white',
            'ORTHO': 'bg-blue-100 text-blue-700',
            'NEURO': 'bg-purple-100 text-purple-700',
            'SURG': 'bg-red-100 text-red-700',
            'ENT': 'bg-amber-100 text-amber-700',
            'OPHTH': 'bg-cyan-100 text-cyan-700',
            'DERMA': 'bg-orange-100 text-orange-700',
            'PSYCH': 'bg-indigo-100 text-indigo-700',
            'PULMO': 'bg-teal-100 text-teal-700',
            'GASTRO': 'bg-yellow-100 text-yellow-700',
            'NEPHRO': 'bg-rose-100 text-rose-700',
            'URO': 'bg-lime-100 text-lime-700',
        };

        // Render the patient table for the current page
        function renderPage() {
            const tbody = document.getElementById('patientTableBody');
            const startIndex = (currentPage - 1) * PATIENTS_PER_PAGE;
            const endIndex = Math.min(startIndex + PATIENTS_PER_PAGE, patients.length);
            const pagePatients = patients.slice(startIndex, endIndex);

            if (pagePatients.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-lg font-medium">No patients found</p>
                            <p class="text-sm mt-1">Add patients through the <a href="/admin" class="text-indigo-600 hover:underline">admin panel</a></p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = pagePatients.map((patient, index) => {
                const globalIndex = startIndex + index + 1;
                const badgeColor = deptColors[patient.department] || 'bg-gray-100 text-gray-700';
                return `
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-700">${globalIndex}</td>
                        <td class="px-6 py-4 text-sm font-medium text-cyan-500">${patient.bed_no || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.hos_no || '-'}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 uppercase">${patient.patient_name || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.age || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.sex || '-'}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-md ${badgeColor}">
                                ${patient.department}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.admitted_date}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">${patient.remarks}</td>
                    </tr>
                `;
            }).join('');

            // Update page indicators
            document.getElementById('currentPage').textContent = currentPage;
            document.getElementById('totalPages').textContent = totalPages;
            document.getElementById('showingRange').textContent = `${startIndex + 1}-${endIndex}`;

            // Update page dots
            updatePageDots();
        }

        // Update page dots indicator
        function updatePageDots() {
            const dotsContainer = document.getElementById('pageDots');
            dotsContainer.innerHTML = '';
            
            for (let i = 1; i <= totalPages; i++) {
                const dot = document.createElement('div');
                dot.className = `page-indicator w-2 h-2 rounded-full transition-all ${i === currentPage ? 'bg-indigo-600 active' : 'bg-gray-300'}`;
                dotsContainer.appendChild(dot);
            }
        }

        // Update progress bar
        function updateProgressBar() {
            progressValue += (PROGRESS_UPDATE_INTERVAL / AUTO_SCROLL_INTERVAL) * 100;
            document.getElementById('progressBar').style.width = `${Math.min(progressValue, 100)}%`;
        }

        // Go to next page or reload
        function nextPage() {
            if (currentPage >= totalPages) {
                // All pages viewed, reload for updated data
                window.location.reload();
            } else {
                currentPage++;
                renderPage();
                resetProgress();
            }
        }

        // Reset progress bar and timer
        function resetProgress() {
            progressValue = 0;
            document.getElementById('progressBar').style.width = '0%';
        }

        // Start auto-scroll
        function startAutoScroll() {
            // Clear existing timers
            if (progressTimer) clearInterval(progressTimer);
            if (autoScrollTimer) clearInterval(autoScrollTimer);

            // Start progress bar updates
            progressTimer = setInterval(updateProgressBar, PROGRESS_UPDATE_INTERVAL);

            // Auto-scroll to next page
            autoScrollTimer = setInterval(() => {
                nextPage();
            }, AUTO_SCROLL_INTERVAL);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            renderPage();
            startAutoScroll();
        });
    </script>
</body>
</html>
