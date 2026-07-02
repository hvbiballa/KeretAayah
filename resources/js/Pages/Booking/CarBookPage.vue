<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import TouchDateTimePicker from "@/Components/TouchDateTimePicker.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { carImageUrl, formatMYR } from "@/lib/car";
import { computed } from "vue";

const page = usePage();

const car = page.props.car;

const form = useForm({
    car_id: car.id,
    rental_method: "daily",
    pickup_date: "",
    pickup_time: "",
    return_date: "",
    return_time: "",
});

const duration = computed(() => {
    if (!form.pickup_date || !form.return_date) return 0;

    const start = new Date(`${form.pickup_date}T${form.pickup_time || "00:00"}`);
    const end = new Date(`${form.return_date}T${form.return_time || "00:00"}`);

    const diffTime = end - start;

    if (form.rental_method === "hourly") {
        return diffTime > 0
            ? Math.round((diffTime / (1000 * 60 * 60)) * 100) / 100
            : 0;
    }

    const startDate = new Date(`${form.pickup_date}T00:00`);
    const endDate = new Date(`${form.return_date}T00:00`);
    const dayDiff = endDate - startDate;

    return dayDiff > 0 ? Math.ceil(dayDiff / (1000 * 60 * 60 * 24)) : 0;
});

const rate = computed(() =>
    form.rental_method === "hourly"
        ? car.hourly_rent_price
        : car.daily_rent_price,
);

const totalCost = computed(() => {
    return duration.value * rate.value;
});

const malaysiaDateValue = (date) =>
    new Intl.DateTimeFormat("en-CA", {
        timeZone: "Asia/Kuala_Lumpur",
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    }).format(date);

const minStartDate = malaysiaDateValue(new Date());

const minEndDate = computed(() => {
    if (!form.pickup_date) return minStartDate;
    return form.pickup_date;
});

const submit = () => {
    form.post("/rentals", {
        onSuccess: () => {
            // if (page.props.flash.success) {
            //     alert("Booking successful!");
            // }
            // if (page.props.flash.error) {
            //     alert(page.props.flash.error);
            // }
        },
    });
};
</script>

