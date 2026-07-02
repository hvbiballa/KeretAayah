<script setup>
import AlertDialog from "@/Components/ui/alert-dialog/AlertDialog.vue";
import AlertDialogAction from "@/Components/ui/alert-dialog/AlertDialogAction.vue";
import AlertDialogCancel from "@/Components/ui/alert-dialog/AlertDialogCancel.vue";
import AlertDialogContent from "@/Components/ui/alert-dialog/AlertDialogContent.vue";
import AlertDialogDescription from "@/Components/ui/alert-dialog/AlertDialogDescription.vue";
import AlertDialogFooter from "@/Components/ui/alert-dialog/AlertDialogFooter.vue";
import AlertDialogHeader from "@/Components/ui/alert-dialog/AlertDialogHeader.vue";
import AlertDialogTitle from "@/Components/ui/alert-dialog/AlertDialogTitle.vue";
import AlertDialogTrigger from "@/Components/ui/alert-dialog/AlertDialogTrigger.vue";

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { bookingStatusLabelKey, bookingStatusTheme } from "@/lib/bookingStatus";
import { Link, router, usePage } from "@inertiajs/vue3";
import { formatMYR } from "@/lib/car";
import { rentalMethodLabel, rentalPeriod } from "@/lib/rental";
import { computed } from "vue";
import { useI18n } from "@/lib/i18n";

const page = usePage();
// const rentals = page.props.rentals;
const rentals = computed(() => page.props.rentals);
const { locale, t } = useI18n();

const deleteRental = (id) => {
    router.delete(route("rentals.destroy", id));
};
</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">{{ $t("admin.rentals.manage_title") }}</h1>
            <p class="text-sm text-primary-500">
                {{ $t("admin.rentals.manage_subtitle") }}
            </p>
        </template>

        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-muted-foreground">
                {{ $t("common.labels.showing") }}
                <strong class="text-foreground">{{ rentals?.length }}</strong>
                {{ $t("nav.rentals") }}
            </p>
        </div>

        <div
            class="bg-white rounded-2xl border border-primary-100 overflow-auto shadow-sm"
        >
            <table class="w-full">
                <thead>
                    <tr class="bg-primary-50/40 border-b border-primary-100/70">
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("admin.rentals.rental_id") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("admin.rentals.customer_name") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("admin.rentals.car_details") }}
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
                            {{ $t("common.labels.total_cost") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("common.labels.status") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("common.labels.actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100/70">
                    <tr
                        class="hover:bg-primary-50/40 transition-colors"
                        v-for="rental in rentals"
                        :key="rental.id"
                    >
                        <td
                            class="px-6 py-4 text-sm font-semibold text-foreground"
                        >
                            {{ rental.id }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm font-semibold text-foreground"
                        >
                            {{ rental.user.name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            {{ rental.car.brand }} • {{ rental.car.model }}
                        </td>
                        <td class="px-6 py-4">
                            <span>{{ rentalPeriod(rental, locale) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
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
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('rentals.edit', rental.id)"
                                    class="p-1.5 rounded-lg hover:bg-primary-50 text-primary-600 transition-colors"
                                    ><svg
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        /></svg
                                ></Link>
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <button
                                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition-colors"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>
                                        </button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle
                                                >{{ $t("admin.rentals.delete_title") }}</AlertDialogTitle
                                            >
                                            <AlertDialogDescription>
                                                {{ $t("common.dialog.cannot_be_undone") }}
                                                {{ $t("admin.rentals.delete_copy") }}
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>

                                        <AlertDialogFooter
                                            class="dialog-footer"
                                        >
                                            <AlertDialogCancel
                                                >{{ $t("common.actions.cancel") }}</AlertDialogCancel
                                            >

                                            <AlertDialogAction
                                                @click="deleteRental(rental.id)"
                                            >
                                                {{ $t("common.actions.delete") }}
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
