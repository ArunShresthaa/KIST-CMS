(function () {
    let lastActiveInputId = null;
    let clickedInsideDatepicker = false;

    function initNepaliPicker() {
        const inputs = document.querySelectorAll('input.nepali-datepicker, .nepali-datepicker input');

        inputs.forEach(function (input) {
            if (input.dataset.ndpInitialized === 'true') return;

            if (!input.id) {
                input.id = 'ndp-input-' + Math.random().toString(36).substr(2, 9);
            }

            const inputId = input.id;

            input.addEventListener('focus', function () {
                lastActiveInputId = inputId;
            });

            // Prevent blur when clicking inside datepicker
            input.addEventListener('blur', function (e) {
                if (clickedInsideDatepicker) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Refocus the input
                    setTimeout(() => {
                        const inp = document.getElementById(inputId);
                        if (inp) inp.focus();
                    }, 0);
                    return false;
                }
            });

            try {
                input.nepaliDatePicker({
                    ndpYear: true,
                    ndpMonth: true,
                    ndpYearCount: 20,
                    dateFormat: 'YYYY-MM-DD',
                    onChange: function (dateObj) {
                        console.log('Date selected via onChange:', dateObj);
                        syncDateToInput(inputId, dateObj.bs);
                    }
                });

                input.dataset.ndpInitialized = 'true';
                console.log('Nepali DatePicker initialized on:', inputId);
            } catch (error) {
                console.error('Nepali DatePicker init failed:', error);
            }
        });
    }

    function syncDateToInput(inputId, dateValue) {
        const targetInput = document.getElementById(inputId);
        if (targetInput) {
            const nativeInputValueSetter = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, 'value').set;
            nativeInputValueSetter.call(targetInput, dateValue);

            console.log('Input value set to:', dateValue);

            targetInput.dispatchEvent(new Event('input', { bubbles: true }));
            targetInput.dispatchEvent(new Event('change', { bubbles: true }));

            console.log('Events dispatched for:', inputId);
        }
    }

    // Track mousedown inside datepicker to prevent blur
    document.addEventListener('mousedown', function (e) {
        const target = e.target;
        if (!target) return;

        const datepickerBox = document.getElementById('ndp-nepali-box');
        if (!datepickerBox) {
            clickedInsideDatepicker = false;
            return;
        }

        if (datepickerBox.contains(target)) {
            clickedInsideDatepicker = true;

            // Check if this is a date cell click
            if (target.closest) {
                const dateLink = target.closest('a[data-value]');
                if (dateLink && !dateLink.classList.contains('ndp-disabled')) {
                    const dateValue = dateLink.getAttribute('data-value');
                    console.log('Date mousedown:', dateValue);

                    e.preventDefault();
                    e.stopPropagation();

                    if (lastActiveInputId) {
                        clickedInsideDatepicker = false;
                        syncDateToInput(lastActiveInputId, dateValue);
                        datepickerBox.style.top = '-999px';
                    }
                    return;
                }
            }

            // For navigation buttons and dropdowns, prevent default to avoid blur
            e.preventDefault();
        } else {
            clickedInsideDatepicker = false;
        }
    }, true);

    // Reset flag on mouseup
    document.addEventListener('mouseup', function () {
        setTimeout(() => {
            clickedInsideDatepicker = false;
        }, 100);
    }, true);

    // Debounce function
    let initTimeout;
    function debouncedInit() {
        clearTimeout(initTimeout);
        initTimeout = setTimeout(initNepaliPicker, 200);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', debouncedInit);
    } else {
        debouncedInit();
    }

    document.addEventListener('livewire:init', () => {
        initNepaliPicker();

        Livewire.hook('morph.updated', () => {
            debouncedInit();
        });

        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                debouncedInit();
            });
        });
    });

    const observer = new MutationObserver((mutations) => {
        let shouldInit = false;
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    if (node.querySelector &&
                        (node.querySelector('.nepali-datepicker') ||
                            node.classList?.contains('nepali-datepicker'))) {
                        shouldInit = true;
                    }
                }
            });
        });
        if (shouldInit) {
            debouncedInit();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();
