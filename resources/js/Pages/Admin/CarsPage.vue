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
import { Head, Link, router } from "@inertiajs/vue3";
import { carImageUrl, carStatusLabel, formatMYR, isCarAvailable } from "@/lib/car";
import { useI18n } from "@/lib/i18n";

defineProps({
    cars: Array,
});

const { t } = useI18n();
</script>

<template>
    <Head :title="$t('car.admin.manage_title')" />

    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">{{ $t("car.admin.manage_title") }}</h1>
            <p class="text-sm text-primary-500">
                {{ $t("car.admin.manage_subtitle") }}
            </p>
        </template>

        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-muted-foreground">
                {{ $t("common.labels.showing") }}
                <strong class="text-foreground">{{ cars?.length }}</strong> {{ $t("car.showing_cars") }}
            </p>
            <Link
                :href="route('cars.create')"
                class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-md shadow-primary-500/20"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path d="M12 4v16m8-8H4" />
                </svg>
                {{ $t("car.admin.add_new") }}
            </Link>
        </div>

        <div
            class="bg-white rounded-2xl border border-primary-100 overflow-auto shadow-sm"
        >
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
                            {{ $t("common.labels.image") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("common.labels.name") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("car.admin.number_plate") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("common.labels.brand") }} / {{ $t("common.labels.model") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("common.labels.type") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("common.labels.year") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("car.admin.daily_price") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("car.admin.hourly_price") }}
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
                        v-for="car in cars"
                        :key="car.id"
                    >
                        <td
                            class="px-6 py-4 text-sm font-semibold text-foreground"
                        >
                            {{ car.id }}
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="w-14 h-10 bg-primary-50 rounded-lg flex items-center justify-center"
                            >
                                <img
                                    :src="carImageUrl(car)"
                                    alt="Image"
                                />
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 text-sm font-semibold text-foreground"
                        >
                            {{ car.name }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex px-2.5 py-1 rounded-lg bg-primary-50 text-xs font-semibold text-primary-700 uppercase"
                            >
                                {{ car.number_plate || $t("common.labels.not_set") }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            {{ car.brand }} • {{ car.model }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2 py-1 bg-primary-50 text-muted-foreground text-xs font-medium rounded-lg"
                                >{{ car.car_type }}</span
                            >
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            {{ car.year }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm font-semibold text-foreground"
                        >
                            {{ formatMYR(car.daily_rent_price) }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm font-semibold text-foreground"
                        >
                            {{ formatMYR(car.hourly_rent_price) }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="[
                                    `inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg`,
                                    isCarAvailable(car)
                                        ? `bg-green-50 text-green-700`
                                        : `bg-red-50 text-red-700`,
                                ]"
                                ><span
                                    :class="[
                                        'w-1.5 h-1.5 rounded-full',
                                        isCarAvailable(car)
                                            ? `bg-green-500`
                                            : `bg-red-500`,
                                    ]"
                                ></span
                                >{{ carStatusLabel(car.status, t) }}</span
                            >
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('cars.edit', car.id)"
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
                                                >{{ $t("car.admin.delete_title") }}</AlertDialogTitle
                                            >
                                            <AlertDialogDescription>
                                                {{ $t("common.dialog.cannot_be_undone") }}
                                                {{ $t("car.admin.delete_copy") }}
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>

                                        <AlertDialogFooter
                                            class="dialog-footer"
                                        >
                                            <AlertDialogCancel
                                                >{{ $t("common.actions.cancel") }}</AlertDialogCancel
                                            >

                                            <AlertDialogAction
                                                @click="
                                                    router.delete(
                                                        route(
                                                            'cars.destroy',
                                                            car.id,
                                                        ),
                                                    )
                                                "
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
