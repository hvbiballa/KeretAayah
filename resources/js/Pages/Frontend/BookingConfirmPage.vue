<script setup>
import BookingVerificationGate from "@/Components/BookingVerificationGate.vue";
import InfoHint from "@/Components/InfoHint.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { carImageAlt, carImageUrl, formatMYR } from "@/lib/car";
import { rentalMethodLabel, rentalMethodTheme } from "@/lib/rental";
import { computed } from "vue";
import { useI18n } from "@/lib/i18n";

const page = usePage();
const { t } = useI18n();

const car = computed(() => page.props.car);
const schedule = computed(() => page.props.schedule);
const quote = computed(() => page.props.quote);
const bookingBlocked = computed(() => page.props.bookingBlocked);
const bookingBlockReason = computed(() => page.props.bookingBlockReason);
const auth = computed(() => page.props.auth || {});
const emailVerified = computed(() => Boolean(auth.value.user?.email_verified_at));
const verification = computed(() => auth.value.verification);
const bookingReady = computed(
    () => emailVerified.value && verification.value?.can_book && !bookingBlocked.value,
);
const showVerificationGate = computed(
    () => Boolean(verification.value?.can_submit_documents),
);
const theme = computed(() => rentalMethodTheme(schedule.value.rental_method));
const tooltipClass = computed(() => `${theme.value.card} ${theme.value.border}`);

const form = useForm({
    car_id: car.value.id,
    rental_method: schedule.value.rental_method,
    pickup_date: schedule.value.pickup_date,
    pickup_time: schedule.value.pickup_time,
    return_date: schedule.value.return_date,
    return_time: schedule.value.return_time,
});

const submit = () => {
    if (!bookingReady.value) return;

    form.post(route("rentals.store"));
};
</script>

<template>
    <GuestLayout>
        <section class="py-10" :class="theme.pageBackground">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                    <Link :href="route('customer.dashboard')" class="transition-colors" :class="theme.link">
                        {{ $t("booking.customer_dashboard") }}
                    </Link>
                    <span>/</span>
                    <span>{{ $t("booking.confirm_step") }}</span>
                </nav>

                <div class="mt-5 max-w-3xl">
                    <p class="text-sm font-semibold tracking-[0.18em]" :class="theme.text">
                        {{ $t("booking.confirm_step") }}
                    </p>
                    <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-foreground">
                        {{ $t("booking.confirm_page_title") }}
                    </h1>
                    <p class="mt-3 text-base leading-7 text-muted-foreground">
                        {{ $t("booking.confirm_page_copy") }}
                    </p>
                </div>

                <div class="mt-8 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                    <div class="space-y-6">
                        <section
                            class="rounded-[2rem] border p-6 shadow-[0_24px_80px_-36px_rgba(30,41,59,0.35)]"
                            :class="[theme.card, theme.border]"
                        >
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="max-w-2xl">
                                    <p class="text-sm font-semibold tracking-[0.18em]" :class="theme.text">
                                        {{ $t("booking.choose_car_step") }}
                                    </p>
                                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-foreground">
                                        {{ car.name }}
                                    </h2>
                                    <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                        {{ car.brand }} · {{ car.model }} · {{ car.year }} · {{ car.car_type }}
                                    </p>
                                </div>
                                <Link
                                    :href="route('customer.dashboard', schedule)"
                                    class="text-sm font-semibold transition-colors"
                                    :class="theme.link"
                                >
                                    {{ $t("booking.edit_schedule") }}
                                </Link>
                            </div>

                            <div class="mt-6 overflow-hidden rounded-[1.75rem]" :class="theme.surface">
                                <img
                                    :src="carImageUrl(car)"
                                    :alt="carImageAlt(car, $t)"
                                    class="h-64 w-full object-cover"
                                />
                            </div>

                            <div class="mt-6 grid gap-4 rounded-[1.75rem] p-5 text-white sm:grid-cols-2" :class="theme.panel">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em]" :class="theme.panelSubtext">
                                        {{ $t("booking.rental_method") }}
                                    </p>
                                    <p class="mt-2 text-lg font-semibold">
                                        {{ rentalMethodLabel(schedule.rental_method, t) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em]" :class="theme.panelSubtext">
                                        {{ $t("common.labels.total_cost") }}
                                    </p>
                                    <p class="mt-2 text-lg font-semibold">
                                        {{ formatMYR(quote.total_cost) }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <BookingVerificationGate
                            v-if="showVerificationGate"
                            :email-verified="emailVerified"
                            :verification="verification"
                        />
                    </div>

                    <aside class="space-y-6">
                        <section
                            class="rounded-[2rem] border p-6 shadow-[0_24px_80px_-36px_rgba(30,41,59,0.35)]"
                            :class="[theme.card, theme.border]"
                        >
                            <p class="text-sm font-semibold tracking-[0.18em]" :class="theme.text">
                                {{ $t("booking.schedule_summary") }}
                            </p>
                            <div class="mt-5 space-y-4">
                                <div class="rounded-[1.5rem] p-4" :class="theme.surface">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                        {{ $t("booking.pickup_time") }}
                                    </p>
                                    <p class="mt-1 text-sm font-semibold text-foreground">
                                        {{ schedule.pickup_date }} · {{ schedule.pickup_time }}
                                    </p>
                                </div>
                                <div class="rounded-[1.5rem] p-4" :class="theme.surface">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                        {{ $t("booking.return_time") }}
                                    </p>
                                    <p class="mt-1 text-sm font-semibold text-foreground">
                                        {{ schedule.return_date }} · {{ schedule.return_time }}
                                    </p>
                                </div>
                                <div class="rounded-[1.5rem] p-4" :class="theme.surface">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                        {{ $t("common.labels.duration") }}
                                    </p>
                                    <p class="mt-1 text-sm font-semibold text-foreground">
                                        {{
                                            schedule.rental_method === "hourly"
                                                ? $t("booking.schedule_summary_hourly", {
                                                      hours: Number(
                                                          schedule.duration_units,
                                                      ),
                                                  })
                                                : $t("booking.schedule_summary_daily", {
                                                      days: Number(
                                                          schedule.duration_units,
                                                      ),
                                                  })
                                        }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section
                            class="rounded-[2rem] border p-6 shadow-[0_24px_80px_-36px_rgba(30,41,59,0.35)]"
                            :class="[theme.card, theme.border]"
                        >
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold tracking-[0.18em]" :class="theme.text">
                                    {{ $t("booking.payment_next_step") }}
                                </p>
                                <InfoHint
                                    :text="$t('booking.tooltips.confirm_booking')"
                                    :icon-class="theme.text"
                                    :tooltip-class="tooltipClass"
                                />
                            </div>
                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-foreground">
                                {{ $t("booking.pay_after_confirmation_title") }}
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                {{ $t("booking.pay_after_confirmation_copy") }}
                            </p>

                            <div
                                v-if="bookingBlocked"
                                class="mt-5 rounded-[1.5rem] border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800"
                            >
                                {{ bookingBlockReason }}
                            </div>

                            <button
                                type="button"
                                :disabled="form.processing || !bookingReady"
                                class="mt-6 inline-flex min-h-14 w-full items-center justify-center rounded-2xl px-6 text-sm font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-60"
                                :class="theme.solidButton"
                                @click="submit"
                            >
                                {{ $t("booking.confirm_booking") }}
                            </button>

                            <p
                                v-if="!bookingReady"
                                class="mt-3 text-xs leading-6 text-muted-foreground"
                            >
                                {{ $t("booking.confirm_blocked_copy") }}
                            </p>
                        </section>
                    </aside>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
