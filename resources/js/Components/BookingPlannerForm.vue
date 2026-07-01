<script setup>
import InfoHint from "@/Components/InfoHint.vue";
import TouchDateTimePicker from "@/Components/TouchDateTimePicker.vue";
import { carImageAlt, carImageUrl, formatMYR } from "@/lib/car";
import {
    buildDurationFromReturn,
    buildReturnFromDuration,
} from "@/lib/bookingPlanner";
import { rentalMethodLabel, rentalMethodTheme } from "@/lib/rental";
import { Link, router, useForm } from "@inertiajs/vue3";
import { computed, nextTick, ref, watch } from "vue";

const props = defineProps({
    actionRoute: {
        type: String,
        required: true,
    },
    initialSchedule: {
        type: Object,
        default: () => ({}),
    },
    hasSchedule: {
        type: Boolean,
        default: false,
    },
    cars: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    rental_method: props.initialSchedule.rental_method || "hourly",
    pickup_date: props.initialSchedule.pickup_date || "",
    pickup_time: props.initialSchedule.pickup_time || "",
    return_date: props.initialSchedule.return_date || "",
    return_time: props.initialSchedule.return_time || "",
});

const syncing = ref(false);
const lastEdited = ref("duration");
const normalizeDurationUnits = (value, method = form.rental_method) => {
    if (value === "" || value === null || value === undefined) {
        return "";
    }

    const numeric = Number(value);

    if (!Number.isFinite(numeric) || numeric <= 0) {
        return "";
    }

    const rounded = Math.round(numeric);
    const min = method === "hourly" ? 2 : 1;
    const max = method === "hourly" ? 20 : 7;

    return Math.min(max, Math.max(min, rounded));
};
const durationUnits = ref(
    normalizeDurationUnits(
        props.initialSchedule.duration_units ||
            buildDurationFromReturn(form.data()) ||
            (form.rental_method === "hourly" ? 2 : 1),
        form.rental_method,
    ),
);

const currentStep = computed(() => (props.hasSchedule ? 2 : 1));
const matchingCars = computed(() =>
    props.cars.filter((car) => car.matches_schedule),
);
const theme = computed(() => rentalMethodTheme(form.rental_method));
const tooltipClass = computed(() => `${theme.value.card} ${theme.value.border}`);
const pickupTimePicker = ref(null);
const returnTimePicker = ref(null);

const malaysiaDateValue = (date) =>
    new Intl.DateTimeFormat("en-CA", {
        timeZone: "Asia/Kuala_Lumpur",
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    }).format(date);

const minStartDate = malaysiaDateValue(new Date());
const minEndDate = computed(() => form.pickup_date || minStartDate);
const durationStep = "1";
const durationMin = computed(() => (form.rental_method === "hourly" ? 2 : 1));
const durationMax = computed(() => (form.rental_method === "hourly" ? 20 : 7));

const progressSteps = computed(() => [
    {
        number: 1,
        label: "booking.step_schedule",
        active: currentStep.value >= 1,
        current: currentStep.value === 1,
    },
    {
        number: 2,
        label: "booking.step_car",
        active: currentStep.value >= 2,
        current: currentStep.value === 2,
    },
    {
        number: 3,
        label: "booking.step_confirm",
        active: false,
        current: false,
    },
]);

function runSync(callback) {
    syncing.value = true;
    callback();
    syncing.value = false;
}

function syncReturnFromDuration() {
    const next = buildReturnFromDuration({
        ...form.data(),
        duration_units: durationUnits.value,
    });

    form.return_date = next.return_date;
    form.return_time = next.return_time;
}

function syncDurationFromReturn() {
    if (form.rental_method === "daily" && form.pickup_time) {
        form.return_time = form.pickup_time;
    }

    const rawDuration = buildDurationFromReturn(form.data());
    const normalizedDuration = normalizeDurationUnits(rawDuration);

    durationUnits.value = normalizedDuration;

    return normalizedDuration !== "" && Number(normalizedDuration) !== Number(rawDuration);
}

