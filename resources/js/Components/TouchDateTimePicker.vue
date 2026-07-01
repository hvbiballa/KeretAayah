<script setup>
import axios from "axios";
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        required: true,
        validator: (value) => ["date", "time"].includes(value),
    },
    min: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    ariaLabel: {
        type: String,
        default: "",
    },
    availabilityCarId: {
        type: [Number, String],
        default: null,
    },
    availabilityRoute: {
        type: String,
        default: "",
    },
    availabilityQuery: {
        type: Object,
        default: () => ({}),
    },
    autoConfirmOnSelect: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "confirmed"]);
const { locale, t } = useI18n();

const isOpen = ref(false);
const draftDate = ref("");
const calendarMonth = ref(startOfMonth(dateFromValue(props.modelValue)));
const selectedHour = ref(12);
const selectedMinute = ref(0);
const selectedPeriod = ref("AM");
const availabilityLoading = ref(false);
const availabilityError = ref("");
const availabilityDays = ref([]);
const hourWheel = ref(null);
const minuteWheel = ref(null);
const periodWheel = ref(null);
const wheelTimers = {
    hour: null,
    minute: null,
    period: null,
};

const weekdayKeys = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
const WHEEL_ITEM_HEIGHT = 48;
const WHEEL_SPACER_HEIGHT = 96;
const hours = Array.from({ length: 12 }, (_, index) => index + 1);
const minutes = Array.from({ length: 60 }, (_, index) => index);
const periods = ["AM", "PM"];
const availabilityStyles = {
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

function pad(value) {
    return String(value).padStart(2, "0");
}

function malaysiaNow() {
    const parts = new Intl.DateTimeFormat("en-MY", {
        timeZone: "Asia/Kuala_Lumpur",
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        hourCycle: "h23",
    }).formatToParts(new Date());

    return Object.fromEntries(parts.map((part) => [part.type, part.value]));
}

function todayValue() {
    const now = malaysiaNow();
    return `${now.year}-${now.month}-${now.day}`;
}

function dateFromValue(value) {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value || "");

    if (!match) {
        const today = todayValue();
        const [year, month, day] = today.split("-").map(Number);
        return new Date(year, month - 1, day, 12);
    }

    return new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]), 12);
}

function dateValue(date) {
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

function startOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1, 12);
}

