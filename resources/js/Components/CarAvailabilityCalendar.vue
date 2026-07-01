<script setup>
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
    car: {
        type: Object,
        required: true,
    },
});

const { locale, t } = useI18n();
const weekdayKeys = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
const loading = ref(false);
const error = ref("");
const availability = ref({ days: [] });
const selectedDate = ref("");
const visibleMonth = ref(currentMalaysiaMonth());

const styles = {
    available: {
        labelKey: "common.statuses.available",
        cell: "bg-green-50 text-green-700 hover:bg-green-100",
        dot: "bg-green-500",
    },
    booked: {
        labelKey: "common.statuses.booked",
        cell: "bg-red-50 text-red-700 hover:bg-red-100",
        dot: "bg-red-500",
    },
    partially_booked: {
        labelKey: "common.statuses.partially_booked",
        cell: "bg-amber-50 text-amber-700 hover:bg-amber-100",
        dot: "bg-amber-500",
    },
    maintenance: {
        labelKey: "common.statuses.under_maintenance",
        cell: "bg-primary-100 text-muted-foreground",
        dot: "bg-primary-500",
    },
    past: {
        labelKey: "common.statuses.past",
        cell: "bg-primary-50/40 text-primary-500 hover:bg-primary-50",
        dot: "bg-primary-300",
    },
};

function currentMalaysiaMonth() {
    const parts = new Intl.DateTimeFormat("en-MY", {
        timeZone: "Asia/Kuala_Lumpur",
        year: "numeric",
        month: "2-digit",
    }).formatToParts(new Date());
    const values = Object.fromEntries(
        parts.map((part) => [part.type, part.value]),
    );

    return `${values.year}-${values.month}`;
}

function monthDate(value) {
    const [year, month] = value.split("-").map(Number);
    return new Date(year, month - 1, 1, 12);
}