function syncNormalizedDuration() {
    const normalizedDuration = normalizeDurationUnits(durationUnits.value);

    if (normalizedDuration === "") {
        durationUnits.value = "";
        return;
    }

    durationUnits.value = normalizedDuration;
}

function submit() {
    form.get(props.actionRoute, {
        preserveScroll: false,
        replace: true,
    });
}

function goBackToSchedule() {
    router.get(
        props.actionRoute,
        {},
        {
            preserveScroll: false,
            replace: true,
        },
    );
}

function openPickupTimePicker() {
    nextTick(() => {
        pickupTimePicker.value?.openPicker?.();
    });
}

function openReturnTimePicker() {
    if (form.rental_method !== "hourly") return;

    nextTick(() => {
        returnTimePicker.value?.openPicker?.();
    });
}

const confirmationUrl = (carId) =>
    route("bookings.confirm", {
        rental_method: form.rental_method,
        pickup_date: form.pickup_date,
        pickup_time: form.pickup_time,
        return_date: form.return_date,
        return_time: form.return_time,
        car_id: carId,
    });

watch(
    () => form.rental_method,
    (method) => {
        if (syncing.value) return;

        runSync(() => {
            durationUnits.value =
                normalizeDurationUnits(durationUnits.value, method) ||
                (method === "hourly" ? 2 : 1);

            if (method === "daily" && form.pickup_time) {
                form.return_time = form.pickup_time;
            }

            lastEdited.value = "duration";
            syncReturnFromDuration();
        });
    },
);

watch(
    [() => form.pickup_date, () => form.pickup_time],
    () => {
        if (syncing.value) return;

        runSync(() => {
            if (form.rental_method === "daily" && form.pickup_time) {
                form.return_time = form.pickup_time;
            }

            if (lastEdited.value === "return") {
                syncDurationFromReturn();
            } else {
                syncReturnFromDuration();
            }
        });
    },
);

watch(durationUnits, () => {
    if (syncing.value) return;

    lastEdited.value = "duration";

    runSync(() => {
        syncNormalizedDuration();
        syncReturnFromDuration();
    });
});

watch(
    [() => form.return_date, () => form.return_time],
    () => {
        if (syncing.value) return;

        lastEdited.value = "return";

        runSync(() => {
            const snappedToWholeUnit = syncDurationFromReturn();

            if (form.rental_method === "hourly" && snappedToWholeUnit) {
                syncReturnFromDuration();
            }
        });
    },
);

runSync(() => {
    if (form.return_date && form.return_time) {
        lastEdited.value = "return";
        syncDurationFromReturn();
    } else {
        syncReturnFromDuration();
    }
});
</script>

