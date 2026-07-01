<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { bookingStatusLabelKey, bookingStatusTheme } from "@/lib/bookingStatus";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { formatMYR } from "@/lib/car";
import { rentalMethodLabel, rentalPeriod } from "@/lib/rental";
import { computed } from "vue";
import { useI18n } from "@/lib/i18n";

const page = usePage();

const data = page.props;
const rentals = computed(() => page.props.rentals);
const { locale, t } = useI18n();
</script>

<template>
    <Head :title="$t('admin.dashboard')" />

    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">{{ $t("admin.dashboard") }}</h1>
            <p class="text-sm text-primary-500">
                {{ $t("admin.dashboard_welcome") }}
            </p>
        </template>

        <!-- Stats Cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Cars -->
            <div
                class="bg-white rounded-2xl border border-primary-100 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center"
                    >
                        <svg
                            class="w-6 h-6 text-primary-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M9 17a2 2 0 100-4 2 2 0 000 4zm6 0a2 2 0 100-4 2 2 0 000 4z"
                            />
                            <path
                                d="M5 17h-2a1 1 0 01-1-1v-4l2-6h12l2 6v4a1 1 0 01-1 1h-2m-8 0h8"
                            />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-foreground">
                    {{ data.cars }}
                </p>
                <p class="text-sm text-muted-foreground mt-1">{{ $t("admin.total_cars") }}</p>
            </div>

            <!-- Available Cars -->
            <div
                class="bg-white rounded-2xl border border-primary-100 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center"
                    >
                        <svg
                            class="w-6 h-6 text-green-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-foreground">
                    {{ data.availableCars }}
                </p>
                <p class="text-sm text-muted-foreground mt-1">{{ $t("admin.available_cars") }}</p>
            </div>

            <!-- Total Rentals -->
            <div
                class="bg-white rounded-2xl border border-primary-100 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center"
                    >
                        <svg
                            class="w-6 h-6 text-primary-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-foreground">
                    {{ rentals.length }}
                </p>
                <p class="text-sm text-muted-foreground mt-1">{{ $t("admin.total_rentals") }}</p>
            </div>

            <!-- Total Earnings -->
            <div
                class="bg-white rounded-2xl border border-primary-100 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center"
                    >
                        <svg
                            class="w-6 h-6 text-amber-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-foreground">
                    {{ formatMYR(data.earnings) }}
                </p>
                <p class="text-sm text-muted-foreground mt-1">{{ $t("admin.total_earnings") }}</p>
            </div>
        </div>

        <!-- Recent Rentals -->
        <div
            class="bg-white rounded-2xl border border-primary-100 overflow-hidden"
        >
            <div
                class="px-6 py-5 border-b border-primary-100/70 flex items-center justify-between"
            >
                <div>
                    <h2 class="text-base font-bold text-foreground">
                        {{ $t("admin.recent_rentals") }}
                    </h2>
                    <p class="text-xs text-primary-500 mt-0.5">
                        {{ $t("admin.latest_activity") }}
                    </p>
                </div>
                <Link
                    :href="route('rentals.index')"
                    class="text-sm text-primary-600 font-medium hover:text-primary-700 transition-colors"
                    >{{ $t("common.actions.view_all") }} -></Link
                >
            </div>
            <div class="overflow-auto">
                <table class="w-full min-w-max">
                    <thead>
                        <tr class="bg-primary-50/40 border-b border-primary-100/70">
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                ID
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                {{ $t("common.labels.customer") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                {{ $t("common.labels.car") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                {{ $t("booking.rental_period") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                {{ $t("booking.method") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                {{ $t("common.labels.total") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                {{ $t("common.labels.status") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100/70">
                        <tr
                            class="hover:bg-primary-50/40 transition-colors"
                            v-for="(rental, index) in rentals"
                            :key="rental.id"
                        >
                            <td
                                class="px-6 py-4 text-sm font-medium text-foreground"
                            >
                                # {{ index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white text-xs font-bold shrink-0"
                                    >
                                        {{ rental.user.name.charAt(0) }}
                                    </div>
                                    <span class="text-sm text-foreground">{{
                                        rental.user.name
                                    }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground">
                                {{ rental.car.name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-muted-foreground">
                                {{ rentalPeriod(rental, locale) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-muted-foreground">
                                {{ rentalMethodLabel(rental.rental_method, t) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm font-semibold text-foreground"
                            >
                                {{ formatMYR(rental.total_cost) }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        `inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg`,
                                        bookingStatusTheme(rental.status).badge,
                                    ]"
                                    >{{ $t(bookingStatusLabelKey(rental.status)) }}</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