function monthValue(date) {
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}`;
}

async function loadAvailability() {
    if (
        props.type !== "date" ||
        (!props.availabilityCarId && !props.availabilityRoute) ||
        !isOpen.value
    ) {
        return;
    }

    const requestedMonth = monthValue(calendarMonth.value);
    availabilityLoading.value = true;
    availabilityError.value = "";

    try {
        const response = await axios.get(
            props.availabilityRoute || route("cars.availability", props.availabilityCarId),
            {
                params: {
                    month: requestedMonth,
                    ...props.availabilityQuery,
                },
            },
        );

        if (requestedMonth === monthValue(calendarMonth.value)) {
            availabilityDays.value = response.data.days || [];
        }
    } catch (requestError) {
        if (requestedMonth === monthValue(calendarMonth.value)) {
            availabilityDays.value = [];
            availabilityError.value =
                requestError.response?.data?.message ||
                t("calendar.load_picker_error");
        }
    } finally {
        if (requestedMonth === monthValue(calendarMonth.value)) {
            availabilityLoading.value = false;
        }
    }
}

function parseTime(value) {
    const match = /^(\d{2}):(\d{2})$/.exec(value || "");
    const now = malaysiaNow();
    const hour24 = match ? Number(match[1]) : Number(now.hour);
    const minute = match ? Number(match[2]) : Number(now.minute);

    selectedHour.value = hour24 % 12 || 12;
    selectedMinute.value = minute;
    selectedPeriod.value = hour24 >= 12 ? "PM" : "AM";
}

function selectedTimeValue() {
    let hour = selectedHour.value % 12;

    if (selectedPeriod.value === "PM") {
        hour += 12;
    }

    return `${pad(hour)}:${pad(selectedMinute.value)}`;
}

function openPicker() {
    if (props.type === "date") {
        draftDate.value = props.modelValue;
        calendarMonth.value = startOfMonth(dateFromValue(props.modelValue || props.min));
    } else {
        parseTime(props.modelValue);
    }

    isOpen.value = true;
    loadAvailability();

    if (props.type === "time") {
        nextTick(() => {
            syncTimeWheels("auto");
        });
    }
}

function closePicker() {
    isOpen.value = false;
}

function confirmSelection() {
    let value = "";

    if (props.type === "date") {
        if (!draftDate.value) return;
        value = draftDate.value;
        emit("update:modelValue", value);
    } else {
        settleWheel("hour");
        settleWheel("minute");
        settleWheel("period");
        value = selectedTimeValue();
        emit("update:modelValue", value);
    }

    closePicker();
    emit("confirmed", { type: props.type, value });
}

function selectDate(value) {
    draftDate.value = value;

    if (props.autoConfirmOnSelect) {
        confirmSelection();
    }
}

function moveMonth(offset) {
    calendarMonth.value = new Date(
        calendarMonth.value.getFullYear(),
        calendarMonth.value.getMonth() + offset,
        1,
        12,
    );
}

function clampIndex(index, length) {
    return Math.max(0, Math.min(length - 1, index));
}

function scrollWheelToIndex(element, index, behavior = "smooth") {
    if (!element) return;

    element.scrollTo({
        top: clampIndex(index, Number.MAX_SAFE_INTEGER) * WHEEL_ITEM_HEIGHT,
        behavior,
    });
}

function syncTimeWheels(behavior = "smooth") {
    scrollWheelToIndex(hourWheel.value, hours.indexOf(selectedHour.value), behavior);
    scrollWheelToIndex(minuteWheel.value, selectedMinute.value, behavior);
    scrollWheelToIndex(periodWheel.value, periods.indexOf(selectedPeriod.value), behavior);
}

function settleWheel(name) {
    if (name === "hour") {
        const index = clampIndex(
            Math.round((hourWheel.value?.scrollTop || 0) / WHEEL_ITEM_HEIGHT),
            hours.length,
        );
        selectedHour.value = hours[index];
        scrollWheelToIndex(hourWheel.value, index);
        return;
    }

    if (name === "minute") {
        const index = clampIndex(
            Math.round((minuteWheel.value?.scrollTop || 0) / WHEEL_ITEM_HEIGHT),
            minutes.length,
        );
        selectedMinute.value = minutes[index];
        scrollWheelToIndex(minuteWheel.value, index);
        return;
    }

    const index = clampIndex(
        Math.round((periodWheel.value?.scrollTop || 0) / WHEEL_ITEM_HEIGHT),
        periods.length,
    );
    selectedPeriod.value = periods[index];
    scrollWheelToIndex(periodWheel.value, index);
}

function queueWheelSettle(name) {
    if (wheelTimers[name]) {
        window.clearTimeout(wheelTimers[name]);
    }

    wheelTimers[name] = window.setTimeout(() => {
        settleWheel(name);
        wheelTimers[name] = null;
    }, 90);
}

function chooseHour(hour) {
    selectedHour.value = hour;
    scrollWheelToIndex(hourWheel.value, hours.indexOf(hour));
}

function chooseMinute(minute) {
    selectedMinute.value = minute;
    scrollWheelToIndex(minuteWheel.value, minute);
}

function choosePeriod(period) {
    selectedPeriod.value = period;
    scrollWheelToIndex(periodWheel.value, periods.indexOf(period));
}

function handleKeydown(event) {
    if (event.key === "Escape" && isOpen.value) {
        closePicker();
    }
}

onMounted(() => window.addEventListener("keydown", handleKeydown));
onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleKeydown);

    Object.values(wheelTimers).forEach((timer) => {
        if (timer) {
            window.clearTimeout(timer);
        }
    });
});

watch(
    () => props.modelValue,
    (value) => {
        if (!isOpen.value && props.type === "date") {
            calendarMonth.value = startOfMonth(dateFromValue(value));
        }
    },
);
watch(calendarMonth, loadAvailability);
watch(() => props.availabilityCarId, loadAvailability);
watch(() => props.availabilityRoute, loadAvailability);
watch(() => JSON.stringify(props.availabilityQuery || {}), loadAvailability);

defineExpose({
    openPicker,
    closePicker,
});

const calendarTitle = computed(() =>
    new Intl.DateTimeFormat(locale.value === "ms" ? "ms-MY" : "en-MY", {
        month: "long",
        year: "numeric",
    }).format(calendarMonth.value),
);

const calendarDays = computed(() => {
    const year = calendarMonth.value.getFullYear();
    const month = calendarMonth.value.getMonth();
    const firstWeekday = new Date(year, month, 1, 12).getDay();
    const dayCount = new Date(year, month + 1, 0, 12).getDate();
    const days = [];

    for (let index = 0; index < firstWeekday; index += 1) {
        days.push({ key: `blank-${index}`, blank: true });
    }

    for (let day = 1; day <= dayCount; day += 1) {
        const value = dateValue(new Date(year, month, day, 12));
        days.push({
            key: value,
            day,
            value,
            disabled: Boolean(props.min && value < props.min),
            isToday: value === todayValue(),
        });
    }

    return days;
});

const availabilityByDate = computed(() =>
    Object.fromEntries(
        availabilityDays.value.map((day) => [day.date, day]),
    ),
);

const selectedAvailabilityDay = computed(
    () => availabilityByDate.value[draftDate.value],
);
const availabilityMethod = computed(
    () => props.availabilityQuery?.rental_method || "hourly",
);
const hasAvailabilitySource = computed(
    () => Boolean(props.availabilityCarId || props.availabilityRoute),
);

function availabilityDay(value) {
    return availabilityByDate.value[value];
}

function calendarDayClass(day) {
    if (draftDate.value === day.value) {
        return "bg-primary-600 text-white shadow-md shadow-primary-500/25";
    }

    const status = availabilityDay(day.value)?.status;

    return status
        ? availabilityStyles[status].cell
        : "text-foreground hover:bg-primary-50";
}

const displayValue = computed(() => {
    if (!props.modelValue) {
        return props.placeholder || (props.type === "date" ? t("calendar.select_date") : t("calendar.select_time"));
    }

    if (props.type === "date") {
        return new Intl.DateTimeFormat(locale.value === "ms" ? "ms-MY" : "en-MY", {
            year: "numeric",
            month: "short",
            day: "numeric",
            timeZone: "Asia/Kuala_Lumpur",
        }).format(new Date(`${props.modelValue}T00:00:00+08:00`));
    }

    return new Intl.DateTimeFormat(locale.value === "ms" ? "ms-MY" : "en-MY", {
        hour: "numeric",
        minute: "2-digit",
        timeZone: "Asia/Kuala_Lumpur",
    }).format(new Date(`2026-01-01T${props.modelValue}:00+08:00`));
});
</script>

<template>
    <button
        type="button"
        :aria-label="ariaLabel || placeholder"
        class="flex min-h-14 w-full items-center justify-between gap-3 rounded-xl border border-primary-200 bg-white px-4 py-3 text-left text-base text-foreground transition-all hover:border-primary-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
        @click="openPicker"
    >
        <span :class="{ 'text-primary-500': !modelValue }">{{ displayValue }}</span>
        <svg
            v-if="type === 'date'"
            class="h-6 w-6 shrink-0 text-primary-600"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
        >
            <path d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 012 2v13a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
        </svg>
        <svg
            v-else
            class="h-6 w-6 shrink-0 text-primary-600"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
        >
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3 2" />
        </svg>
    </button>

    <Teleport to="body">
        <div
            v-if="isOpen"
            class="fixed inset-0 z-[100] flex items-end justify-center bg-black/50 p-3 backdrop-blur-sm sm:items-center"
            @click.self="closePicker"
        >
            <section
                role="dialog"
                aria-modal="true"
                class="max-h-[92vh] w-full max-w-md overflow-auto rounded-3xl bg-white p-5 shadow-2xl sm:p-6"
            >
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-foreground">
                            {{ type === "date" ? t("calendar.select_date") : t("calendar.select_time") }}
                        </h2>
                        <p class="mt-1 text-xs text-primary-500">
                            {{ type === "time" ? t("calendar.malaysia_time") : t("calendar.tap_preferred_date") }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-50 text-xl text-muted-foreground"
                        :aria-label="t('common.actions.close')"
                        @click="closePicker"
                    >
                        &times;
                    </button>
                </div>

                <template v-if="type === 'date'">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <button
                            type="button"
                            :aria-label="t('calendar.previous_month')"
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-xl text-primary-700"
                            @click="moveMonth(-1)"
                        >
                            &#8249;
                        </button>
                        <p class="text-base font-bold text-foreground">
                            {{ calendarTitle }}
                        </p>
                        <button
                            type="button"
                            :aria-label="t('calendar.next_month')"
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-xl text-primary-700"
                            @click="moveMonth(1)"
                        >
                            &#8250;
                        </button>
                    </div>

                    <div class="grid grid-cols-7 gap-1 text-center">
                        <span
                            v-for="weekday in weekdayKeys"
                            :key="weekday"
                            class="py-2 text-[11px] font-bold uppercase tracking-wide text-primary-500"
                        >
                            {{ t(`calendar.weekdays.${weekday}`) }}
                        </span>
                        <template v-for="day in calendarDays" :key="day.key">
                            <span v-if="day.blank" class="h-11"></span>
                            <button
                                v-else
                                type="button"
                                :disabled="day.disabled"
                                class="relative flex h-11 items-center justify-center rounded-xl text-sm font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-40"
                                :class="[
                                    calendarDayClass(day),
                                    day.isToday && draftDate !== day.value
                                        ? 'ring-1 ring-primary-300'
                                        : '',
                                ]"
                                @click="selectDate(day.value)"
                            >
                                {{ day.day }}
                                <span
                                    v-if="availabilityDay(day.value)"
                                    class="absolute bottom-1 h-1.5 w-1.5 rounded-full"
                                    :class="
                                        availabilityStyles[
                                            availabilityDay(day.value).status
                                        ].dot
                                    "
                                ></span>
                            </button>
                        </template>
                    </div>

                    <div
                        v-if="hasAvailabilitySource"
                        class="mt-4 border-t border-primary-100/70 pt-4"
                    >
                        <p
                            v-if="availabilityLoading"
                            class="text-xs text-primary-500"
                        >
                            {{ t("calendar.loading_availability") }}
                        </p>
                        <p
                            v-else-if="availabilityError"
                            class="text-xs text-red-600"
                        >
                            {{ availabilityError }}
                        </p>
                        <div v-else>
                            <div class="flex flex-wrap gap-x-3 gap-y-2">
                                <span
                                    v-for="status in [
                                        'available',
                                        'booked',
                                        'partially_booked',
                                        'maintenance',
                                    ]"
                                    :key="status"
                                    class="inline-flex items-center gap-1 text-[11px] text-muted-foreground"
                                >
                                    <span
                                        class="h-2 w-2 rounded-full"
                                        :class="availabilityStyles[status].dot"
                                    ></span>
                                    {{ t(availabilityStyles[status].labelKey) }}
                                </span>
                            </div>

                            <div
                                v-if="selectedAvailabilityDay"
                                class="mt-4 rounded-xl bg-primary-50/40 p-3"
                            >
                                <p
                                    class="text-xs font-bold text-foreground"
                                >
                                    {{
                                        t(availabilityStyles[
                                            selectedAvailabilityDay.status
                                        ].labelKey)
                                    }}
                                </p>
                                <p
                                    v-if="
                                        typeof selectedAvailabilityDay.available_cars_count === 'number'
                                    "
                                    class="mt-2 text-xs font-medium text-muted-foreground"
                                >
                                    {{
                                        t("calendar.available_cars_count", {
                                            count: selectedAvailabilityDay.available_cars_count,
                                        })
                                    }}
                                </p>
                                <p
                                    v-if="
                                        availabilityMethod === 'hourly' &&
                                        !selectedAvailabilityDay.available_ranges.length
                                    "
                                    class="mt-2 text-xs text-muted-foreground"
                                >
                                    {{ t("calendar.no_hourly_slots") }}
                                </p>
                                <p
                                    v-else-if="
                                        availabilityMethod === 'daily' &&
                                        selectedAvailabilityDay.available_cars_count === 0
                                    "
                                    class="mt-2 text-xs text-muted-foreground"
                                >
                                    {{ t("calendar.no_daily_cars") }}
                                </p>
                                <div v-else class="mt-2 space-y-1">
                                    <p
                                        v-for="range in selectedAvailabilityDay.available_ranges"
                                        :key="`${range.label}-${range.count ?? 0}`"
                                        class="text-xs font-medium text-muted-foreground"
                                    >
                                        {{
                                            range.count
                                                ? t("calendar.range_with_count", {
                                                      range: range.label,
                                                      count: range.count,
                                                  })
                                                : range.label
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div class="mb-5 rounded-[1.75rem] bg-linear-to-br from-primary-50 via-white to-primary-50/60 p-4 text-center shadow-[inset_0_1px_0_rgba(255,255,255,0.85)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-500">
                            {{ t("calendar.selected_time") }}
                        </p>
                        <p class="mt-1 text-3xl font-extrabold tracking-tight text-primary-700 [font-variant-numeric:tabular-nums]">
                            {{ pad(selectedHour) }}:{{ pad(selectedMinute) }}
                            {{ selectedPeriod }}
                        </p>
                    </div>

                    <div class="rounded-[2rem] border border-primary-100 bg-linear-to-b from-white via-primary-50/35 to-white p-4 shadow-[0_18px_50px_-32px_rgba(225,29,72,0.35)]">
                        <div class="mb-3 grid grid-cols-3 gap-3 px-3">
                            <p class="text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-primary-500">
                                {{ t("calendar.hour") }}
                            </p>
                            <p class="text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-primary-500">
                                {{ t("calendar.minute") }}
                            </p>
                            <p class="text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-primary-500">
                                {{ t("calendar.period") }}
                            </p>
                        </div>

                        <div class="relative grid grid-cols-[1fr_1fr_0.8fr] gap-3">
                            <div class="pointer-events-none absolute inset-x-3 top-1/2 z-10 h-12 -translate-y-1/2 rounded-2xl border border-primary-200/80 bg-white/80 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.45)] backdrop-blur-sm"></div>
                            <div class="pointer-events-none absolute inset-x-0 top-0 z-10 h-24 rounded-t-[1.5rem] bg-linear-to-b from-white via-white/90 to-transparent"></div>
                            <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-24 rounded-b-[1.5rem] bg-linear-to-t from-white via-white/90 to-transparent"></div>

                            <div
                                ref="hourWheel"
                                class="relative z-20 h-60 snap-y snap-mandatory overflow-y-auto rounded-[1.5rem] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                                @scroll="queueWheelSettle('hour')"
                            >
                                <div :style="{ height: `${WHEEL_SPACER_HEIGHT}px` }"></div>
                                <button
                                    v-for="hour in hours"
                                    :key="hour"
                                    type="button"
                                    class="flex h-12 w-full snap-center items-center justify-center text-xl font-semibold tracking-tight transition-all duration-200 [font-variant-numeric:tabular-nums]"
                                    :class="
                                        selectedHour === hour
                                            ? 'scale-[1.02] text-primary-700'
                                            : 'text-primary-300'
                                    "
                                    @click="chooseHour(hour)"
                                >
                                    {{ pad(hour) }}
                                </button>
                                <div :style="{ height: `${WHEEL_SPACER_HEIGHT}px` }"></div>
                            </div>

                            <div
                                ref="minuteWheel"
                                class="relative z-20 h-60 snap-y snap-mandatory overflow-y-auto rounded-[1.5rem] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                                @scroll="queueWheelSettle('minute')"
                            >
                                <div :style="{ height: `${WHEEL_SPACER_HEIGHT}px` }"></div>
                                <button
                                    v-for="minute in minutes"
                                    :key="minute"
                                    type="button"
                                    class="flex h-12 w-full snap-center items-center justify-center text-xl font-semibold tracking-tight transition-all duration-200 [font-variant-numeric:tabular-nums]"
                                    :class="
                                        selectedMinute === minute
                                            ? 'scale-[1.02] text-primary-700'
                                            : 'text-primary-300'
                                    "
                                    @click="chooseMinute(minute)"
                                >
                                    {{ pad(minute) }}
                                </button>
                                <div :style="{ height: `${WHEEL_SPACER_HEIGHT}px` }"></div>
                            </div>

                            <div
                                ref="periodWheel"
                                class="relative z-20 h-60 snap-y snap-mandatory overflow-y-auto rounded-[1.5rem] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                                @scroll="queueWheelSettle('period')"
                            >
                                <div :style="{ height: `${WHEEL_SPACER_HEIGHT}px` }"></div>
                                <button
                                    v-for="period in periods"
                                    :key="period"
                                    type="button"
                                    class="flex h-12 w-full snap-center items-center justify-center text-lg font-semibold tracking-[0.08em] transition-all duration-200"
                                    :class="
                                        selectedPeriod === period
                                            ? 'scale-[1.02] text-primary-700'
                                            : 'text-primary-300'
                                    "
                                    @click="choosePeriod(period)"
                                >
                                    {{ period }}
                                </button>
                                <div :style="{ height: `${WHEEL_SPACER_HEIGHT}px` }"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button
                        type="button"
                        class="min-h-12 rounded-xl bg-primary-50 px-4 font-bold text-muted-foreground"
                        @click="closePicker"
                    >
                        {{ t("common.actions.cancel") }}
                    </button>
                    <button
                        type="button"
                        :disabled="type === 'date' && !draftDate"
                        class="min-h-12 rounded-xl bg-primary-600 px-4 font-bold text-white shadow-md shadow-primary-500/25 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="confirmSelection"
                    >
                        {{ t("common.actions.done") }}
                    </button>
                </div>
            </section>
        </div>
    </Teleport>
</template>
