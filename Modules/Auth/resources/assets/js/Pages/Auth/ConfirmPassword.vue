<script setup>
import AccountCard from "@/Components/AccountCard.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.confirm_title')" />

        <AccountCard
            :title="$t('auth.confirm_title')"
            :description="$t('auth.confirm_copy')"
        >
            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="password" :value="$t('auth.password')" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1.5"
                        required
                        autocomplete="current-password"
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <PrimaryButton class="w-full" :disabled="form.processing">
                    {{ form.processing ? $t("auth.confirming") : $t("auth.confirm") }}
                </PrimaryButton>
            </form>
        </AccountCard>
    </GuestLayout>
</template>