<template>
    <GuestLayout>
        <section class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav
                    class="flex items-center gap-2 text-sm text-muted-foreground mb-8"
                >
                    <Link
                        href="/"
                        class="hover:text-primary-600 transition-colors"
                        >{{ $t("nav.home") }}</Link
                    >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    <Link
                        href="/cars"
                        class="hover:text-primary-600 transition-colors"
                        >{{ $t("nav.cars") }}</Link
                    >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-foreground font-medium">{{ $t("booking.book_car") }}</span>
                </nav>

                <h1 class="text-3xl font-extrabold text-foreground mb-8">
                    {{ $t("booking.book_your_car") }}
                </h1>

                <!-- Selected Car Summary -->
                <div
                    class="bg-white rounded-2xl border border-primary-100 p-5 mb-8 flex flex-wrap items-center gap-5"
                >
                    <div
                        class="w-24 h-20 bg-linear-to-br from-primary-50 to-primary-50 rounded-xl flex items-center justify-center shrink-0"
                    >
                        <!-- <span class="text-4xl">🚗</span> -->
                        <img :src="carImageUrl(car)" alt="Image" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-foreground">
                            {{ car.name }}
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            {{ car.brand }} • {{ car.model }} • {{ car.year }} •
                            {{ car.car_type }}
                        </p>
                        <p
                            v-if="car.number_plate"
                            class="inline-flex mt-2 px-2.5 py-1 rounded-lg bg-primary-50 text-xs font-semibold text-primary-700 uppercase"
                        >
                            {{ $t("car.specs.number_plate") }}: {{ car.number_plate }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-extrabold text-primary-600">
                            {{ formatMYR(car.daily_rent_price) }}
                        </p>
                        <p class="text-xs text-primary-500">{{ $t("car.per_day") }}</p>
                        <p class="text-sm font-bold text-primary-600 mt-1">
                            {{ formatMYR(car.hourly_rent_price) }}
                        </p>
                        <p class="text-xs text-primary-500">{{ $t("car.per_hour") }}</p>
                    </div>
                </div>

                <!-- Booking Form -->
                <div
                    class="bg-white rounded-2xl border border-primary-100 p-5 sm:p-8"
                >
                    <h2 class="text-xl font-bold text-foreground mb-6">
                        {{ $t("booking.rental_details") }}
                    </h2>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-2"
                                >{{ $t("booking.rental_method") }}</label
                            >
                            <div class="grid sm:grid-cols-2 gap-3">
                                <label
                                    class="p-4 border rounded-xl cursor-pointer"
                                    :class="
                                        form.rental_method === 'daily'
                                            ? 'border-primary-600 bg-primary-50'
                                            : 'border-primary-100'
                                    "
                                >
                                    <input
                                        v-model="form.rental_method"
                                        type="radio"
                                        value="daily"
                                        class="sr-only"
                                    />
                                    <span
                                        class="block font-semibold text-foreground"
                                        >{{ $t("booking.daily_rental") }}</span
                                    >
                                    <span class="text-xs text-muted-foreground"
                                        >{{ $t("booking.daily_hint") }} -
                                        {{ formatMYR(car.daily_rent_price) }}{{ $t("car.day_suffix") }}</span
                                    >
                                </label>
                                <label
                                    class="p-4 border rounded-xl cursor-pointer"
                                    :class="
                                        form.rental_method === 'hourly'
                                            ? 'border-primary-600 bg-primary-50'
                                            : 'border-primary-100'
                                    "
                                >
                                    <input
                                        v-model="form.rental_method"
                                        type="radio"
                                        value="hourly"
                                        class="sr-only"
                                    />
                                    <span
                                        class="block font-semibold text-foreground"
                                        >{{ $t("booking.hourly_rental") }}</span
                                    >
                                    <span class="text-xs text-muted-foreground"
                                        >{{ $t("booking.hourly_hint") }} -
                                        {{ formatMYR(car.hourly_rent_price) }}{{ $t("car.hour_suffix") }}</span
                                    >
                                </label>
                            </div>
                            <p class="text-xs text-red-500 mt-2">
                                {{ form.errors.rental_method }}
                            </p>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label
                                    class="block text-sm font-medium text-foreground mb-1.5"
                                    >{{ $t("booking.pickup_date") }}</label
                                >
                                <TouchDateTimePicker
                                    v-model="form.pickup_date"
                                    type="date"
                                    :min="minStartDate"
                                    :availability-car-id="car.id"
                                    :placeholder="$t('booking.select_pickup_date')"
                                    :aria-label="$t('booking.pickup_date')"
                                />
                                <p class="text-xs text-red-500 mt-2">
                                    {{ form.errors.pickup_date }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-foreground mb-1.5"
                                    >{{
                                        form.rental_method === "hourly"
                                            ? $t("booking.pickup_time")
                                            : $t("booking.preferred_pickup_time")
                                    }}</label
                                >
                                <TouchDateTimePicker
                                    v-model="form.pickup_time"
                                    type="time"
                                    :placeholder="$t('booking.select_pickup_time')"
                                    :aria-label="$t('booking.pickup_time')"
                                />
                                <p class="text-xs text-red-500 mt-2">
                                    {{ form.errors.pickup_time }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-foreground mb-1.5"
                                    >{{ $t("booking.return_date") }}</label
                                >
                                <TouchDateTimePicker
                                    v-model="form.return_date"
                                    type="date"
                                    :min="minEndDate"
                                    :availability-car-id="car.id"
                                    :placeholder="$t('booking.select_return_date')"
                                    :aria-label="$t('booking.return_date')"
                                />
                                <p class="text-xs text-red-500 mt-2">
                                    {{ form.errors.return_date }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-foreground mb-1.5"
                                    >{{
                                        form.rental_method === "hourly"
                                            ? $t("booking.return_time")
                                            : $t("booking.preferred_return_time")
                                    }}</label
                                >
                                <TouchDateTimePicker
                                    v-model="form.return_time"
                                    type="time"
                                    :placeholder="$t('booking.select_return_time')"
                                    :aria-label="$t('booking.return_time')"
                                />
                                <p class="text-xs text-red-500 mt-2">
                                    {{ form.errors.return_time }}
                                </p>
                            </div>
                        </div>

                        <!-- Cost Breakdown -->
                        <div class="bg-primary-50/40 rounded-xl p-5 mt-2">
                            <h3
                                class="text-sm font-semibold text-foreground mb-3"
                            >
                                {{ $t("booking.cost_breakdown") }}
                            </h3>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >{{
                                            form.rental_method === "hourly"
                                                ? $t("booking.hourly_rate")
                                                : $t("booking.daily_rate")
                                        }}</span
                                    >
                                    <span class="text-foreground font-medium"
                                        >{{ formatMYR(rate) }}</span
                                    >
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">{{ $t("common.labels.duration") }}</span>
                                    <span class="text-foreground font-medium"
                                        >{{ duration }}
                                        {{
                                            form.rental_method === "hourly"
                                                ? $t("booking.units.hours")
                                                : $t("booking.units.days")
                                        }}</span
                                    >
                                </div>
                                <div
                                    class="border-t border-primary-100 pt-2 mt-2 flex justify-between"
                                >
                                    <span class="font-bold text-foreground"
                                        >{{ $t("common.labels.total_cost") }}</span
                                    >
                                    <span
                                        class="font-extrabold text-primary-600 text-lg"
                                        >{{ formatMYR(totalCost) }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div
                            class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3"
                        >
                            <svg
                                class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">
                                    {{ $t("car.manual_payment_title") }}
                                </p>
                                <p class="text-xs text-amber-600 mt-0.5">
                                    {{ $t("car.manual_payment_copy") }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 text-white font-semibold rounded-2xl shadow-xl shadow-primary-500/25 hover:shadow-2xl hover:shadow-primary-500/40 hover:from-primary-600 hover:to-primary-800 transition-all duration-300 text-lg disabled:opacity-50 disabled:pointer-events-none"
                        >
                            {{ $t("booking.confirm_booking") }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
