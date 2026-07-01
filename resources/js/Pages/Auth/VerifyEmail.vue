<script setup>
import AccountCard from "@/Components/AccountCard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent",
);
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.verify_title')" />

        <AccountCard
            :title="$t('auth.verify_title')"
            :description="$t('auth.verify_copy')"
        >
            <div
                v-if="verificationLinkSent"
                class="mb-5 p-3 rounded-xl bg-green-50 border border-green-100 text-sm font-medium text-green-700"
            >
                {{ $t("auth.verification_sent") }}
            </div>

            <form @submit.prevent="submit" class="space-y-3">
                <PrimaryButton class="w-full" :disabled="form.processing">
                    {{
                        form.processing
                            ? $t("auth.sending_email")
                            : $t("auth.resend_verification")
                    }}
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full px-5 py-3 rounded-xl text-sm font-semibold text-muted-foreground hover:bg-primary-50 transition-colors"
                >
                    {{ $t("nav.log_out") }}
                </Link>
            </form>
        </AccountCard>
    </GuestLayout>
</template>
