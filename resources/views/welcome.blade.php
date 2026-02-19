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
                <p>Last updated: {{ now()->format('Y/m/d, h:i:s A') }}</p>
            </div>
        </div>
    </div>

    @php
        $patientsData = $patients->map(function($patient) {
            return [
                'bed_no' => $patient->bed_no,
                'hos_no' => $patient->hos_no,
                'patient_name' => $patient->patient_name,
                'age' => $patient->age,
                'sex' => $patient->sex,
                'department' => $patient->department?->name ?? '-',
                'department_color' => $patient->department?->color ?? '#6b7280',
                'admitted_date' => $patient->admitted_date_bs ?? '-',
                'remarks' => $patient->remarks ?? '-'
            ];
        })->values();
    @endphp

    <script>
        // Patient data from server
        const patients = @json($patientsData);

        // Pagination configuration
        const PATIENTS_PER_PAGE = 8;
        const AUTO_SCROLL_INTERVAL = 15000; // 15 seconds

        // State
        let currentPage = 1;
        const totalPages = Math.max(1, Math.ceil(patients.length / PATIENTS_PER_PAGE));
        let autoScrollTimer = null;
        let animationFrameId = null;
        let progressStartTime = null;

        // Cached DOM references
        let progressBarEl = null;
        let tbodyEl = null;
        let currentPageEl = null;
        let totalPagesEl = null;
        let showingRangeEl = null;
        let pageDotsEl = null;

        // Helper function to lighten a color for background
        function lightenColor(hexColor, percent = 85) {
            const hex = hexColor.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);

            const newR = Math.round(r + (255 - r) * (percent / 100));
            const newG = Math.round(g + (255 - g) * (percent / 100));
            const newB = Math.round(b + (255 - b) * (percent / 100));

            return `rgb(${newR}, ${newG}, ${newB})`;
        }

        // Render the patient table for the current page
        function renderPage() {
            const startIndex = (currentPage - 1) * PATIENTS_PER_PAGE;
            const endIndex = Math.min(startIndex + PATIENTS_PER_PAGE, patients.length);
            const pagePatients = patients.slice(startIndex, endIndex);

            if (pagePatients.length === 0) {
                tbodyEl.innerHTML = `
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

            tbodyEl.innerHTML = pagePatients.map((patient, index) => {
                const globalIndex = startIndex + index + 1;
                const bgColor = lightenColor(patient.department_color);
                const textColor = patient.department_color;
                return `
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-700">${globalIndex}</td>
                        <td class="px-6 py-4 text-sm font-medium text-cyan-500">${patient.bed_no || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.hos_no || '-'}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 uppercase">${patient.patient_name || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.age || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.sex || '-'}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-md" style="background-color: ${bgColor}; color: ${textColor};">
                                ${patient.department}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">${patient.admitted_date}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">${patient.remarks}</td>
                    </tr>
                `;
            }).join('');

            // Update page indicators
            currentPageEl.textContent = currentPage;
            totalPagesEl.textContent = totalPages;
            showingRangeEl.textContent = `${startIndex + 1}-${endIndex}`;

            // Update page dots
            updatePageDots();
        }

        // Update page dots indicator
        function updatePageDots() {
            pageDotsEl.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const dot = document.createElement('div');
                dot.className = `page-indicator w-2 h-2 rounded-full transition-all ${i === currentPage ? 'bg-indigo-600 active' : 'bg-gray-300'}`;
                pageDotsEl.appendChild(dot);
            }
        }

        // Animate progress bar using requestAnimationFrame (no setInterval leak)
        function animateProgress(timestamp) {
            if (!progressStartTime) progressStartTime = timestamp;
            const elapsed = timestamp - progressStartTime;
            const progress = Math.min((elapsed / AUTO_SCROLL_INTERVAL) * 100, 100);
            progressBarEl.style.width = progress + '%';

            if (progress < 100) {
                animationFrameId = requestAnimationFrame(animateProgress);
            }
            // Stop requesting frames once progress reaches 100%
        }

        // Go to next page or reload
        function nextPage() {
            stopTimers();
            if (currentPage >= totalPages) {
                // All pages viewed, reload for updated data
                window.location.reload();
            } else {
                currentPage++;
                renderPage();
                startAutoScroll();
            }
        }

        // Stop all timers and animation frames
        function stopTimers() {
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
                animationFrameId = null;
            }
            if (autoScrollTimer) {
                clearTimeout(autoScrollTimer);
                autoScrollTimer = null;
            }
            progressStartTime = null;
        }

        // Start auto-scroll
        function startAutoScroll() {
            stopTimers();

            // Reset progress bar
            progressBarEl.style.width = '0%';

            // Start progress bar animation via requestAnimationFrame
            animationFrameId = requestAnimationFrame(animateProgress);

            // Schedule next page with setTimeout (single fire, no stacking)
            autoScrollTimer = setTimeout(nextPage, AUTO_SCROLL_INTERVAL);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            // Cache DOM references once
            progressBarEl = document.getElementById('progressBar');
            tbodyEl = document.getElementById('patientTableBody');
            currentPageEl = document.getElementById('currentPage');
            totalPagesEl = document.getElementById('totalPages');
            showingRangeEl = document.getElementById('showingRange');
            pageDotsEl = document.getElementById('pageDots');

            renderPage();
            startAutoScroll();
        });
    </script>
</body>
</html>
