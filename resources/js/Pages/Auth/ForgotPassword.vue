<script setup>
import AccountCard from "@/Components/AccountCard.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.forgot_title')" />

        <AccountCard
            :title="$t('auth.forgot_title')"
            :description="$t('auth.forgot_copy')"
        >
            <div
                v-if="status"
                class="mb-5 p-3 rounded-xl bg-green-50 border border-green-100 text-sm font-medium text-green-700"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="email" :value="$t('auth.email')" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1.5"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <PrimaryButton
                    class="w-full"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? $t("auth.sending_link")
                            : $t("auth.send_reset_link")
                    }}
                </PrimaryButton>
            </form>
        </AccountCard>
    </GuestLayout>
</template>
