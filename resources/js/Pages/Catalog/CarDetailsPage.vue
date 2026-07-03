<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import CarAvailabilityCalendar from "@/Components/CarAvailabilityCalendar.vue";
import FuelEstimator from "@/Components/FuelEstimator.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { carStatusLabel, formatMYR, imageUrl, isCarAvailable } from "@/lib/car";
import { useI18n } from "@/lib/i18n";
import { ref } from "vue";

const page = usePage();

const car = page.props.car;
const authUser = page.props.auth?.user;
const activeImage = ref(car.images?.[0]?.path || "placeholder.png");
const { t } = useI18n();
</script>

<template>
    <Head :title="car.name" />
    <GuestLayout>
        <section class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        :href="route('cars')"
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
                    <span class="text-foreground font-medium">{{
                        car.name
                    }}</span>
                </nav>

                <div class="grid lg:grid-cols-2 gap-10">
                    <!-- Car Image -->
                    <div>
                        <div
                            class="bg-gradient-to-br from-primary-100 to-primary-100 rounded-3xl h-80 lg:h-[420px] flex items-center justify-center border border-primary-100"
                        >
                            <!-- <span class="text-9xl">🚗</span>
                            -->
                            <img
                                :src="imageUrl(activeImage)"
                                :alt="car.name"
                                class="w-full h-full object-cover rounded-3xl"
                            />
                        </div>
                        <div
                            v-if="car.images?.length > 1"
                            class="grid grid-cols-4 gap-3 mt-3"
                        >
                            <button
                                v-for="image in car.images"
                                :key="image.id"
                                type="button"
                                class="rounded-xl overflow-hidden border-2"
                                :class="
                                    activeImage === image.path
                                        ? 'border-primary-600'
                                        : 'border-transparent'
                                "
                                @click="activeImage = image.path"
                            >
                                <img
                                    :src="imageUrl(image.path)"
                                    :alt="car.name"
                                    class="w-full aspect-video object-cover"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Car Details -->
                    <div>
                        <span
                            class="inline-block px-3 py-1 text-xs font-semibold rounded-lg mb-3"
                            :class="
                                isCarAvailable(car)
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'
                            "
                            >{{ carStatusLabel(car.status, t) }}</span
                        >
                        <h1
                            class="text-3xl lg:text-4xl font-extrabold text-foreground mb-2"
                        >
                            {{ car.name }}
                        </h1>
                        <p class="text-lg text-muted-foreground mb-6">
                            {{ car.model }} • {{ car.year }}
                        </p>
                        <p
                            v-if="car.number_plate"
                            class="inline-flex mb-6 px-3 py-1.5 rounded-xl bg-primary-50 text-sm font-bold text-primary-700 uppercase tracking-wide"
                        >
                            {{ $t("car.specs.number_plate") }}: {{ car.number_plate }}
                        </p>

                        <div class="flex flex-wrap items-baseline gap-5 mb-8">
                            <p>
                                <span
                                    class="text-3xl font-extrabold text-primary-600"
                                    >{{ formatMYR(car.daily_rent_price) }}</span
                                >
                                <span class="text-lg text-primary-500">{{ $t("car.day_suffix") }}</span>
                            </p>
                            <p>
                                <span
                                    class="text-2xl font-extrabold text-primary-600"
                                    >{{ formatMYR(car.hourly_rent_price) }}</span
                                >
                                <span class="text-lg text-primary-500"
                                    >{{ $t("car.hour_suffix") }}</span
                                >
                            </p>
                        </div>

                        <!-- Specs Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="p-4 bg-primary-50/40 rounded-xl">
                                <p
                                    class="text-xs text-primary-500 uppercase tracking-wider mb-1"
                                >
                                    {{ $t("car.specs.brand") }}
                                </p>
                                <p class="text-sm font-semibold text-foreground">
                                    {{ car.brand }}
                                </p>
                            </div>
                            <div class="p-4 bg-primary-50/40 rounded-xl">
                                <p
                                    class="text-xs text-primary-500 uppercase tracking-wider mb-1"
                                >
                                    {{ $t("car.specs.model") }}
                                </p>
                                <p class="text-sm font-semibold text-foreground">
                                    {{ car.model }}
                                </p>
                            </div>
                            <div class="p-4 bg-primary-50/40 rounded-xl">
                                <p
                                    class="text-xs text-primary-500 uppercase tracking-wider mb-1"
                                >
                                    {{ $t("car.specs.year") }}
                                </p>
                                <p class="text-sm font-semibold text-foreground">
                                    {{ car.year }}
                                </p>
                            </div>
                            <div class="p-4 bg-primary-50/40 rounded-xl">
                                <p
                                    class="text-xs text-primary-500 uppercase tracking-wider mb-1"
                                >
                                    {{ $t("car.specs.type") }}
                                </p>
                                <p class="text-sm font-semibold text-foreground">
                                    {{ car.car_type }}
                                </p>
                            </div>
                            <div
                                v-if="car.number_plate"
                                class="p-4 bg-primary-50/40 rounded-xl"
                            >
                                <p
                                    class="text-xs text-primary-500 uppercase tracking-wider mb-1"
                                >
                                    {{ $t("car.specs.number_plate") }}
                                </p>
                                <p class="text-sm font-semibold text-foreground uppercase">
                                    {{ car.number_plate }}
                                </p>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div
                            class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-8 flex items-start gap-3"
                        >
                            <svg
                                class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
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

                        <CarAvailabilityCalendar :car="car" />

                        <div class="mt-6">
                            <FuelEstimator
                                :fuel-consumption-rate="car.fuel_consumption_rate"
                            />
                        </div>

                        <Link
                            v-if="isCarAvailable(car)"
                            :href="
                                authUser?.role === 'customer'
                                    ? route('customer.dashboard')
                                    : authUser
                                      ? route('cars.book', car.id)
                                      : route('login')
                            "
                            class="inline-flex items-center gap-2 w-full justify-center px-8 py-4 bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 text-white font-semibold rounded-2xl shadow-xl shadow-primary-500/25 hover:shadow-2xl hover:shadow-primary-500/40 hover:from-primary-600 hover:to-primary-800 transition-all duration-300 text-lg"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                            {{
                                authUser?.role === "customer"
                                    ? $t("booking.start_with_schedule_cta")
                                    : $t("car.book_this_car")
                            }}
                        </Link>
                        <p
                            v-else
                            class="w-full px-8 py-4 bg-primary-50 text-muted-foreground font-semibold rounded-2xl text-center"
                        >
                            {{ $t("car.car_under_maintenance") }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