function monthValue(date) {
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`;
}

function moveMonth(offset) {
    const nextMonth = monthDate(visibleMonth.value);
    nextMonth.setMonth(nextMonth.getMonth() + offset);
    visibleMonth.value = monthValue(nextMonth);
}

function dayNumber(date) {
    return Number(date.slice(-2));
}

function selectDate(day) {
    selectedDate.value = day.date;
}

function formatSelectedDate(value) {
    if (!value) return "";

    return new Intl.DateTimeFormat(locale.value === "ms" ? "ms-MY" : "en-MY", {
        year: "numeric",
        month: "long",
        day: "numeric",
        timeZone: "Asia/Kuala_Lumpur",
    }).format(new Date(`${value}T00:00:00+08:00`));
}

async function loadAvailability() {
    loading.value = true;
    error.value = "";
    selectedDate.value = "";

    try {
        const response = await axios.get(
            route("cars.availability", props.car.id),
            {
                params: {
                    month: visibleMonth.value,
                },
            },
        );

        availability.value = response.data;
    } catch (requestError) {
        error.value =
            requestError.response?.data?.message ||
            t("calendar.load_error");
    } finally {
        loading.value = false;
    }
}

watch(visibleMonth, loadAvailability);
onMounted(loadAvailability);

const monthTitle = computed(() =>
    new Intl.DateTimeFormat(locale.value === "ms" ? "ms-MY" : "en-MY", {
        month: "long",
        year: "numeric",
    }).format(monthDate(visibleMonth.value)),
);

const calendarDays = computed(() => {
    const days = availability.value.days || [];

    if (!days.length) return [];

    const firstWeekday = new Date(`${days[0].date}T00:00:00+08:00`).getDay();

    return [
        ...Array.from({ length: firstWeekday }, (_, index) => ({
            key: `blank-${index}`,
            blank: true,
        })),
        ...days.map((day) => ({
            ...day,
            key: day.date,
        })),
    ];
});

const selectedDay = computed(() =>
    (availability.value.days || []).find(
        (day) => day.date === selectedDate.value,
    ),
);

const canBookSelectedDay = computed(
    () =>
        selectedDay.value &&
        ["available", "partially_booked"].includes(selectedDay.value.status),
);
</script>

<template>
    <section
        class="mb-8 overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm"
    >
        <div
            class="flex flex-wrap items-start justify-between gap-3 border-b border-primary-100/70 px-4 py-5 sm:px-5"
        >
            <div>
                <h2 class="text-lg font-bold text-foreground">
                    {{ t("calendar.availability_calendar") }}
                </h2>
                <p class="mt-1 text-xs text-muted-foreground">
                    {{ t("calendar.availability_copy") }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    :aria-label="t('calendar.previous_month')"
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-xl font-bold text-primary-700 transition-colors hover:bg-primary-50"
                    @click="moveMonth(-1)"
                >
                    &#8249;
                </button>
                <p
                    class="min-w-32 text-center text-sm font-bold text-foreground"
                >
                    {{ monthTitle }}
                </p>
                <button
                    type="button"
                    :aria-label="t('calendar.next_month')"
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-xl font-bold text-primary-700 transition-colors hover:bg-primary-50"
                    @click="moveMonth(1)"
                >
                    &#8250;
                </button>
            </div>
        </div>

        <div class="p-4 sm:p-5">
            <p
                v-if="error"
                class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700"
            >
                {{ error }}
            </p>

            <div v-else>
                <div class="grid grid-cols-7 gap-1 text-center sm:gap-2">
                    <span
                        v-for="weekday in weekdayKeys"
                        :key="weekday"
                        class="py-2 text-[10px] font-bold uppercase tracking-wide text-primary-500 sm:text-xs"
                    >
                        {{ t(`calendar.weekdays.${weekday}`) }}
                    </span>

                    <template v-if="loading">
                        <span
                            v-for="index in 35"
                            :key="index"
                            class="h-12 animate-pulse rounded-xl bg-primary-50 sm:h-14"
                        ></span>
                    </template>

                    <template v-else>
                        <template v-for="day in calendarDays" :key="day.key">
                            <span v-if="day.blank" class="h-12 sm:h-14"></span>
                            <button
                                v-else
                                type="button"
                                class="relative flex h-12 items-center justify-center rounded-xl text-sm font-bold transition-all sm:h-14"
                                :class="[
                                    styles[day.status].cell,
                                    selectedDate === day.date
                                        ? 'ring-2 ring-primary-600 ring-offset-1'
                                        : '',
                                ]"
                                :aria-label="`${day.date}: ${t(styles[day.status].labelKey)}`"
                                @click="selectDate(day)"
                            >
                                {{ dayNumber(day.date) }}
                                <span
                                    class="absolute bottom-1 h-1.5 w-1.5 rounded-full"
                                    :class="styles[day.status].dot"
                                ></span>
                            </button>
                        </template>
                    </template>
                </div>

                <div class="mt-5 flex flex-wrap gap-x-4 gap-y-2">
                    <span
                        v-for="status in [
                            'available',
                            'booked',
                            'partially_booked',
                            'maintenance',
                            'past',
                        ]"
                        :key="status"
                        class="inline-flex items-center gap-1.5 text-xs text-muted-foreground"
                    >
                        <span
                            class="h-2.5 w-2.5 rounded-full"
                            :class="styles[status].dot"
                        ></span>
                        {{ t(styles[status].labelKey) }}
                    </span>
                </div>

                <div
                    v-if="selectedDay"
                    class="mt-5 rounded-2xl border border-primary-100 bg-primary-50/40 p-4"
                >
                    <div
                        class="flex flex-wrap items-start justify-between gap-3"
                    >
                        <div>
                            <p class="text-sm font-bold text-foreground">
                                {{ formatSelectedDate(selectedDay.date) }}
                            </p>
                            <p class="mt-1 text-xs font-semibold text-muted-foreground">
                                {{ t(styles[selectedDay.status].labelKey) }}
                            </p>
                        </div>
                        <Link
                            v-if="canBookSelectedDay"
                            :href="route('cars.book', car.id)"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            {{ t("car.book_this_car") }}
                        </Link>
                    </div>

                    <div class="mt-4">
                        <p
                            v-if="!selectedDay.available_ranges.length"
                            class="text-sm text-muted-foreground"
                        >
                            {{ t("calendar.no_hourly_slots") }}
                        </p>
                        <div v-else class="space-y-2">
                            <p
                                v-for="range in selectedDay.available_ranges"
                                :key="range.label"
                                class="rounded-xl bg-white px-3 py-2 text-sm font-medium text-foreground"
                            >
                                {{ range.label }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
