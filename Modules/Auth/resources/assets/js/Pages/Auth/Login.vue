<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.login_title')" />

        <!-- <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-muted-foreground dark:text-gray-400"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form> -->

        <div class="bg-linear-to-br flex items-center justify-center p-4 py-20">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="bg-white rounded-3xl shadow-2xl p-8">
                    <h2
                        class="text-2xl font-extrabold text-foreground text-center mb-1"
                    >
                        {{ $t("auth.welcome_back") }}
                    </h2>
                    <p class="text-sm text-muted-foreground text-center mb-8">
                        {{ $t("auth.login_copy") }}
                    </p>

                    <div
                        v-if="status"
                        class="mb-5 p-3 rounded-xl bg-green-50 border border-green-100 text-sm font-medium text-green-700"
                    >
                        {{ status }}
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
                        <div>
                            <InputLabel
                                for="email"
                                :value="$t('auth.email')"
                                class="block text-sm font-medium text-foreground mb-1.5"
                            />

                            <TextInput
                                id="email"
                                type="email"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.email"
                            />
                        </div>
                        <div>
                            <div
                                class="flex items-center justify-between mb-1.5"
                            >
                                <InputLabel
                                    for="password"
                                    :value="$t('auth.password')"
                                    class="block text-sm font-medium text-foreground"
                                />
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs text-primary-600 hover:text-primary-700 font-medium"
                                    >{{ $t("auth.forgot_password") }}</Link
                                >
                            </div>

                            <TextInput
                                id="password"
                                type="password"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.password"
                            />
                        </div>
                        <label class="flex items-center gap-2">
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                            />
                            <span class="text-sm text-muted-foreground"
                                >{{ $t("auth.remember_me") }}</span
                            >
                        </label>
                        <button
                            type="submit"
                            class="w-full py-3.5 bg-linear-to-r from-primary-500 via-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-md shadow-primary-500/25 hover:shadow-lg hover:shadow-primary-500/30 hover:from-primary-600 hover:to-primary-800 transition-all duration-200"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? $t("auth.logging_in") : $t("auth.login") }}
                        </button>
                    </form>

                    <p class="text-sm text-muted-foreground text-center mt-6">
                        {{ $t("auth.no_account") }}
                        <Link
                            href="/register"
                            class="text-primary-600 font-semibold hover:text-primary-700"
                            >{{ $t("nav.sign_up") }}</Link
                        >
                    </p>
                </div>

                <p class="text-center mt-6">
                    <Link
                        href="/"
                        class="text-sm text-black/70 hover:text-black transition-colors flex items-center justify-center gap-1.5"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ $t("nav.back_to_home") }}
                    </Link>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>
