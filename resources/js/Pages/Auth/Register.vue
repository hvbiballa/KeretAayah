<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PasswordField from "@/Components/PasswordField.vue";
import PasswordRequirements from "@/Components/PasswordRequirements.vue";
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const passwordMatches = computed(
    () =>
        form.password_confirmation.length > 0 &&
        form.password === form.password_confirmation,
);

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.register_title')" />

        <div class="flex items-center justify-center p-4 py-20">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="bg-white rounded-3xl shadow-2xl p-8">
                    <h2
                        class="text-2xl font-extrabold text-foreground text-center mb-1"
                    >
                        {{ $t("auth.create_account") }}
                    </h2>
                    <p class="text-sm text-muted-foreground text-center mb-8">
                        {{ $t("auth.register_copy") }}
                    </p>

                    <form class="space-y-5" @submit.prevent="submit">
                        <div>
                            <!-- <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >Full Name</label
                            >
                            <input
                                type="text"
                                placeholder="John Doe"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            /> -->

                            <InputLabel
                                for="name"
                                :value="$t('auth.name')"
                                class="block text-sm font-medium text-foreground mb-1.5"
                            />

                            <TextInput
                                id="name"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="John Doe"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
                        </div>
                        <div>
                            <!-- <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >Email Address</label
                            >
                            <input
                                type="email"
                                placeholder="you@example.com"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            /> -->

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
                                autocomplete="username"
                                placeholder="you@example.com"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.email"
                            />
                        </div>
                        <div>
                            <!-- <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >Password</label
                            >
                            <input
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            /> -->

                            <InputLabel
                                for="password"
                                :value="$t('auth.password')"
                                class="block text-sm font-medium text-foreground mb-1.5"
                            />

                            <PasswordField
                                id="password"
                                v-model="form.password"
                                placeholder="********"
                                required
                                autocomplete="new-password"
                            />

                            <PasswordRequirements
                                class="mt-3"
                                :password="form.password"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.password"
                            />
                        </div>
                        <div>
                            <!-- <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >Confirm Password</label
                            >
                            <input
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            /> -->

                            <InputLabel
                                for="password_confirmation"
                                :value="$t('auth.confirm_password')"
                                class="block text-sm font-medium text-foreground mb-1.5"
                            />

                            <PasswordField
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                placeholder="********"
                                required
                                autocomplete="new-password"
                            />

                            <p
                                v-if="form.password_confirmation"
                                class="mt-2 text-xs"
                                :class="
                                    passwordMatches
                                        ? 'text-green-700'
                                        : 'text-red-600'
                                "
                            >
                                {{
                                    passwordMatches
                                        ? $t("auth.password_requirements.match")
                                        : form.errors.password_confirmation
                                }}
                            </p>

                            <InputError
                                class="mt-2"
                                :message="form.errors.password_confirmation"
                            />
                        </div>
                        <button
                            type="submit"
                            class="w-full py-3.5 bg-linear-to-r from-primary-500 via-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-md shadow-primary-500/25 hover:shadow-lg hover:shadow-primary-500/30 hover:from-primary-600 hover:to-primary-800 transition-all duration-200"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{
                                form.processing
                                    ? $t("auth.creating_account")
                                    : $t("auth.create_account")
                            }}
                        </button>
                    </form>

                    <p class="text-sm text-muted-foreground text-center mt-6">
                        {{ $t("auth.already_account") }}
                        <Link
                            href="/login"
                            class="text-primary-600 font-semibold hover:text-primary-700"
                            >{{ $t("auth.sign_in") }}</Link
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

        <!-- <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form> -->
    </GuestLayout>
</template>
