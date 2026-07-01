<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import { computed, ref, watch } from "vue";
import { toast, Toaster } from "vue-sonner";

const baseClasses =
    "flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200";

const activeClasses = "bg-primary-600/20 text-primary-400";

const inactiveClasses =
    "text-primary-100 hover:bg-surface-dark-2 hover:text-white";

const page = usePage();
const isStaff = computed(() => page.props.auth.user.role === "staff");

// Watch for changes in Inertia's flash session data
watch(
    () => page.props.flash,
    (flash) => {
        if (flash && flash.success) {
            toast.success(flash.success);
        }
        if (flash && flash.error) {
            toast.error(flash.error);
        }
    },
    { deep: true, immediate: true },
);

const isSidebarOpen = ref(false);
</script>

<template>
    <div class="flex min-h-screen bg-primary-50/40">
        <div
            v-if="isSidebarOpen"
            @click="isSidebarOpen = false"
            class="fixed inset-0 bg-black/50 backdrop-blur-xs z-40 lg:hidden"
        ></div>

        <aside
            class="w-64 bg-surface-dark text-white flex flex-col fixed inset-y-0 left-0 z-50 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div
                class="p-5 border-b border-white/10 flex items-center justify-between"
            >
                <Link
                    :href="isStaff ? route('payments.index') : route('dashboard')"
                    class="flex items-center gap-2.5"
                >
                    <div
                        class="w-9 h-9 rounded-xl overflow-hidden shadow-sm shadow-black/20"
                    >
                        <img
                            src="/icon-white-red-bg.png"
                            alt="KeretaAyah"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div>
                        <span class="text-lg font-bold">KeretaAyah</span>
                        <span
                            class="block text-[10px] uppercase tracking-widest text-primary-500 -mt-0.5"
                            >{{
                                isStaff
                                    ? $t("nav.staff_panel")
                                    : $t("nav.admin_panel")
                            }}</span
                        >
                    </div>
                </Link>

                <button
                    @click="isSidebarOpen = false"
                    class="p-1 rounded-lg text-primary-500 hover:bg-primary-950 hover:text-white lg:hidden"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <p
                    v-if="!isStaff"
                    class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-muted-foreground"
                >
                    {{ $t("nav.main") }}
                </p>
                <Link
                    v-if="!isStaff"
                    :href="route('dashboard')"
                    :class="[
                        baseClasses,
                        route().current('dashboard')
                            ? activeClasses
                            : inactiveClasses,
                    ]"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                        />
                    </svg>
                    {{ $t("nav.dashboard") }}
                </Link>

                <p
                    class="px-3 mt-6 mb-2 text-[10px] font-semibold uppercase tracking-widest text-muted-foreground"
                >
                    {{ isStaff ? $t("nav.operations") : $t("nav.management") }}
                </p>
                <Link
                    v-if="!isStaff"
                    :href="route('cars.index')"
                    :class="[
                        baseClasses,
                        route().current('cars.*')
                            ? activeClasses
                            : inactiveClasses,
                    ]"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M9 17a2 2 0 100-4 2 2 0 000 4zm6 0a2 2 0 100-4 2 2 0 000 4z"
                        />
                        <path
                            d="M5 17h-2a1 1 0 01-1-1v-4l2-6h12l2 6v4a1 1 0 01-1 1h-2m-8 0h8"
                        />
                    </svg>
                    {{ $t("nav.manage_cars") }}
                </Link>
                <Link
                    v-if="!isStaff"
                    :href="route('rentals.index')"
                    :class="[
                        baseClasses,
                        route().current('rentals.*')
                            ? activeClasses
                            : inactiveClasses,
                    ]"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        />
                    </svg>
                    {{ $t("nav.manage_rentals") }}
                </Link>
                <Link
                    v-if="!isStaff"
                    :href="route('customers.index')"
                    :class="[
                        baseClasses,
                        route().current('customers.*')
                            ? activeClasses
                            : inactiveClasses,
                    ]"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path
                            d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
                        />
                    </svg>
                    {{ $t("nav.manage_customers") }}
                </Link>
                <Link
                    v-if="!isStaff"
                    :href="route('verifications.index')"
                    :class="[
                        baseClasses,
                        route().current('verifications.*')
                            ? activeClasses
                            : inactiveClasses,
                    ]"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                        />
                    </svg>
                    {{ $t("nav.manage_verifications") }}
                </Link>
                <Link
                    :href="route('payments.index')"
                    :class="[
                        baseClasses,
                        route().current('payments.*')
                            ? activeClasses
                            : inactiveClasses,
                    ]"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M3 10h18M7 15h2m3 0h2" />
                        <path
                            d="M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"
                        />
                    </svg>
                    {{ $t("nav.manage_payments") }}
                </Link>
            </nav>

            <div class="p-4 border-t border-white/10 space-y-1">
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-primary-100 hover:bg-surface-dark-2 hover:text-white transition-all duration-200"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M20 21a8 8 0 10-16 0" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    {{ $t("nav.profile") }}
                </Link>
                <Link
                    :href="`/`"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-primary-100 hover:bg-surface-dark-2 hover:text-white transition-all duration-200"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ $t("nav.back_to_site") }}
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-primary-500 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                    {{ $t("nav.log_out") }}
                </Link>
            </div>
        </aside>

        <div class="flex-1 min-w-0 transition-all duration-300 lg:ml-64">
            <header
                class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-primary-100/70 px-4 sm:px-6 lg:px-8 py-4"
            >
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <button
                            @click="isSidebarOpen = true"
                            class="p-2 rounded-xl text-muted-foreground hover:bg-primary-50 hover:text-muted-foreground focus:outline-hidden lg:hidden"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>

                        <div class="truncate">
                            <slot name="header-title">
                                <h1
                                    class="text-base sm:text-lg font-semibold text-foreground truncate"
                                >
                                    {{ $t("nav.dashboard") }}
                                </h1>
                            </slot>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <LanguageSwitcher />
                        <div class="flex items-center gap-2 sm:gap-3 pl-3">
                            <div
                                class="w-8 h-8 rounded-full bg-linear-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white text-xs font-bold shrink-0"
                            >
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <p
                                    class="text-sm font-medium text-foreground leading-tight"
                                >
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <p
                                    class="text-xs text-primary-500 max-w-[150px] truncate"
                                >
                                    {{ $page.props.auth.user.email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-4 sm:p-6 lg:p-8">
                <slot />
                <Toaster richColors position="top-center" closeButton />
            </div>
        </div>
    </div>
</template>
