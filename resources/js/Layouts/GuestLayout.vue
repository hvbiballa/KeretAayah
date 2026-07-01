<script setup>
import DropdownMenu from "@/Components/ui/dropdown-menu/DropdownMenu.vue";
import DropdownMenuContent from "@/Components/ui/dropdown-menu/DropdownMenuContent.vue";
import DropdownMenuGroup from "@/Components/ui/dropdown-menu/DropdownMenuGroup.vue";
import DropdownMenuItem from "@/Components/ui/dropdown-menu/DropdownMenuItem.vue";
import DropdownMenuLabel from "@/Components/ui/dropdown-menu/DropdownMenuLabel.vue";
import DropdownMenuTrigger from "@/Components/ui/dropdown-menu/DropdownMenuTrigger.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { toast, Toaster } from "vue-sonner";

const auth = usePage().props.auth;
const role = computed(() => auth?.user?.role);

const page = usePage();

// Watch for changes in Inertia's flash session data
watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;

        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
    },
    { deep: true, immediate: true },
);

const isMobileMenuOpen = ref(false);
const showStatusBar = ref(true);
const showActivityBar = ref(false);
const showPanel = ref(false);

const position = ref("bottom");
</script>

<template>
    <!-- Navbar -->
    <nav
        class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-primary-100/70"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <router-link to="/" class="flex items-center gap-2.5">
                    <div
                        class="w-9 h-9 rounded-xl overflow-hidden shadow-sm shadow-primary-500/20"
                    >
                        <img
                            src="/icon-white-red-bg.png"
                            alt="KeretaAyah"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <span
                        class="text-xl font-bold bg-gradient-to-r from-primary-500 to-primary-800 bg-clip-text text-transparent"
                        >KeretaAyah</span
                    >
                </router-link>

                <div class="hidden md:flex items-center gap-1">
                    <Link
                        href="/"
                        class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                        >{{ $t("nav.home") }}</Link
                    >
                    <Link
                        href="/about"
                        class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                        >{{ $t("nav.about") }}</Link
                    >
                    <Link
                        href="/cars"
                        class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                        >{{ $t("nav.rentals") }}</Link
                    >
                    <Link
                        href="/contact"
                        class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                        >{{ $t("nav.contact") }}</Link
                    >
                </div>

                <div class="hidden md:flex items-center gap-3">
                    <LanguageSwitcher />
                    <div
                        class="flex items-center gap-3"
                        v-if="$page.props.auth.user"
                    >
                        <Link
                            :href="route('dashboard')"
                            v-if="role === 'admin'"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                            >{{ $t("nav.dashboard") }}
                        </Link>
                        <Link
                            :href="route('customer.dashboard')"
                            v-if="role === 'customer'"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                        >
                            {{ $t("nav.dashboard") }}
                        </Link>
                        <Link
                            :href="route('payments.index')"
                            v-if="role === 'staff'"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                            >{{ $t("nav.payments") }}
                        </Link>
                        <Link
                            v-if="role === 'customer'"
                            :href="route('bookings.index')"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                />
                            </svg>
                            {{ $t("nav.my_bookings") }}
                        </Link>
                        <Link
                            v-if="role === 'customer'"
                            :href="`${route('profile.edit')}#verification`"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-muted-foreground hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200"
                        >
                            {{ $t("nav.verification") }}
                        </Link>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline">
                                    <div class="flex items-center gap-3 pl-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-linear-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white text-xs font-bold"
                                        >
                                            {{
                                                $page.props.auth.user.name.charAt(
                                                    0,
                                                )
                                            }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-foreground text-left"
                                            >
                                                {{ $page.props.auth.user.name }}
                                            </p>
                                            <p class="text-xs text-primary-500">
                                                {{
                                                    $page.props.auth.user.email
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-56" align="start">
                                <DropdownMenuLabel>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-linear-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white text-xs font-bold shrink-0"
                                        >
                                            {{
                                                $page.props.auth.user.name.charAt(
                                                    0,
                                                )
                                            }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-foreground"
                                            >
                                                {{ $page.props.auth.user.name }}
                                            </p>
                                            <p class="text-xs text-primary-500">
                                                {{
                                                    $page.props.auth.user.email
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuGroup>
                                    <DropdownMenuItem
                                        @click="router.visit(route('profile.edit'))"
                                    >
                                        {{ $t("nav.profile") }}
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click="router.post(route('logout'))"
                                    >
                                        {{ $t("nav.log_out") }}
                                    </DropdownMenuItem>
                                </DropdownMenuGroup>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>

                    <div class="flex items-center gap-3" v-else>
                        <Link
                            href="/login"
                            class="px-4 py-2 text-sm font-medium text-foreground hover:text-primary-600 transition-colors"
                            >{{ $t("nav.login") }}</Link
                        >
                        <Link
                            href="/register"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 rounded-xl hover:from-primary-600 hover:to-primary-800 shadow-md shadow-primary-500/25 transition-all duration-200 hover:shadow-lg hover:shadow-primary-500/30"
                            >{{ $t("nav.sign_up") }}</Link
                        >
                    </div>
                </div>

                <div class="flex items-center md:hidden">
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        type="button"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-muted-foreground hover:text-muted-foreground hover:bg-primary-50 focus:outline-hidden"
                        aria-controls="mobile-menu"
                        :aria-expanded="isMobileMenuOpen"
                    >
                        <span class="sr-only">{{ $t("nav.open_main_menu") }}</span>
                        <svg
                            v-if="!isMobileMenuOpen"
                            class="block h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                            />
                        </svg>
                        <svg
                            v-else
                            class="block h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div
            class="md:hidden border-t border-primary-100/70 bg-white"
            id="mobile-menu"
            v-show="isMobileMenuOpen"
        >
            <div class="pt-4 pb-4 px-4">
                <div v-if="$page.props.auth.user" class="space-y-3">
                    <div class="flex items-center gap-3 px-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-linear-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white text-sm font-bold shrink-0"
                        >
                            {{ $page.props.auth.user.name.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-foreground">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ $page.props.auth.user.email }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <Link
                            :href="route('dashboard')"
                            v-if="role === 'admin'"
                            class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                            >{{ $t("nav.dashboard") }}
                        </Link>
                        <Link
                            :href="route('customer.dashboard')"
                            v-if="role === 'customer'"
                            class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                            >{{ $t("nav.dashboard") }}
                        </Link>
                        <Link
                            :href="route('payments.index')"
                            v-if="role === 'staff'"
                            class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                            >{{ $t("nav.payments") }}
                        </Link>
                        <Link
                            v-if="role === 'customer'"
                            :href="route('bookings.index')"
                            class="flex items-center gap-2 px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
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
                            {{ $t("nav.my_bookings") }}
                        </Link>
                        <Link
                            v-if="role === 'customer'"
                            :href="`${route('profile.edit')}#verification`"
                            class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                        >
                            {{ $t("nav.verification") }}
                        </Link>
                        <Link
                            :href="route('profile.edit')"
                            class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                        >
                            {{ $t("nav.profile") }}
                        </Link>
                        <button
                            @click="router.post(route('logout'))"
                            class="w-full text-left block px-3 py-2.5 text-base font-medium text-rose-600 hover:bg-rose-50 rounded-xl transition-colors"
                        >
                            {{ $t("nav.log_out") }}
                        </button>
                    </div>
                </div>

                <div v-else class="grid grid-cols-2 gap-3">
                    <Link
                        href="/login"
                        class="block text-center px-4 py-2.5 text-sm font-medium text-foreground bg-primary-50 hover:bg-primary-100 rounded-xl transition-colors"
                        >{{ $t("nav.login") }}</Link
                    >
                    <Link
                        href="/register"
                        class="block text-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 hover:from-primary-600 hover:to-primary-800 rounded-xl text-center shadow-md shadow-primary-500/10 transition-all duration-200"
                        >{{ $t("nav.sign_up") }}</Link
                    >
                </div>
                <div class="mt-4 flex justify-center border-t border-primary-100/70 pt-4">
                    <LanguageSwitcher />
                </div>
                <div class="mt-4 space-y-1 border-t border-primary-100/70 pt-4">
                    <Link
                        href="/"
                        class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                        >{{ $t("nav.home") }}</Link
                    >
                    <Link
                        href="/about"
                        class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                        >{{ $t("nav.about") }}</Link
                    >
                    <Link
                        href="/cars"
                        class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                        >{{ $t("nav.rentals") }}</Link
                    >
                    <Link
                        href="/contact"
                        class="block px-3 py-2.5 text-base font-medium text-muted-foreground hover:text-primary-600 rounded-xl hover:bg-primary-50 transition-colors"
                        >{{ $t("nav.contact") }}</Link
                    >
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-16">
        <slot />
        <Toaster richColors position="top-center" closeButton />
    </main>

    <!-- Footer -->
    <footer class="bg-surface-dark text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Brand -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div
                            class="w-9 h-9 rounded-xl overflow-hidden shadow-sm shadow-primary-950/20"
                        >
                            <img
                                src="/icon-white-red-bg.png"
                                alt="KeretaAyah"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <span class="text-xl font-bold">KeretaAyah</span>
                    </div>
                    <p class="text-primary-500 text-sm leading-relaxed">
                        {{ $t("common.app_description") }}
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4
                        class="font-semibold mb-4 text-sm uppercase tracking-wider text-primary-100"
                    >
                        {{ $t("nav.quick_links") }}
                    </h4>
                    <ul class="space-y-2.5">
                        <li>
                            <router-link
                                to="/"
                                class="text-primary-500 hover:text-white text-sm transition-colors"
                                >{{ $t("nav.home") }}</router-link
                            >
                        </li>
                        <li>
                            <router-link
                                to="/about"
                                class="text-primary-500 hover:text-white text-sm transition-colors"
                                >{{ $t("nav.about") }}</router-link
                            >
                        </li>
                        <li>
                            <router-link
                                to="/cars"
                                class="text-primary-500 hover:text-white text-sm transition-colors"
                                >{{ $t("car.browse_available") }}</router-link
                            >
                        </li>
                        <li>
                            <router-link
                                to="/contact"
                                class="text-primary-500 hover:text-white text-sm transition-colors"
                                >{{ $t("nav.contact") }}</router-link
                            >
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4
                        class="font-semibold mb-4 text-sm uppercase tracking-wider text-primary-100"
                    >
                        {{ $t("nav.contact_info") }}
                    </h4>
                    <ul class="space-y-2.5">
                        <li
                            class="text-primary-500 text-sm flex items-start gap-2"
                        >
                            <svg
                                class="w-4 h-4 mt-0.5 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                />
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            UMPSA Pekan
                        </li>
                        <li
                            class="text-primary-500 text-sm flex items-center gap-2"
                        >
                            <svg
                                class="w-4 h-4 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                />
                            </svg>
                            +60199694913
+601166000438
                        </li>
                        <li
                            class="text-primary-500 text-sm flex items-center gap-2"
                        >
                            <svg
                                class="w-4 h-4 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                            info@keretaayah.myuni.agency
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-t border-white/10 mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center"
            >
                <p class="text-muted-foreground text-sm">
                    &copy; {{ $t("common.copyright") }}
                </p>
            </div>
        </div>
    </footer>
</template>
