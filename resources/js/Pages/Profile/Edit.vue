<script setup>
import CustomerVerificationSection from "@/Components/CustomerVerificationSection.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    verification: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const showVerification = computed(() => page.props.auth?.user?.role === "customer");
</script>

<template>
    <Head :title="$t('profile.profile')" />

    <GuestLayout>
        <section class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-foreground">
                        {{ $t("profile.settings") }}
                    </h1>
                    <p class="text-sm text-muted-foreground mt-2">
                        {{ $t("profile.settings_copy") }}
                    </p>
                </div>

                <div class="space-y-6">
                    <div
                        class="bg-white rounded-2xl border border-primary-100 p-6 sm:p-8 shadow-sm"
                    >
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>

                    <CustomerVerificationSection
                        v-if="showVerification"
                        :verification="props.verification"
                    />

                    <div
                        class="bg-white rounded-2xl border border-primary-100 p-6 sm:p-8 shadow-sm"
                    >
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>

                    <div
                        class="bg-white rounded-2xl border border-red-100 p-6 sm:p-8 shadow-sm"
                    >
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
