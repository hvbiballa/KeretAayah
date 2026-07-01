<script setup>
import AccountCard from "@/Components/AccountCard.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PasswordField from "@/Components/PasswordField.vue";
import PasswordRequirements from "@/Components/PasswordRequirements.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.reset_title')" />

        <AccountCard
            :title="$t('auth.reset_title')"
            :description="$t('auth.reset_password')"
        >
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
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" :value="$t('profile.new_password')" />
                    <PasswordField
                        id="password"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <PasswordRequirements
                        class="mt-3"
                        :password="form.password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel
                        for="password_confirmation"
                        :value="$t('auth.confirm_password')"
                    />
                    <PasswordField
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <PrimaryButton class="w-full" :disabled="form.processing">
                    {{
                        form.processing
                            ? $t("auth.resetting_password")
                            : $t("auth.reset_password")
                    }}
                </PrimaryButton>
            </form>
        </AccountCard>
    </GuestLayout>
</template>
