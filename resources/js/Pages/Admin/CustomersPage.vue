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
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const pages = usePage();

// const customers = pages.props.customers;
const customers = computed(() => pages.props.customers);

const deleteCustomer = (id) => {
    router.delete(route("customers.destroy", id));
};
</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">
                {{ $t("admin.customers.manage_title") }}
            </h1>
            <p class="text-sm text-primary-500">
                {{ $t("admin.customers.manage_subtitle") }}
            </p>
        </template>

        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-muted-foreground">
                {{ $t("common.labels.showing") }}
                <strong class="text-foreground">{{ customers?.length }}</strong>
                {{ $t("common.labels.customer") }}
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
                            {{ $t("admin.customers.customer_name") }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("common.labels.email") }}
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                        >
                            {{ $t("admin.customers.rental_history") }}
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
                        v-if="customers.length > 0"
                        v-for="customer in customers"
                        :key="customer.id"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 shrink-0 rounded-full bg-linear-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white text-xs font-bold"
                                >
                                    {{ customer.name.charAt(0) }}
                                </div>
                                <Link
                                    :href="route('customers.show', customer.id)"
                                    class="text-sm font-semibold text-foreground"
                                    >{{ customer.name }}</Link
                                >
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            {{ customer.email }}
                        </td>
                        <td class="px-6 py-4">
                            <Link
                                :href="route('customers.show', customer.id)"
                                class="text-primary-600 hover:text-primary-700 text-sm font-medium"
                                >{{ customer.rentals.length }} {{ $t("admin.customers.rentals") }}</Link
                            >
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
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
                                                >{{ $t("admin.customers.delete_title") }}</AlertDialogTitle
                                            >
                                            <AlertDialogDescription>
                                                {{ $t("common.dialog.cannot_be_undone") }}
                                                {{ $t("admin.customers.delete_copy") }}
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
                                                    deleteCustomer(customer.id)
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