<template>
    <section
        class="rounded-[2rem] border p-6 shadow-[0_24px_80px_-36px_rgba(30,41,59,0.35)] sm:p-8"
        :class="[theme.card, theme.border]"
    >
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold tracking-[0.18em]" :class="theme.text">
                    {{
                        currentStep === 1
                            ? $t("booking.schedule_step")
                            : $t("booking.choose_car_step")
                    }}
                </p>
                <h2 class="mt-1.5 text-2xl font-extrabold tracking-tight text-foreground">
                    {{
                        currentStep === 1
                            ? $t("booking.dashboard_form_title")
                            : $t("booking.fleet_match_title")
                    }}
                </h2>
                <p class="mt-1 text-sm leading-6 text-muted-foreground">
                    {{
                        currentStep === 1
                            ? $t("booking.dashboard_form_copy")
                            : $t("booking.fleet_match_copy_ready", {
                                  count: matchingCars.length,
                              })
                    }}
                </p>
            </div>

            <div
                v-if="currentStep === 1"
                class="inline-flex rounded-2xl p-1"
                :class="theme.toggle"
            >
                <div class="flex items-center gap-1.5">
                    <button
                        type="button"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition-colors"
                        :class="
                            form.rental_method === 'hourly'
                                ? 'bg-white text-foreground shadow-sm'
                                : 'text-muted-foreground'
                        "
                        @click="form.rental_method = 'hourly'"
                    >
                        {{ $t("booking.hourly_rental") }}
                    </button>
                    <button
                        type="button"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition-colors"
                        :class="
                            form.rental_method === 'daily'
                                ? 'bg-white text-foreground shadow-sm'
                                : 'text-muted-foreground'
                        "
                        @click="form.rental_method = 'daily'"
                    >
                        {{ $t("booking.daily_rental") }}
                    </button>
                    <InfoHint
                        :text="$t('booking.tooltips.rental_method')"
                        :icon-class="theme.text"
                        :tooltip-class="tooltipClass"
                    />
                </div>
            </div>
        </div>

        <div class="mt-5 rounded-[1.25rem] px-3 py-2.5" :class="theme.surfaceStrong">
            <div class="flex items-center gap-2">
                <div
                    v-for="(step, index) in progressSteps"
                    :key="step.number"
                    class="flex min-w-0 flex-1 items-center gap-2"
                >
                    <div class="flex items-center min-w-0">
                        <div
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                            :class="
                                step.active
                                    ? step.current
                                        ? theme.progressCurrent
                                        : theme.progressDone
                                    : theme.progressPending
                            "
                        >
                            {{ step.number }}
                        </div>
                    </div>
                    <div
                        v-if="index < progressSteps.length - 1"
                        class="h-0.5 flex-1 rounded-full"
                        :class="step.active ? theme.progressBar : 'bg-white'"
                    ></div>
                </div>
            </div>
        </div>

        <form
            v-if="currentStep === 1"
            class="mt-8 space-y-6"
            @submit.prevent="submit"
        >
            <div class="grid gap-5 lg:grid-cols-[1.15fr_1fr_0.9fr]">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-foreground">
                            {{ $t("booking.pickup_date") }}
                        </label>
                        <InfoHint
                            :text="$t('booking.tooltips.pickup_date')"
                            :icon-class="theme.text"
                            :tooltip-class="tooltipClass"
                        />
                    </div>
                    <TouchDateTimePicker
                        v-model="form.pickup_date"
                        type="date"
                        :min="minStartDate"
                        :availability-route="route('fleet.availability')"
                        :availability-query="{ rental_method: form.rental_method }"
                        :auto-confirm-on-select="true"
                        :placeholder="$t('booking.select_pickup_date')"
                        :aria-label="$t('booking.pickup_date')"
                        @confirmed="openPickupTimePicker"
                    />
                    <p
                        v-if="form.errors.pickup_date"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.pickup_date }}
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-foreground">
                            {{ $t("booking.pickup_time") }}
                        </label>
                        <InfoHint
                            :text="$t('booking.tooltips.pickup_time')"
                            :icon-class="theme.text"
                            :tooltip-class="tooltipClass"
                        />
                    </div>
                    <TouchDateTimePicker
                        ref="pickupTimePicker"
                        v-model="form.pickup_time"
                        type="time"
                        :placeholder="$t('booking.select_pickup_time')"
                        :aria-label="$t('booking.pickup_time')"
                    />
                    <p
                        v-if="form.errors.pickup_time"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.pickup_time }}
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-foreground">
                            {{
                                form.rental_method === "hourly"
                                    ? $t("booking.number_of_hours")
                                    : $t("booking.number_of_days")
                            }}
                        </label>
                        <InfoHint
                            :text="
                                form.rental_method === 'hourly'
                                    ? $t('booking.tooltips.duration_hourly')
                                    : $t('booking.tooltips.duration_daily')
                            "
                            :icon-class="theme.text"
                            :tooltip-class="tooltipClass"
                        />
                    </div>
                    <input
                        v-model="durationUnits"
                        :min="durationMin"
                        :max="durationMax"
                        :step="durationStep"
                        type="number"
                        inputmode="numeric"
                        class="min-h-14 w-full rounded-xl border bg-white px-4 py-3 text-base text-foreground transition-all"
                        :class="theme.input"
                    />
                    <p class="text-xs" :class="theme.textSoft">
                        {{
                            form.rental_method === "hourly"
                                ? $t("booking.hourly_hint")
                                : $t("booking.daily_hint")
                        }}
                    </p>
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-foreground">
                            {{ $t("booking.return_date") }}
                        </label>
                        <InfoHint
                            :text="
                                form.rental_method === 'hourly'
                                    ? $t('booking.tooltips.return_date_hourly')
                                    : $t('booking.tooltips.return_date_daily')
                            "
                            :icon-class="theme.text"
                            :tooltip-class="tooltipClass"
                        />
                    </div>
                    <TouchDateTimePicker
                        v-model="form.return_date"
                        type="date"
                        :min="minEndDate"
                        :availability-route="route('fleet.availability')"
                        :availability-query="{ rental_method: form.rental_method }"
                        :auto-confirm-on-select="form.rental_method === 'hourly'"
                        :placeholder="$t('booking.select_return_date')"
                        :aria-label="$t('booking.return_date')"
                        @confirmed="openReturnTimePicker"
                    />
                    <p
                        v-if="form.errors.return_date"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.return_date }}
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-foreground">
                            {{
                                form.rental_method === "hourly"
                                    ? $t("booking.return_time")
                                    : $t("booking.synced_return_time")
                            }}
                        </label>
                        <InfoHint
                            :text="
                                form.rental_method === 'hourly'
                                    ? $t('booking.tooltips.return_time_hourly')
                                    : $t('booking.tooltips.return_time_daily')
                            "
                            :icon-class="theme.text"
                            :tooltip-class="tooltipClass"
                        />
                    </div>
                    <TouchDateTimePicker
                        v-if="form.rental_method === 'hourly'"
                        ref="returnTimePicker"
                        v-model="form.return_time"
                        type="time"
                        :placeholder="$t('booking.select_return_time')"
                        :aria-label="$t('booking.return_time')"
                    />
                    <div
                        v-else
                        class="flex min-h-14 items-center justify-between rounded-xl border px-4 py-3"
                        :class="[theme.border, theme.surface]"
                    >
                        <span class="text-base font-medium text-foreground">
                            {{
                                form.return_time ||
                                $t("booking.select_pickup_time")
                            }}
                        </span>
                        <span
                            class="text-xs font-semibold uppercase tracking-[0.18em]"
                            :class="theme.text"
                        >
                            {{ $t("booking.same_as_pickup") }}
                        </span>
                    </div>
                    <p
                        v-if="form.errors.return_time"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.return_time }}
                    </p>
                </div>
            </div>

            <div
                class="grid gap-4 rounded-[1.75rem] p-5 lg:grid-cols-[1fr_auto] lg:items-center"
                :class="theme.panel"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.2em]"
                        :class="theme.panelSubtext"
                    >
                        {{ $t("booking.schedule_summary") }}
                    </p>
                    <p class="mt-2 text-lg font-semibold leading-7">
                        {{
                            form.rental_method === "hourly"
                                ? $t("booking.schedule_summary_hourly", {
                                      hours: Number(durationUnits || 0),
                                  })
                                : $t("booking.schedule_summary_daily", {
                                      days: Number(durationUnits || 0),
                                  })
                        }}
                    </p>
                    <p class="mt-2 text-sm leading-6" :class="theme.panelSubtext">
                        {{ $t("booking.schedule_summary_copy") }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex min-h-14 items-center justify-center rounded-2xl px-6 text-sm font-semibold transition-transform duration-200 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60"
                    :class="theme.lightButton"
                >
                    {{ $t("booking.show_matching_cars") }}
                </button>
            </div>
        </form>

        <div v-else class="mt-8 space-y-6">
            <div
                class="flex flex-wrap items-center justify-between gap-4 rounded-[1.5rem] p-4"
                :class="theme.surfaceStrong"
            >
                <div class="flex flex-wrap items-center gap-3">
                    <span
                        class="rounded-full px-3 py-1 text-xs font-semibold"
                        :class="theme.badge"
                    >
                        {{ rentalMethodLabel(form.rental_method, $t) }}
                    </span>
                    <span class="text-sm font-medium text-foreground">
                        {{ form.pickup_date }} · {{ form.pickup_time }}
                    </span>
                    <span class="text-sm text-muted-foreground">→</span>
                    <span class="text-sm font-medium text-foreground">
                        {{ form.return_date }} · {{ form.return_time }}
                    </span>
                </div>

                <button
                    type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl px-4 text-sm font-semibold transition-colors"
                    :class="theme.lightButton"
                    @click="goBackToSchedule"
                >
                    {{ $t("booking.back_to_schedule") }}
                </button>
            </div>

            <div
                v-if="matchingCars.length > 0"
                class="grid gap-5 md:grid-cols-2 xl:grid-cols-3"
            >
                <article
                    v-for="car in matchingCars"
                    :key="car.id"
                    class="overflow-hidden rounded-[2rem] border shadow-[0_20px_70px_-40px_rgba(15,23,42,0.45)] transition-transform duration-200 hover:-translate-y-1"
                    :class="[theme.card, theme.border]"
                >
                    <div
                        class="relative h-52 overflow-hidden"
                        :class="theme.surfaceStrong"
                    >
                        <img
                            :src="carImageUrl(car)"
                            :alt="carImageAlt(car, $t)"
                            class="h-full w-full object-cover"
                        />
                        <span
                            class="absolute left-4 top-4 rounded-full px-3 py-1 text-xs font-semibold"
                            :class="theme.badgeSoft"
                        >
                            {{ $t("booking.available_for_schedule") }}
                        </span>
                    </div>

                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-foreground">
                                    {{ car.name }}
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ car.brand }} · {{ car.model }} ·
                                    {{ car.year }}
                                </p>
                            </div>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="theme.badgeSoft"
                            >
                                {{ car.car_type }}
                            </span>
                        </div>

                        <div
                            v-if="car.schedule_quote"
                            class="mt-5 grid gap-3 rounded-[1.5rem] p-4 sm:grid-cols-2"
                            :class="theme.surface"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.16em]"
                                    :class="theme.text"
                                >
                                    {{ $t("booking.rental_method") }}
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{
                                        rentalMethodLabel(
                                            form.rental_method,
                                            $t,
                                        )
                                    }}
                                </p>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-[0.16em]"
                                        :class="theme.text"
                                    >
                                        {{ $t("common.labels.total_cost") }}
                                    </p>
                                    <InfoHint
                                        :text="$t('booking.tooltips.car_total')"
                                        :icon-class="theme.text"
                                        :tooltip-class="tooltipClass"
                                    />
                                </div>
                                <p
                                    class="mt-1 text-sm font-semibold text-foreground"
                                >
                                    {{
                                        formatMYR(
                                            car.schedule_quote.total_cost,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-6 flex items-center justify-between gap-4 border-t pt-5"
                            :class="theme.border"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.16em]"
                                    :class="theme.text"
                                >
                                    {{ $t("booking.from_rate") }}
                                </p>
                                <p
                                    class="mt-1 text-lg font-extrabold"
                                    :class="theme.text"
                                >
                                    {{ formatMYR(car.hourly_rent_price) }}
                                    <span
                                        class="text-sm font-semibold"
                                        :class="theme.textSoft"
                                    >
                                        {{ $t("car.hour_suffix") }}
                                    </span>
                                </p>
                                <p class="text-xs" :class="theme.textSoft">
                                    {{
                                        formatMYR(car.daily_rent_price)
                                    }}{{ $t("car.day_suffix") }}
                                </p>
                            </div>

                            <Link
                                :href="confirmationUrl(car.id)"
                                class="inline-flex min-h-12 items-center justify-center rounded-2xl px-5 text-sm font-semibold transition-colors"
                                :class="theme.solidButton"
                            >
                                {{ $t("booking.choose_this_car") }}
                            </Link>
                        </div>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="rounded-[2rem] border border-dashed px-6 py-10 text-center"
                :class="[theme.border, theme.surface]"
            >
                <h3 class="text-lg font-semibold text-foreground">
                    {{ $t("booking.no_matching_cars_title") }}
                </h3>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    {{ $t("booking.no_matching_cars_copy") }}
                </p>
                <button
                    type="button"
                    class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl px-4 text-sm font-semibold transition-colors"
                    :class="theme.solidButton"
                    @click="goBackToSchedule"
                >
                    {{ $t("booking.back_to_schedule") }}
                </button>
            </div>
        </div>
    </section>
</template>
