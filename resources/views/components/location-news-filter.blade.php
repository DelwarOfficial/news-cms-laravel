@props([
    'divisions' => [],
    'selectedDivision' => '',
    'selectedDistrict' => '',
    'selectedUpazila' => '',
])

@php
    $filterId = 'location-news-filter-' . uniqid();
@endphp

<div class="w-full bg-bg border border-border rounded-md px-4 py-3" id="{{ $filterId }}">
    <form
        id="{{ $filterId }}-form"
        action="{{ route('category.parent', 'country-news') }}"
        method="GET"
        class="flex flex-col md:flex-row md:flex-wrap xl:flex-nowrap md:items-center gap-3"
    >
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-primary text-[20px] leading-none" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     fill="currentColor" class="w-5 h-5 inline-block">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 17.93V18a1 1 0 0 0-2 0v1.93A8 8 0 0 1 4.07 13H6a1 1 0 0 0 0-2H4.07A8 8 0 0 1 11 4.07V6a1 1 0 0 0 2 0V4.07A8 8 0 0 1 19.93 11H18a1 1 0 0 0 0 2h1.93A8 8 0 0 1 13 19.93Z"/>
                </svg>
            </span>
            <span class="font-bold text-[14px] text-fg font-serif whitespace-nowrap">
                জেলার সংবাদ
            </span>
        </div>

        <div class="flex flex-col md:flex-row gap-3 flex-1 min-w-0">
            <div class="flex-1">
                <select
                    id="{{ $filterId }}-division"
                    name="division"
                    aria-label="বিভাগ"
                    class="location-select w-full border border-border bg-bg text-[14px] text-fg rounded-sm px-3 py-2 appearance-none cursor-pointer focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                >
                    <option value="">বিভাগ</option>
                    @foreach($divisions as $engName => $bnName)
                        <option value="{{ $engName }}" {{ $selectedDivision === $engName ? 'selected' : '' }}>
                            {{ $bnName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <select
                    id="{{ $filterId }}-district"
                    name="district"
                    aria-label="জেলা"
                    disabled
                    class="location-select w-full border border-border bg-bg text-[14px] text-fg rounded-sm px-3 py-2 appearance-none cursor-pointer focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <option value="">জেলা</option>
                </select>
            </div>

            <div class="flex-1">
                <select
                    id="{{ $filterId }}-upazila"
                    name="upazila"
                    aria-label="উপজেলা"
                    disabled
                    class="location-select w-full border border-border bg-bg text-[14px] text-fg rounded-sm px-3 py-2 appearance-none cursor-pointer focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <option value="">উপজেলা</option>
                </select>
            </div>
        </div>

        <button
            type="submit"
            id="{{ $filterId }}-submit"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-hover text-fg-on-primary font-bold text-[14px] rounded-sm transition-colors whitespace-nowrap font-serif shrink-0 md:min-w-[96px] focus:outline-none focus:ring-2 focus:ring-primary/30"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                 fill="currentColor" class="w-4 h-4 text-fg-on-primary" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                    clip-rule="evenodd" />
            </svg>
            খুঁজুন
        </button>
    </form>
</div>

<script>
(function () {
    'use strict';

    const rootEl = document.getElementById(@json($filterId));

    if (!rootEl) {
        console.warn('[LocationFilter] Component root was not found.');
        return;
    }

    const formEl = rootEl.querySelector('form');
    const divisionEl = rootEl.querySelector('select[name="division"]');
    const districtEl = rootEl.querySelector('select[name="district"]');
    const upazilaEl = rootEl.querySelector('select[name="upazila"]');

    if (!formEl || !divisionEl || !districtEl || !upazilaEl) {
        console.warn('[LocationFilter] Required form controls are missing.');
        return;
    }

    const endpoints = {
        districts: @json(route('saradesh.districts')),
        upazilas: @json(route('saradesh.upazilas')),
    };

    const selectedDistrict = @json($selectedDistrict);
    const selectedUpazila = @json($selectedUpazila);

    function resetSelect(selectEl, placeholder, disabled = true) {
        selectEl.innerHTML = '';
        selectEl.appendChild(new Option(placeholder, ''));
        selectEl.disabled = disabled;
    }

    function setLoading(selectEl) {
        selectEl.innerHTML = '';
        selectEl.appendChild(new Option('লোড হচ্ছে...', ''));
        selectEl.disabled = true;
    }

    function normaliseItem(item) {
        if (typeof item === 'string') {
            return { value: item, label: item };
        }

        if (item && typeof item === 'object') {
            return {
                value: item.slug || item.name || '',
                label: item.name_bn || item.name_bangla || item.name || item.slug || '',
            };
        }

        return { value: '', label: '' };
    }

    function populateSelect(selectEl, items, placeholder, selectedValue = '') {
        resetSelect(selectEl, placeholder, true);

        if (!Array.isArray(items) || items.length === 0) {
            return;
        }

        items.forEach(function (item) {
            const normalised = normaliseItem(item);

            if (!normalised.value) {
                return;
            }

            const option = new Option(normalised.label, normalised.value);
            option.selected = normalised.value === selectedValue;
            selectEl.appendChild(option);
        });

        selectEl.disabled = selectEl.options.length <= 1;
    }

    function buildUrl(baseUrl, params) {
        const url = new URL(baseUrl, window.location.origin);

        Object.entries(params).forEach(function ([key, value]) {
            if (value) {
                url.searchParams.set(key, value);
            }
        });

        return url.toString();
    }

    async function fetchJson(baseUrl, params) {
        const response = await fetch(buildUrl(baseUrl, params), {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Location request failed.');
        }

        return response.json();
    }

    async function loadDistricts(division, preselectDistrict = '', preselectUpazila = '') {
        resetSelect(districtEl, 'জেলা');
        resetSelect(upazilaEl, 'উপজেলা');

        if (!division) {
            return;
        }

        setLoading(districtEl);

        try {
            const districts = await fetchJson(endpoints.districts, { division });
            populateSelect(districtEl, districts, 'জেলা', preselectDistrict);

            if (preselectDistrict && !districtEl.disabled) {
                await loadUpazilas(division, preselectDistrict, preselectUpazila);
            }
        } catch (error) {
            resetSelect(districtEl, 'জেলা');
            console.warn('[LocationFilter] Failed to load districts.', error);
        }
    }

    async function loadUpazilas(division, district, preselectUpazila = '') {
        resetSelect(upazilaEl, 'উপজেলা');

        if (!division || !district) {
            return;
        }

        setLoading(upazilaEl);

        try {
            const upazilas = await fetchJson(endpoints.upazilas, { division, district });
            populateSelect(upazilaEl, upazilas, 'উপজেলা', preselectUpazila);
        } catch (error) {
            resetSelect(upazilaEl, 'উপজেলা');
            console.warn('[LocationFilter] Failed to load upazilas.', error);
        }
    }

    divisionEl.addEventListener('change', function () {
        loadDistricts(this.value);
    });

    districtEl.addEventListener('change', function () {
        loadUpazilas(divisionEl.value, this.value);
    });

    formEl.addEventListener('submit', function (event) {
        if ((districtEl.value && !divisionEl.value) || (upazilaEl.value && !districtEl.value)) {
            event.preventDefault();
            console.warn('[LocationFilter] Submit blocked because the location hierarchy is incomplete.');
        }
    });

    if (divisionEl.value) {
        loadDistricts(divisionEl.value, selectedDistrict, selectedUpazila);
    }
}());
</script>
